<?php

require_once('PHPUnit/Runner/Version.php');


use WebDriver\WebDriver;

abstract class WebDriverTestCase extends PHPUnit_Framework_TestCase
{
    protected $wd_host = 'http://localhost';
    protected $wd_port = '4444';
    protected $wd_hub = '/wd/hub';
    protected $browser_name = 'firefox';
    protected $caps = array();

    public static $browsers = array();

    protected static $strategy_map = array(
        'css' => 'css selector',
        'class' => 'class name',
        'tag' => 'tag name',
        'link' => 'link text'
    );

    public function __construct(
        $name=NULL,
        $data=array(),
        $dataName='',
        $wd_host=false,
        $wd_port=false,
        $wd_hub=false,
        $browser_name=false,
        $caps=false
    )
    {
        parent::__construct($name, $data, $dataName);
        $this->setUpSpecificBrowser(array(
            'wd_host' => $wd_host,
            'wd_port' => $wd_port,
            'wd_hub' => $wd_hub,
            'name' => $browser_name,
            'caps' => $caps));
    }

    public function setUpSpecificBrowser($browser)
    {
        foreach (array('wd_host', 'wd_port', 'wd_hub', 'name', 'caps') as $k)
            if (!isset($browser[$k]))
                $browser[$k] = false;
        //print_r($browser);
        $this->wd_host = $browser['wd_host'] ?: $this->wd_host;
        $this->wd_port = $browser['wd_port'] ?: $this->wd_port;
        $this->wd_hub = $browser['wd_hub'] ?: $this->wd_hub;
        $this->browser_name = $browser['name'] ?: $this->browser_name;
        $this->caps = $browser['caps'] ?: $this->caps;
        $this->setUpDriver();
    }

    protected function setUpDriver()
    {
        $this->wd = new WebDriver($this->wd_host.':'.$this->wd_port.$this->wd_hub);
    }

    protected function setUp()
    {
        $this->sess = $this->wd->session($this->browser_name, $this->caps);
        $this->sess->timeouts()->implicit_wait(array('ms'=>5000));
    }

    public function tearDown()
    {
        $this->sess->close();
        //$this->stop();
    }

    public static function suite($className)
    {
        $suite = Selenium2TestSuite::fromTestCaseClass($className);
        return $suite;
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'elementBy') !== false || strpos($name, 'elBy') !== false) {
            $raw_strat = str_replace('elementBy', '', $name);
            $raw_strat = str_replace('elBy', '', $raw_strat);
            $strat = $this->strategyFromCamelCase($raw_strat);
            if (isset(self::$strategy_map[$strat])) {
                $strat = self::$strategy_map[$strat];
            }
            return $this->element($strat, $arguments[0]);
        } else {
            echo $name;
            return call_user_func_array(array($this->sess, $name), $arguments);
        }
    }

    public function run(PHPUnit_Framework_TestResult $result = NULL)
    {
        if ($result === NULL) {
            $result = $this->createResult();
        }

        parent::run($result);

        return $result;
    }

    private function strategyFromCamelCase($raw_strat)
    {
        $names = preg_split('/([[:upper:]][[:lower:]]+)/',
            $raw_strat, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        foreach ($names as $i => $name) {
            $names[$i] = strtolower($name);
        }
        return implode(' ', $names);
    }

    // WEBDRIVER HELPERS

    private function bodyText() {
        $body = $this->elementByTag('body');
        return $body->text();
    }

    protected function element($strat, $locator)
    {
        return $this->sess->element($strat, $locator);
    }

    protected function sendKeys($el, $str)
    {
        $value = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        return $el->postValue(array('value' => $value));
    }

    protected function isTextPresent($text)
    {
        return strpos($this->bodyText(), $text) !== false;
    }

    // POSITIVE ASSERTIONS

    protected function assertTextPresent($text)
    {
        $this->assertTrue($this->isTextPresent($text));
    }

    protected function assertElementPresent() {

    }

    // NEGATIVE ASSERTIONS

    protected function assertTextNotPresent($text)
    {
        $this->assertFalse($this->isTextPresent($text));
    }

}

