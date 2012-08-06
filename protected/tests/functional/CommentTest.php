<?php

class CommentTest extends CWebDriverTestCase
{
    /**
     * We use both 'Post' and 'Comment' fixtures.
     * @see CWebTestCase::fixtures
     */
    public $fixtures=array(
        'posts'=>'Post',
        'comments'=>'Comment',
    );

    public function setUp()
    {
        parent::setUp();
        $this->open('post/1/xyz');
    }

    public function testDisplay()
    {
        // verify the sample post title exists
        $this->waitForText($this->f->posts['sample1']['title']);
        $this->elementByName("Comment[author]");
    }

    public function testValidation()
    {
        // verify validation errors
        $this->elementByXpath("//input[@value='Submit']")->click();
        $this->waitForText('Name cannot be blank.');
        $this->waitForText('Email cannot be blank.');
        $this->waitForText('Comment cannot be blank.');
    }

    public function testAdd()
    {
       // verify commenting is successful
        $comment="comment 1";
        $this->sendKeys($this->elementByName('Comment[author]'), 'me');
        $this->sendKeys($this->elementByName('Comment[email]'), 'me@example.com');
        $this->sendKeys($this->elementByName('Comment[content]'), $comment);
        $this->elementByXpath("//input[@value='Submit']")->click();
        $this->waitForText('Yii Blog Demo');
        $comments=Comment::model()->findAll();
        $this->assertEquals($comments[0]->attributes['content'], $comment);
    }
}

