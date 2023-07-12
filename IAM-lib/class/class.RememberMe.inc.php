<?php

interface RememberMe
{
    function create($username);
    function remove($username);
    function hasValid();
}
