<?php

interface KDF
{
    function compare($ptxt, $hash);
    function hash($ptxt);
}
