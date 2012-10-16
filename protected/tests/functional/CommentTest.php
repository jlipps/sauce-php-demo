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
        $this->url('post/1/xyz');
        // verify the sample post title exists
        $this->assertTextPresent($this->f->posts['sample1']['title']);
        $field = $this->byName("Comment[author]");
        $this->assertNotEquals($field, NULL);
    }

    public function testValidation()
    {
        $this->url('post/1/xyz');
        // verify validation errors
        $this->byXPath("//input[@value='Submit']")->click();
        $this->assertTextPresent('Name cannot be blank.');
        $this->assertTextPresent('Email cannot be blank.');
        $this->assertTextPresent('Comment cannot be blank.');
    }

    public function testAdd()
    {
        $this->url('post/1/xyz');
        // verify commenting is successful
        $comment = "comment 1";
        $this->byName('Comment[author]')->value('me');
        $this->byName('Comment[email]')->value('me@example.com');
        $this->byName('Comment[content]')->value($comment);
        $this->byXPath("//input[@value='Submit']")->click();
        $this->assertTextPresent('Yii Blog Demo');

        $comments = Comment::model()->findAll();
        $this->assertEquals($comments[0]->attributes['content'], $comment);
    }
}

