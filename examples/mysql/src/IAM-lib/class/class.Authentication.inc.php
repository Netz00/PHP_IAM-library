<?php

interface Authentication
{
    public function  set($userID);
    public function get();
    public function  clear();
}
