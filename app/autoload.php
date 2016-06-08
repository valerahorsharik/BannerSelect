<?php

function __autoload($classname) {
    $ways = array(
        '/app/',
        '/models/',
        '/controllers/',
    );
    foreach ($ways as $way) {
        $path = ROOT.$way.$classname.'.php';
        if (is_file($path)){
            include $path;
        }
    }
}
