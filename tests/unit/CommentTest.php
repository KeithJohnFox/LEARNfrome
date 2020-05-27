<?php

class CommentReplyTest extends \PHPUnit\Framework\TestCase {

    public function test_a_comment_is_returned_from_get_Replies(){
        
        // \includes\classes\User;
        $userComment = new \App\classes\User;
        
        $userComment->setComment('I learned a lot from your tutorial!');

        $this->assertEquals($userComment->getReply(), 'I learned a lot from your tutorial!');
        
    }

}