<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 */
define('TEST_BASE_URL','http://localhost/yiidemo/index-test.php/');

/**
 * The base class for functional test cases.
 * In this class, we set the base URL for the test application.
 * We also provide some common methods to be used by concrete test classes.
 */
class WebTestCase extends CWebTestCase
{

    // public static $browsers = array(
    //     array(
    //         'name' => 'Firefox on Mac',
    //         'browser' => '*firefox',
    //         'host' => 'localhost',
    //         'port' => 4444,
    //         'timeout' => 30000
    //     )
    // );
	/**
	 * Sets up before each test method runs.
	 * This mainly sets the base URL for the test application.
	 */
	protected function setUp()
	{
		parent::setUp();
        $this->setBrowser('*firefox');
		$this->setBrowserUrl(TEST_BASE_URL);
	}
}
