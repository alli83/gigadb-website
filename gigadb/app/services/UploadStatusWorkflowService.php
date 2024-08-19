<?php

declare(strict_types=1);

namespace GigaDB\services;

use GigaDB\models\Dataset;

class UploadStatusWorkflowService
{
    /**
     * Update a dataset's upload_status from one status to another
     *
     * If the fromStatus doesn't exist, it is noop and return false
     *
     * @param string      $fromStatus     upload status to transition from
     * @param string      $toStatus       upload status to transition to
     * @param string|null $identifier
     * @param string|null $previousStatus useful if dataset has already been updated
     *
     * @return bool whether the transition was enacted or not
     */
    public function transitionStatus(string $fromStatus, string $toStatus, ?string $identifier = null, ?string $previousStatus = null): bool
    {
        if (!$previousStatus) {
            //TODO if !$identifier => exception
            $dataset = Dataset::find()->where(['identifier' => $identifier])->one();
        }

        if ($fromStatus !== ($previousStatus ?: $dataset->upload_status)) {
            \Yii::log(sprintf('Failed to change status to %s', $toStatus), 'error');
            return false;
        }

        if (!$previousStatus) {
            $dataset->upload_status = $toStatus;

            return $dataset->save();
        }

        return true;
    }
}
