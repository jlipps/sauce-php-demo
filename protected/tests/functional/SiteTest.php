<?php

class SiteTest extends CWebDriverTestCase
{
    public function testContactFailsWithoutBody()
    {
        $this->open('site/contact');
        $this->assertTextPresent('Contact Us');
        $el = $this->byName('ContactForm[name]');
        $this->sendKeys($el, 'tester');
        $this->sendKeys($this->byName('ContactForm[email]'), 'tester@example.com');
        $this->sendKeys($this->byName('ContactForm[subject]'), 'test subject');
        $this->byXPath("//input[@value='Submit']")->click();
        $this->assertTextPresent('Body cannot be blank.');
    }

    protected function loginSetup()
    {
        $this->open('');
        // ensure the user is logged out
        if($this->isTextPresent('Logout'))
            $this->byLinkText('Logout')->click();
        $this->byLinkText('Login')->click();
    }

    public function testLoginDoesntWorkWithBlankPassword()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->sendKeys($this->byName('LoginForm[username]'), 'demo');
        $this->byXPath("//input[@value='Login']")->click();
        $this->assertTextPresent('Password cannot be blank.');
    }

    public function testLoginDoesntWorkWithBlankUsername()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->sendKeys($this->byName('LoginForm[password]'), 'demo');
        $this->byXPath("//input[@value='Login']")->click();
        $this->assertTextPresent('Username cannot be blank.');
    }

    public function testLogin()
    {
        $this->loginSetup();
        $this->sendKeys($this->byName('LoginForm[username]'), 'demo');
        $this->sendKeys($this->byName('LoginForm[password]'), 'demo');
        $this->byXPath("//input[@value='Login']")->click();
        $this->assertTextPresent('Logout');
    }

    public function testLogout()
    {
        $this->testLogin();
        $this->assertTextNotPresent('Login');
        $this->byLinkText('Logout')->click();
        $this->assertTextPresent('Login');
    }
}
