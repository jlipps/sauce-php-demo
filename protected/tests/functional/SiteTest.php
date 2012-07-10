<?php

class SiteTest extends SeleniumTestCase
{
    public function testContact()
    {
        $this->open('site/contact');
        $this->assertTextPresent('Contact Us');
        $el = $this->elementByName('ContactForm[name]');
        $this->sendKeys($el,'tester');
        $this->sendKeys($this->elementByName('ContactForm[email]'),'tester@example.com');
        $this->sendKeys($this->elementByName('ContactForm[subject]'),'test subject');
        $this->elementByXpath("//input[@value='Submit']")->click();
        $this->assertTextPresent('Body cannot be blank.');
    }

    public function testLoginLogout()
    {
        $this->open('');
        // ensure the user is logged out
        if($this->isTextPresent('Logout'))
            $this->elByLink('Logout')->click();

        // test login process, including validation
        $this->elByLink('Login')->click();
        $this->sendKeys($this->elByName('LoginForm[username]'), 'demo');
        $this->elByXpath("//input[@value='Login']")->click();
        $this->assertTextPresent('Password cannot be blank.');
        $this->sendKeys($this->elByName('LoginForm[password]'), 'demo');
        $this->elByXpath("//input[@value='Login']")->click();
        $this->assertTextNotPresent('Password cannot be blank.');
        $this->assertTextPresent('Logout');

        // test logout process
        $this->assertTextNotPresent('Login');
        $this->elByLink('Logout')->click();
        $this->assertTextPresent('Login');
    }
}
