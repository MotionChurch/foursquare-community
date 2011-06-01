<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once('config.inc.php');

require_once('Cif_Database.inc.php');

/* Set the timezone for PHP */
date_default_timezone_set('America/Los_Angeles');

/* Start Session */
session_start();

/* Helper functions */
$__DB = null;

function getDatabase() {
    global $CONFIG, $__DB;

    if ($__DB == null) {
        try {
          $__DB = new Cif_Database($CONFIG['dbhost'], $CONFIG['dbuser'],
             $CONFIG['dbpass'], $CONFIG['dbname']);
        
        } catch (Cif_Database_Exception $e) {
            die("Could not connect to database");
        }
    }

    return $__DB;
}

function __autoload($class) {
    require_once "$class.inc.php";
}

function buildUrl($tail='') {
    return 'http://' . $GLOBALS['CONFIG']['domain']
        . $GLOBALS['CONFIG']['urlroot'] . "/$tail";
}


?>
