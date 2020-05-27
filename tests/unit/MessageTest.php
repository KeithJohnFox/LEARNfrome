<?php

class MessageTest extends \PHPUnit\Framework\TestCase {

    public function test_we_recieve_a_message_from_getMessage_function(){
        
        // \includes\classes\User;
        $userMessage = new \App\classes\User;
        
        $userMessage->setMessage('Hey how are you?');

        $this->assertEquals($userMessage->getMessage(), 'Hey how are you?');
        
    }

    public function test_getLastestMessage_function_returns_latest_message(){
        
        // \includes\classes\User;
        $latestMessage= new \App\classes\User;
        
        $latestMessage->setLatestMessage('This is the latest Message');
        
        $this->assertEquals($latestMessage->getLatestMessage(), 'This is the latest Message');
        
    }
}