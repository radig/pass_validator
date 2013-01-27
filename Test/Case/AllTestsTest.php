<?php
App::uses('CakeTestSuite', 'TestSuite');
class AllTestsTest extends CakeTestSuite
{
    public static function suite()
    {
        $suite = new CakeTestSuite('All PassValidator Tests');
        $suite->addTestDirectory(__DIR__ . '/Model/Behavior');

        return $suite;
    }
}
