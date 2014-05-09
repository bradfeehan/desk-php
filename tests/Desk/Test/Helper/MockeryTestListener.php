<?php

namespace Desk\Test\Helper;

use Exception;
use Mockery;
use PHP_CodeCoverage_Filter;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;

class MockeryTestListener
{

    /**
     * After each test, perform Mockery verification tasks and cleanup the
     * statically stored Mockery container for the next test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        try {
            $container = Mockery::getContainer();
            if ($container != null) {
                $expectation_count = $container->mockery_getExpectationCount();
                $test->addToAssertionCount($expectation_count);
            }
            Mockery::close();
        } catch (Exception $e) {
            $result = $test->getTestResultObject();
            $result->addError($test, $e, $time);
        }
    }

    /**
     * Add Mockery files to PHPUnit's blacklist so they don't showup on coverage reports
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (
            class_exists('\\PHP_CodeCoverage_Filter') &&
            method_exists('\\PHP_CodeCoverage_Filter', 'getInstance')
        ) {
            PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(
                __DIR__.'/../../../Mockery/',
                '.php',
                '',
                'PHPUNIT'
            );

            PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(
                __DIR__.'/../../../Mockery.php',
                'PHPUNIT'
            );
        }
    }

    /**
     *  The Listening methods below are not required for Mockery
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // do nothing
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        // do nothing
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // do nothing
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // do nothing
    }


    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        // do nothing
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        // do nothing
    }

    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // do nothing
    }
}
