#!/usr/bin/php
<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "../htdocs/src/base.inc.php";

$pi = new PostIterator();

$diff = $CONFIG['expiretime'] * 86400;

$pi->filterCreated(0, time() - $diff);
$pi->query();

$count = 0;
foreach ($pi as $post) {
    $post->delete();
    $count++;

}

// TODO: Add Logging?

?>
