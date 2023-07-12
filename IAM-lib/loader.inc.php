<?php

include_once('default-constants.inc.php');

// load user defined constants
foreach ($C as $name => $val)
    define($name, $val);

// load undefined default constants
foreach ($_C as $name => $val)
    if (!defined($name))
        define($name, $val);



//include all classes
spl_autoload_register(function ($class) {
    // if (file_exists('class/class.' . $class . '.inc.php'))
    include 'class/class.' . $class . '.inc.php';
});

session_start();
