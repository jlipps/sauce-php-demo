<?php

class SiteTest extends CWebDriverTestCase
{
    public function testContactFailsWithoutBody()
    {
        $this->url('site/contact');
        $this->assertTextPresent('Contact Us');
        $el = $this->byName('ContactForm[name]');
        $el->value('tester');
        $this->byName('ContactForm[email]')->value('tester@example.com');
        $this->byName('ContactForm[subject]')->value('test subject');
        $this->byXPath("//input[@value='Submit']")->click();
        $this->assertTextPresent('Body cannot be blank.');
    }

    protected function loginSetup()
    {
        $this->url('');
        // ensure the user is logged out
        if($this->isTextPresent('Logout'))
            $this->byLinkText('Logout')->click();
        $this->byLinkText('Login')->click();
    }

    public function testLoginDoesntWorkWithBlankPassword()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->byName('LoginForm[username]')->value('demo');
        $this->byXPath("//input[@value='Login']")->click();
        $this->assertTextPresent('Password cannot be blank.');
    }

    public function testLoginDoesntWorkWithBlankUsername()
    {
        $this->loginSetup();
        // test login process, including validation
        $this->byName('LoginForm[password]')->value('demo');
        $this->byXPath("//input[@value='Login']")->click();
        $this->assertTextPresent('Username cannot be blank.');
    }

    public function testLogin()
    {
        $this->loginSetup();
        $this->byName('LoginForm[username]')->value('demo');
        $this->byName('LoginForm[password]')->value('demo');
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
