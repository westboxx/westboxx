<?php

ob_start();

error_reporting(E_ALL);

require_once 'db.php';

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'de_DE', 'deu_deu');
date_default_timezone_set('Europe/Berlin');

db::addConnection(0, 'mysql', 'westboxx', '', 'westboxx_ch', 3306, '/var/run/mysqld/mysqld.sock');

function __autoload($class) {
    include_once dirname(__FILE__).'/modules/'.$class.'.php';
}

class dummy {
}

?>
