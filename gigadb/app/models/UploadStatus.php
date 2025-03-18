<?php

declare(strict_types=1);

namespace GigaDB\models;

use Yii;
use yii\db\ActiveRecord;

class UploadStatus extends ActiveRecord
{

    public static function tableName()
    {
        return 'upload_status';
    }

    public function rules()
    {
        return [
            [['name', 'humanReadableName'], 'required'],
            [['name', 'humanReadableName'], 'string', 'max' => 255],
        ];
    }

    public static function getDb()
    {
        return Yii::$app->db;
    }
}
