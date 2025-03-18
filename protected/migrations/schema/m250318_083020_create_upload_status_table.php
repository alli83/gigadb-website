<?php

declare(strict_types=1);

class m250318_083020_create_upload_status_table extends CDbMigration
{
    public function safeUp()
    {
        if ($this->getDbConnection()->schema->getTable('upload_status') === null) {
            $this->createTable('upload_status', [
                'id' => 'pk',
                'name' => 'string NOT NULL',
                'humanReadableName' => 'string NOT NULL',
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropTable('upload_status');
    }
}
