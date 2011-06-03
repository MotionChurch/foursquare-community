#!/usr/bin/php
<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once __DIR__ . "/../htdocs/src/base.inc.php";

// Read the email
$fd = fopen("php://stdin", "r");
$email = "";
while (!feof($fd)) {
    $email .= fread($fd, 1024);
}
fclose($fd);

// Parse the email
$headers = "";
$to = "";
$from = "";
$subject = "";
$message = "";

$splitmsg = split("\n", $email);

$inheaders = true;
foreach ($splitmsg as $line) {
    if ($inheaders) {
        // This is a header
        if ($line == '') {
            $inheaders = false;
        
        } else {
            $header = split(':', $line, 2);
        
            switch (strtolower(trim($header[0]))) {
                case 'from':
                    $from = $header[1];
                    break;

                case 'subject':
                    $subject = $header[1];
                    break;

                case 'delivered-to':
                    $to = $header[1];
                    break;

                default:
                    $headers .= "$line\n";
            }
        }

    } else {
        // Messsage line
        $message .= "$line\n";
    }
}


// Get the post id and post.
preg_match("/posting-(.+)@.+/", $to, $identifiers);

if (!isset($identifiers[1]) or !is_numeric($identifiers[1])) {
    mailFailure("Invalid id. '$to' " . print_r($identifiers, true));
}

$id = $identifiers[1];

$post = Post::getById($id);

if (!$post or $post->getStage() != 'approved') {
    mailFailure('Invalid post');
}

// Valid Post... forward the message.
$newsubject = "[" . $CONFIG['sitetitle'] . "] $subject";

if (mail($post->getEmail(), $newsubject, $message, $headers)) {
    exit(0);

} else {
    exit(2);
}

function mailFailure($message='') {
    echo "5.1.1 $message\n";
    exit(1);
}

?>
