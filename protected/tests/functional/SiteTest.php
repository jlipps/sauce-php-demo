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

    protected function loginSetup()
    {
        $this->open('');
        // ensure the user is logged out
        if($this->isTextPresent('Logout'))
            $this->elByLink('Logout')->click();
        $this->elByLink('Login')->click();
    }

    public function testLoginDoesntWorkWithBlankPassword()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->sendKeys($this->elByName('LoginForm[username]'), 'demo');
        $this->elByXpath("//input[@value='Login']")->click();
        $this->assertTextPresent('Password cannot be blank.');
    }

    public function testLoginDoesntWorkWithBlankUsername()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->sendKeys($this->elByName('LoginForm[password]'), 'demo');
        $this->elByXpath("//input[@value='Login']")->click();
        $this->assertTextPresent('Username cannot be blank.');
    }

    public function testLogin()
    {
        $this->loginSetup();
        $this->sendKeys($this->elByName('LoginForm[username]'), 'demo');
        $this->sendKeys($this->elByName('LoginForm[password]'), 'demo');
        $this->elByXpath("//input[@value='Login']")->click();
        $this->assertTextPresent('Logout');
    }

    public function testLogout()
    {
        $this->testLogin();
        $this->assertTextNotPresent('Login');
        $this->elByLink('Logout')->click();
        $this->assertTextPresent('Login');
    }
}
