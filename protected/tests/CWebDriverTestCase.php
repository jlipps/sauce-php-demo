<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 */
define('TEST_BASE_URL','http://localhost/yiidemo/index-test.php/');

class CWebDriverTestCase extends \Sauce\Sausage\WebDriverTestCase
{

    protected $fixtures = false;
    protected $f = false;
    protected $base_url = TEST_BASE_URL;

    public static $browsers = array(
        array(
            'browserName' => 'firefox',
            'local' => true
        ),
        array(
            'browserName' => 'chrome',
            'local' => true
        ),
        //array(
            //'browserName' => 'chrome',
            //'desiredCapabilities' => array(
                //'platform' => 'VISTA',
                //'version' => ''
            //)
        //),
        //array(
            //'browserName' => 'iexplore',
            //'desiredCapabilities' => array(
                //'platform' => 'XP',
                //'version' => '6'
            //)
        //)
    );

    public function setUp()
    {
        parent::setUp();
        $this->f = new CWebFixture($this->fixtures);
    }

    protected function login($username)
    {
        $this->url('site/testLogin?u='.$username);
        $this->waitForText("Logged in $username");
    }
}

class CWebFixture
{
    protected $fixtures = false;
    protected $has_data = false;

    public function __construct($fixtures)
    {
        $this->fixtures = $fixtures;
        $this->manager = Yii::app()->getComponent('fixture');
        $this->load();
    }

    public function __get($name)
    {
        if($this->has_data && ($rows=$this->manager->getRows($name)) !== false)
            return $rows;
        else
            throw new Exception("No data for $name!");
    }

    public function __call($name, $params)
    {
        if($this->has_data &&
            isset($params[0]) &&
            ($record=$this->manager->getRecord($name, $params[0])) !== false)
            return $record;
        else
            throw new Exception("Got no active record for $name and those params");
    }

    public function load()
    {
        if(is_array($this->fixtures)) {
            $this->manager->load($this->fixtures);
            $this->has_data = true;
        }
    }

    protected function getFixtureData($name)
    {
        return $this->manager->getRows($name);
    }

    protected function getFixtureRecord($name, $alias)
    {
        return $this->manager->getRecord($name, $alias);
    }
}
