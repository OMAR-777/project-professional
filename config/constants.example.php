<?php


if ($_SERVER['HTTP_HOST'] == 'localhost') { //responsive to host
    define('DB_HOST', 'localhost');
    define('DB_USER', 'DB_USER');
    define('DB_PASS', 'DB_PASS');
    define('DB_NAME', 'DB_NAME');
    define('PORT', 'PORT');
} else {

    //JAWSdb
    define('DB_HOST', 'DB_HOST');
    define('DB_USER', 'DB_USER');
    define('DB_PASS', 'DB_PASS');
    define('DB_NAME', 'DB_NAME');
    define('PORT', 'PORT');
}
