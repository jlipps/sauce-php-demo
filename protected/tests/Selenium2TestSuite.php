<?php

if(getenv('WD_TARGET') == 'sauce')
    define('BROWSER_SUITE_PROP', 'browsers_sauce');
else
    define('BROWSER_SUITE_PROP', 'browsers');

class Selenium2TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Overriding the default: Selenium suites are always built from a TestCase class.
     * @var boolean
     */
    protected $testCase = TRUE;

    /**
     * Making the method public.
     */
    public function addTestMethod(ReflectionClass $class, ReflectionMethod $method)
    {
        return parent::addTestMethod($class, $method);
    }

    /**
     * @param string $className     extending PHPUnit_Extensions_SeleniumTestCase
     * @return PHPUnit_Extensions_SeleniumTestSuite
     */
    public static function fromTestCaseClass($className)
    {
        $suite = new self();
        $suite->setName($className);

        $class            = new ReflectionClass($className);
        $classGroups      = PHPUnit_Util_Test::getGroups($className);
        $staticProperties = $class->getStaticProperties();

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties[BROWSER_SUITE_PROP])) {
            foreach ($staticProperties[BROWSER_SUITE_PROP] as $browser) {
                $browserSuite = Selenium2BrowserSuite::fromClassAndBrowser($className, $browser);
                foreach ($class->getMethods() as $method) {
                    $browserSuite->addTestMethod($class, $method);
                }
                $browserSuite->setupSpecificBrowser($browser);

                $suite->addTest($browserSuite);
            }
        }
        else {
            // Create tests from test methods for single browser.
            foreach ($class->getMethods() as $method) {
                $suite->addTestMethod($class, $method);
            }
        }

        return $suite;
    }

    private static function addGeneratedTestTo(PHPUnit_Framework_TestSuite $suite, PHPUnit_Framework_TestCase $test, $classGroups)
    {
        list ($methodName, ) = explode(' ', $test->getName());
        $test->setDependencies(
              PHPUnit_Util_Test::getDependencies(get_class($test), $methodName)
        );
        $suite->addTest($test, $classGroups);
    }

}

