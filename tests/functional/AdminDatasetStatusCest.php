<?php

/**
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional AdminDatasetStatusCest
 */
class AdminDatasetStatusCest
{
    public function tryToUploadToSubmittedStatusAndFail(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        $I->amOnPage('adminDataset/update/id/5');
        $I->selectOption('form select[id=Dataset_upload_status]', 'Submitted');
        $I->click('Save');

        $I->canSee('Fail to update status!');
    }

    public function tryToUploadStatusAndSucceed(FunctionalTester $I)
    {
        $I->cantSeeInDatabase('dataset', ['id' => 5, 'upload_status' => 'Submitted']);

        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        $I->amOnPage('adminDataset/update/id/5');
        $I->selectOption('form select[id=Dataset_upload_status]', 'DataAvailableForReview');
        $I->click('Save');

        $I->canSee('Updated successfully!');
    }
}
