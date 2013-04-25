<?php

function Datalanche_autoload($className) {
    $filename = dirname(__FILE__) . '/lib/' . $className . '.php';
    // don't interfere with other classloaders
    if (!file_exists($filename)) {
        return;
    }
    return require_once($filename);
}

// register autoloader
spl_autoload_register('Datalanche_autoload');

?>
