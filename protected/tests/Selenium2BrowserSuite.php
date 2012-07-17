<?php

class Selenium2BrowserSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Overriding the default: Selenium suites are always built from a TestCase class.
     * @var boolean
     */
    protected $testCase = false;

    public function __construct()
    {
        parent::__construct();
        $this->testCase = false;
    }

    public function addTestMethod(ReflectionClass $class, ReflectionMethod $method)
    {
        return parent::addTestMethod($class, $method);
    }

    public static function fromClassAndBrowser($className, array $browser)
    {
        $browserSuite = new self();
        $browserSuite->setName($className . ': ' . $browser['name']);
        return $browserSuite;
    }

    public function setupSpecificBrowser(array $browser)
    {
        $this->browserOnAllTests($this, $browser);
    }

    private function browserOnAllTests(PHPUnit_Framework_TestSuite $suite, array $browser)
    {
        foreach ($suite->tests() as $test) {
            if ($test instanceof PHPUnit_Framework_TestSuite) {
                $this->browserOnAllTests($test, $browser);
            } else {
                $test->setupSpecificBrowser($browser);
            }
        }
    }
}


