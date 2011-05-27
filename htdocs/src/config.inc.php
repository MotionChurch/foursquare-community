<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

$CONFIG = array(
    // Database Information
    'dbhost' => '127.0.0.1',
    'dbuser' => 'p4scommunity',
    'dbpass' => 'password',
    'dbname' => 'p4scommunity',

    // Site Information
    'sitetitle'  => 'Foursquare Community',
    'email_from' => 'community@myfoursquarechurch.com',

    'urlroot'   => '/~jesse/p4s/community/htdocs',
    'root'      => '/home/jesse/Development/p4square/community/htdocs',
    'uploads'   => '/home/jesse/Development/p4square/community/uploads',

    'debug'      => true,
    'production' => true,
);

set_include_path(get_include_path() . PATH_SEPARATOR . $CONFIG['root'].'/src');

?>
