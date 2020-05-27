<?php

class UserTest extends \PHPUnit\Framework\TestCase {

    public function testThatWeCanGetUsername(){
        
        // \includes\classes\User;
        $user = new \App\classes\User;
        
        $user->setUsername('keithfox12');

        $this->assertEquals($user->getUsername(), 'keithfox12');
        
    }

    public function testWeCanGetFirstAndLastName(){
        
        // \includes\classes\User;
        $user = new \App\classes\User;
        
        $user->setFirstAndLastName('John_Kelly');
        
        $this->assertEquals($user->getFirstAndLastName(), 'John_Kelly');
        
    }
}