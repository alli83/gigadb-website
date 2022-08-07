<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "manuscript".
 *
 * @property int $id
 * @property int|null $doi
 * @property string|null $manuscript_number
 * @property string|null $article_title
 * @property string|null $publication_date
 * @property string|null $editorial_status
 * @property string|null $editorial_status_date
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Manuscript extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manuscript';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doi', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['doi', 'created_at', 'updated_at'], 'integer'],
            [['publication_date', 'editorial_status_date'], 'safe'],
            [['manuscript_number', 'article_title', 'editorial_status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doi' => 'Doi',
            'manuscript_number' => 'Manuscript Number',
            'article_title' => 'Article Title',
            'publication_date' => 'Publication Date',
            'editorial_status' => 'Editorial Status',
            'editorial_status_date' => 'Editorial Status Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $emReportPath
     * @return array
     */
    public static function buildFromEmReport($emReportPath): array
    {
        $reportData = [];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($emReportPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $columnHeader = str_replace(' ', '_', array_map('strtolower', array_shift($sheetData)));
        foreach ($sheetData as $row) {
            $reportData[] = array_combine($columnHeader,$row);
        }
        return $reportData;
    }

    /**
     * @param $emReportPath
     * @return Manuscript
     */
    public static function saveManuscriptReport($emReportPath): Manuscript
    {
        $reportContent = self::buildFromEmReport($emReportPath);
        foreach ($reportContent as $content) {
            $manuscript = new Manuscript();
            $manuscript->manuscript_number = $content['manuscript_number'];
            $manuscript->article_title = $content['article_title'];
            $manuscript->editorial_status_date = $content['editorial_status_date'];
            $manuscript->editorial_status = $content['editorial_status'];
            $manuscript->save();
        }
        return $manuscript;
    }
}
