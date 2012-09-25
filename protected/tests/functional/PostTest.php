<?php

class PostTest extends CWebDriverTestCase
{
    /**
     * We use the 'Post' only for this test.
     * @see CWebTestCase::fixtures
     */
    public $fixtures=array(
        'posts'=>'Post',
    );

    public function testIndex()
    {
        $this->open('');
        // verify header title exists
        $this->assertTextPresent('Yii Blog Demo');
        // verify the sample post title exists
        $this->assertTextPresent($this->f->posts['sample1']['title']);
    }

    public function testView()
    {
        $this->open('post/1/xyz');
        // verify the sample post title exists
        $this->assertTextPresent($this->f->posts['sample1']['title']);
        // verify comment form exists
        $this->assertTextPresent('Leave a Comment');
    }

    public function testUpdateTitle()
    {
        $new_body = "This is a new title!";
        $this->login('demo');
        $this->open('post/update?id=1');
        $el = $this->byName('Post[title]');
        $el->clear();
        $this->sendKeys($el, $new_body);
        $this->byXPath("//input[@value='Save']")->click();
        $this->assertTextPresent($new_body);
        $this->assertTextNotPresent($this->f->posts['sample1']['title']);
    }

    public function testUpdateBody()
    {
        $new_body = "This is a new body!";
        $this->login('demo');
        $this->open('post/update?id=1');
        $el = $this->byName('Post[content]');
        $el->clear();
        $this->sendKeys($el, $new_body);
        $this->byXPath("//input[@value='Save']")->click();
        $this->assertTextPresent($new_body);
        $this->assertTextNotPresent($this->f->posts['sample1']['content']);
    }

}
