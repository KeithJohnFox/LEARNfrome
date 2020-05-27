<?php
namespace App\classes;



class User
{
    private $user;  
    private $con;   
    private $name;
    private $userMessage; 
    private $latestMessage; 
    private $userComment; 

    public function setUsername($user){
        $this->username = $user;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setFirstAndLastName($user)
    {
        $this->name = $user;
    }

    public function getFirstAndLastName()
    {
        return $this->name;
    }

    public function setMessage($userMessage){
        $this->message = $userMessage;
    }

    public function getMessage(){
        return $this->message;
    }

    public function setLatestMessage($latestMessage){
        $this->message = $latestMessage;
    }

    public function getLatestMessage(){
        return $this->message;
    }

    //Gets username from user logged in
    public function setComment($userComment){
        $this->comment = $userComment;
    }

    public function getReply(){
        return $this->comment;
    }

}