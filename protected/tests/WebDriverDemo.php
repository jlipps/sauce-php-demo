<?php

class WebDriverDemo extends PHPUnit_Extensions_Selenium2TestCase
{

    function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost/yiidemo/');
    }

    function testLoginLogout()
    {
        $this->url('http://localhost/yiidemo/');
        $this->assertEquals("Yii Blog Demo - Post", $this->title());

        $this->byLinkText('Login')->click();

        $this->byName('LoginForm[username]')->value('demo');
        $this->byName('LoginForm[password]')->value('demo');
        $this->byName('yt0')->click();

        $page_text = $this->byCssSelector('body')->text();
        $this->assertContains("Logout", $page_text);

        $this->byLinkText('Logout (demo)')->click();
        $page_text = $this->byCssSelector('body')->text();
        $this->assertContains("Login", $page_text);
    }
}

