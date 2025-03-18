<?php

declare(strict_types=1);

class m250318_083845_insert_upload_status_values extends CDbMigration
{
    public function safeUp()
    {
        $values = [
            ['ImportFromEM', 'ImportFromEM'],
            ['UserStartedIncomplete', 'UserStartedIncomplete'],
            ['Rejected', 'Rejected'],
            ['Not required', 'Not required'],
            ['Submitted', 'Submitted'],
            ['Curation', 'Curation'],
            ['AuthorReview', 'AuthorReview'],
            ['Private', 'Private'],
            ['Published', 'Published'],
            ['AssigningFTPbox', 'AssigningFTPbox'],
            ['UserUploadingData', 'UserUploadingData'],
            ['DataAvailableForReview', 'DataAvailableForReview'],
            ['DataPending', 'DataPending'],
        ];

        foreach ($values as $value) {
            $this->insert('upload_status', [
                'name' => $value[0],
                'humanReadableName' => $value[1],
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('upload_status', [
            'name' => [
                'ImportFromEM',
                'UserStartedIncomplete',
                'Rejected',
                'Not required',
                'Submitted',
                'Curation',
                'AuthorReview',
                'Private',
                'Published',
                'AssigningFTPbox',
                'UserUploadingData',
                'DataAvailableForReview',
                'DataPending'
            ]
        ]);
    }
}
