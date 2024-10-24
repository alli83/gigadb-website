<?php

declare(strict_types=1);

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use yii\base\Component;

/**
 * DatasetFilesUpdater
 *
 * encapsulate business logic for updating the file table
 */
final class DatasetFilesUpdater extends Component
{
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;
    /**
     * @var URLsService URLs helper functions (here we interested in batch grab of specific response header)
     */
    public URLsService $us;
    /**
     * @var \GuzzleHttp\Client web client needed for URLsService
     */
    public \GuzzleHttp\Client $webClient;

    /**
     * @var string URLs helper functions (here we interested in batch grab of specific response header)
     */
    public const GIGADB_DATASETS_METADATA_BUCKET_URL = 'https://s3.ap-northeast-1.amazonaws.com/gigadb-datasets-metadata';

    /**
     * Updates sizes for all files listed in doi.filesizes file located in 
     * gigadb-datasets-metadata S3 bucket.
     *
     * @return int returns the number of files that has been successfully updated
     * @throws Exception
     */
    public function updateFileSizes(): int
    {
        $success = 0;
        $d = Dataset::find()->where(['identifier' => $this->doi])->one();

        $filesizesURL = DatasetFilesUpdater::GIGADB_DATASETS_METADATA_BUCKET_URL . '/' . $this->doi . '.filesizes';
        $array = get_headers($filesizesURL);
        if(!strpos($array[0],'200 OK')) {
            throw new Exception("$filesizesURL not found");
        }

        $content = file_get_contents($filesizesURL);
        $lines = explode("\n", $content);
        foreach($lines as $line) {
            # Last line in .filesizes file might be empty
            if(!str_contains($line, "\t")) {
                break;
            }
            $tokens = explode("\t", $line);
            $size = (int)$tokens[0];
            $filename = ltrim($tokens[1], './');
            $filename = $this->doi . '/' . $filename;
            
            # Find file id for file to be updated
            $result = (new \yii\db\Query())
                ->select('id')
                ->from('file')
                ->where(['like', 'location', $filename])
                ->one();
            if($result) {
                # Update file size
                $f = File::find()->where(['id' => $result['id'], 'dataset_id' => $d->id])->one();
                $f->size = $size;
                if ($f->save()) {
                    $success++;
                }
            }
        }
        return $success;
    }

    /**
     * Method to update the file size for the all the files of the dataset identified with $doi
     *
     * @return int returns the number of files that has been successfully updated
     */
    public function updateFileSize(): int
    {
        $success = 0;
        $zeroOutRedirectsAndDirectories = function ($response, $url) {
            if (301 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return 0;
            }
            return null;
        };


        $d = Dataset::find()->where(["identifier" => $this->doi])->one();

        $urls =  File::find()
                ->select(["location"])
                ->where(["dataset_id" => $d->id])
                ->asArray(true)
                ->all();
        $values = function ($item) {
            return $item["location"];
        };
        $flatURLs = array_map($values, $urls);
        $this->us->urls = $flatURLs;
        $contentLengthList = $this->us->fetchResponseHeader(
            "Content-Length",
            $this->webClient,
            $zeroOutRedirectsAndDirectories
        );
        foreach ($contentLengthList as $location => $contentLength) {
            $f = File::find()->where(["location" => $location])->one();
            $f->size = (int) $contentLength;
            if ($f->save()) {
                $success++;
            }
        }

        return $success;
    }
}
