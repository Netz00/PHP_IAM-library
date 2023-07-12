<?php

class User
{
    public $username = null;
    public $id = null;


    public function __construct($id, $username)
    {
        $this->username = $username;
        $this->id = $id;
    }
}
