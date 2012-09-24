<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 */
define('TEST_BASE_URL','http://localhost/yiidemo/index-test.php');
define('SAUCE_HOST', getenv('SAUCE_USERNAME').':'.getenv('SAUCE_ACCESS_KEY').'@ondemand.saucelabs.com');

class SeleniumInfo
{
    public static function Host()
    {
        return getenv('SAUCE') ? SAUCE_HOST : 'localhost';
    }

    public static function Port()
    {
        return getenv('SAUCE') ? 80 : 4444;
    }

    public static function SessionStrategy()
    {
        return getenv('SAUCE') ? 'isolated' : 'shared';
    }

    public static function __callStatic($name, $arguments)
    {
        echo $name."\n".$arguments."\n";
    }
}

SeleniumInfo::$browsers;

class CWebDriverTestCase extends PHPUnit_Extensions_Selenium2TestCase
{

    protected $fixtures = false;
    protected $f = false;

    public static $browsers = array(
        array(
            'name' => 'firefox',
            'sessionStrategy' => 'isolated'
        ),
        array(
            'name' => 'chrome',
            'sessionStrategy' => 'isolated'
        ),
    );

    public static $browsers_sauce = array(
        array(
            'name' => 'firefox',
            'sauce' => true,
            'caps' => array(
                'platform' => 'Windows 2008',
                'version' => '13'
            )
        ),
        array(
            'name' => 'chrome',
            'sauce' => true,
            'caps' => array(
                'platform' => 'Windows 2008',
                'version' => ''
            )
        ),
        array(
            'name' => 'internet explorer',
            'sauce' => true,
            'caps' => array(
                'platform' => 'Windows 2003',
                'version' => '6'
            )
        )
    );

    protected function setUp()
    {
        parent::setUp();
        $this->f = new CWebFixture($this->fixtures);
    }

    protected function open($url)
    {
        if (strpos($url, TEST_BASE_URL) === false) {
            $url = TEST_BASE_URL.'/'.$url;
        }
        return $this->sess->open($url);
    }

    protected function login($username)
    {
        $this->open('site/testLogin?u='.$username);
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
