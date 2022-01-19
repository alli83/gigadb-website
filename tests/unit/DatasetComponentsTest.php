<?php

/**
 * Class DatasetComponentsTest
 */
class DatasetComponentsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    /**
     * Test that current time is used as invalidation query when cached is set to disabled
     * Test that DISABLE_CACHE contant is acted upon
     *
     * @throws Exception
     */
    public function testSaveToCacheWhenDisabled()
    {
        $mockCache = $this->makeEmpty('CCache', [ 'set' => true]);
        $mockCacheDep = $this->makeEmpty('CDbCacheDependency');
        $mockDatasetFiles = $this->makeEmpty('DatasetFilesInterface');

        //DatasetComponents is the class we want to test but it's an abstract class,
        //so instead we test a concrete subclass. We only need to test one.
        $component = new CachedDatasetFiles($mockCache, $mockCacheDep,$mockDatasetFiles);

        define('DISABLE_CACHE',true);
        $result = $component->saveLocaldataInCache("100001","hello world");
        $this->assertEquals('select current_time;',$mockCacheDep->sql);
    }

    /**
     * Test that current time is not used as invalidation query if cache is not disabled
     * Test that the method ``isCachedDisabled`` is called by partially mocking the system under test
     * (also acts as test proxy for the constant DISABLE_CACHE as constants can only be defined once in a file)
     *
     * @throws Exception
     */
    public function testSaveToCacheWhenEnabled()
    {
        $mockCache = $this->makeEmpty('CCache', [ 'set' => true]);
        $mockCacheDep = $this->makeEmpty('CDbCacheDependency');
        $mockDatasetFiles = $this->makeEmpty('DatasetFilesInterface');

        //Since we need to set an expectation on a system under test's method
        //we need to "construct" a partial mock instead of just instantiating the system under test
        $component = $this->construct('CachedDatasetFiles', [
            '_cache' => $mockCache,
            '_cacheDependency' =>  $mockCacheDep,
            '_storedDatasetFiles' => $mockDatasetFiles,
        ],[
            'isCachedDisabled' => function () { return false;},
        ]);

        $result = $component->saveLocaldataInCache("100001","hello world");
        $this->assertNotEquals('select current_time;',$mockCacheDep->sql);
    }
}