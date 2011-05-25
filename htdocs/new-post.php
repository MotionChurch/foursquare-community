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

echo "<h2>Submit Post</h2>";

// Process submission
if (isset($_POST['category'])) {
    $required = array(
        'title'       => 'Title',
        'description' => 'Description',
        'category'    => 'Category',
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

    if ($_POST['tos'] != '1') {
        $error .= "<p>You must accept the terms of service.</p>";
    }

    if ($error == '') {
        $post = new Post();

        $post->setEmail($values['email']);
        $post->setCategory($values['category']);
        $post->setName($values['title']);
        $post->setDescription($values['description']);

        // TODO: Set the source of the post.

        if ($post->save()) {
            $post->sendValidation();

            // TODO: Revise wording.
            echo "<p>Your posting is awaiting email verification</p>";

        } else {
            $error .= "An internal error has occured.";
        }

    } else {
        render_form($error);
    }

} else {
    render_form();
}

require_once "src/footer.inc.php";


function render_form($error="") {
    if ($error != '') {
        echo "<div class=\"error\">$error</div>";
    }

    echo "<form action=\"new-post.php\" method=\"post\">";
    echo "<p><label>Category: <select name=\"category\">";
    foreach (Category::getCategories() as $short => $name) {
        echo "<option name=\"$short\">$name</option>";
    }
    echo "</select></label</p>";

    echo "<p><label>Title: <input type=\"text\" name=\"title\" /></label></p>";

    echo "<p><label for=\"desc\">Description:</label></p>";
    echo "<p><textarea name=\"description\" id=\"desc\" rows=\"10\""
            . " cols=\"80\"></textarea></p>";

    echo "<p><label>Email Address: <input type=\"text\" name=\"email\" />"
            . "</label></p>";
    echo "<p><label>Confirm Email: <input type=\"text\" name=\"email2\" />"
            . "</label></p>";

    // TODO: Link to terms of service.
    echo "<p><label><input type=\"checkbox\" name=\"tos\" value=\"1\" />"
            ." I agree to the terms of service.</label></p>";

    // TODO: Allow picture uploads.

    echo "<p><input type=\"submit\" value=\"Submit\" /></p></form>";
}


?>
