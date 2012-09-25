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

    public function testDisplay()
    {
        $this->open('post/1/xyz');
        // verify the sample post title exists
        $this->waitForText($this->f->posts['sample1']['title']);
        $this->byName("Comment[author]");
    }

    public function testValidation()
    {
        $this->open('post/1/xyz');
        // verify validation errors
        $this->byXPath("//input[@value='Submit']")->click();
        $this->waitForText('Name cannot be blank.');
        $this->waitForText('Email cannot be blank.');
        $this->waitForText('Comment cannot be blank.');
    }

    public function testAdd()
    {
        $this->open('post/1/xyz');
        // verify commenting is successful
        $comment="comment 1";
        $this->byName('Comment[author]')->value('me');
        $this->byName('Comment[email]')->value('me@example.com');
        $this->byName('Comment[content]')->value($comment);
        $this->byXPath("//input[@value='Submit']")->click();
        $this->waitForText('Yii Blog Demo');
        $comments=Comment::model()->findAll();
        $this->assertEquals($comments[0]->attributes['content'], $comment);
    }
}

