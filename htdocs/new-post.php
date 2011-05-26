<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "src/base.inc.php";

require_once "src/header.inc.php";

echo "<h2>New Posting</h2>";

/*
 * Stages:
 * 1) Pick a category
 * 2) ToS
 * 3) Title, Desc, Location, email
 * 4) Images
 * 5) Notify about email
 * 
 * TODO: Set the source of the post.
 */

$stage = 'category';

if (isset($_GET['stage'])) {
    $stage = trim($_GET['stage']);
}

if (!isset($_SESSION['newpost'])) {
    $stage = 'category';
}

switch ($stage) {
    case 'category':
        handle_category();
        break;

    case 'tos':
        if (finish_category()) 
            handle_tos();
        break;

    case 'post':
        if (finish_tos())
            handle_post();
        break;

    case 'images':
        if (finish_post())
            handle_images();
        break;

    case 'finish':
        if (finish_images())
            handle_finish();
        break;

    default:
        // Category
        handle_category();
}

/* Stage Handlers */

function handle_category() {
    $_SESSION['newpost'] = new Post();

    // Display instructions
    echo "<p>Start by choosing a category from the list below</p>";

    // List Categories
    foreach (Category::getCategories() as $short => $name) {
        echo "<p><a href=\"". $GLOBALS['CONFIG']['urlroot']
            . "/new-post.php?stage=tos&category=$short\">$name</a></p>";
    }
    
}

function finish_category() {
    $post = $_SESSION['newpost'];

    if (isset($_GET['category'])) {
        $category = Category::getByShortname(addslashes($_GET['category']));
        if ($category) {
            $post->setCategory($category->getId());
            return true;
        }
    }

    handle_category();
    return false;
}

function handle_tos() {
    // Display ToS
    // TODO: Display ToS Here

    form_start('post');

    echo "<p><label><input type=\"checkbox\" name=\"tos\" value=\"1\" />"
            ." I agree to the terms of service.</label></p>";

    form_end();
}

function finish_tos() {
    if (isset($_POST['tos']) and $_POST['tos'] == 1) {
       return true;

    } else {
        header('Location: ' . $GLOBALS['CONFIG']['urlroot']);
        exit;
    }
}

function handle_post($error='') {
    // Display Form
    form_start('images');
    render_form($error);
    form_end();
}

function finish_post() {
    $post = $_SESSION['newpost'];
    
    $required = array(
        'title'       => 'Title',
        'description' => 'Description',
        'email'       => 'Email Address',
        'email2'      => 'Confirm Email Address',
        );

    $error = '';
    $values = array();
    foreach ($required as $field => $desc) {
        if (!isset($_POST[$field]) or trim($_POST[$field]) == '') { 
            $error .= "<p>$desc is a required field.</p>";
        
        } else {
            $values[$field] = trim($_POST[$field]);
        }
    }

    if ($values['email'] != $values['email2']) {
        $error .= "<p>Email addresses must match.</p>";
    }

    if ($error == '') {
        $post->setEmail($values['email']);
        $post->setName($values['title']);
        $post->setDescription($values['description']);

        if ($post->save()) {
            return true;

        } else {
            $error .= 'An internal error has occured.';
        }
    }

    handle_post($error);
    return false;
}

function handle_images() {
    $post = $_SESSION['newpost'];

    // Display image form
    echo "<p>You may upload up to four images with your post.</p>";

    form_start('finish');

    for ($i = 1; $i <= 4; $i++) {
        echo "<p><label>Image $i: "
            . "<input type=\"file\" name=\"images[]\" /></label></p>";
    }

    form_end();
}

function finish_images() {
    $post = $_SESSION['newpost'];

    return true;
}

function handle_finish() {
    $post = $_SESSION['newpost'];

    // Send validation email.
    $post->sendValidation();

    // Display confirmation message
    echo "<p>Your posting is almost complete. You must verify your email address by visiting the link we have emailed you, then your posting will be reviewed by our moderation team.</p>";
}


function form_start($stage) {
    echo "<form action=\"". $GLOBALS['CONFIG']['urlroot'] ."/new-post.php?stage=$stage\""
            ." method=\"post\" enctype=\"multipart/form-data\">";
}

function form_end() {
    echo "<p><input type=\"submit\" value=\"Next &gt;\" /></p></form>";
}



require_once "src/footer.inc.php";


function render_form($error="") {
    global $values;

    if ($error != '') {
        echo "<div class=\"errorbox\">$error</div>";
    }

    echo "<p><label>Title: <input type=\"text\" name=\"title\" value=\"${_POST[title]}\" /></label></p>";

    echo "<p><label for=\"desc\">Description:</label></p>";
    echo "<p><textarea name=\"description\" id=\"desc\" rows=\"10\""
            . " cols=\"80\">${_POST[description]}</textarea></p>";

    echo "<p><label>Email Address: <input type=\"text\" name=\"email\" value=\"${_POST[email]}\" />"
            . "</label>";
    echo " <label>Confirm Email: <input type=\"text\" name=\"email2\" value=\"${_POST[email2]}\" />"
            . "</label></p>"
            . "<p>Your email address will only be visible to our moderators.</p>";


}


?>
