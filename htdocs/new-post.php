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

const MAX_IMAGE_UPLOADS = 4;

echo "<h2>New Posting</h2>";

/*
 * Stages:
 * 1) Pick a category
 * 2) ToS
 * 3) Title, Desc, Location, email
 * 4) Images
 * 5) Source
 * 6) Notify about email
 * 
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

    case 'source':
        if (finish_images())
            handle_source();
        break;

    case 'finish':
        if (finish_source())
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
    $cats = Category::getCategories();
    echo "<dl>";
    foreach ($cats as $short => $cat) {
        $url = $GLOBALS['CONFIG']['urlroot']
            . "/new-post.php?stage=tos&category=$short";

        echo "<dt><a href=\"$url\">". $cat->getName() ."</a></dt>";
        echo "<dd>". $cat->getDescription() ."</dd>";
    }
    echo "</dl>";
}

function finish_category() {
    $post = $_SESSION['newpost'];

    if (isset($_GET['category'])) {
        $category = Category::getByShortname(addslashes($_GET['category']));
        if ($category) {
            $post->setCategory($category);
            return true;
        }
    }

    handle_category();
    return false;
}

function handle_tos() {
    // Display ToS
    form_start('post');

    echo "<p><label><input type=\"checkbox\" name=\"tos\" value=\"1\" />"
            ." I agree to the <a href=\"". buildUrl('page/tou.html')
            ."\">terms of use</a>.</label></p>";

    form_end();
}

function finish_tos() {
    if (isset($_REQUEST['tos']) and $_REQUEST['tos'] == 1) {
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
        'location'    => 'Location',
        'description' => 'Description',
        'email'       => 'Email Address',
        'email2'      => 'Confirm Email Address',
        );

    $error = '';
    $values = array('title' => '', 'description' => '',
        'email' => '', 'email2' => '');
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
        $post->setLocation($values['location']);
        
        // Price is optional
        if (isset($_POST['price'])) {
            $post->setPrice($_POST['price']);
        }

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

    form_start('source');

    for ($i = 1; $i <= MAX_IMAGE_UPLOADS; $i++) {
        echo "<p><label>Image $i: "
            . "<input type=\"file\" name=\"images[]\" /></label></p>";
    }

    form_end();
}

function finish_images() {
    $post = $_SESSION['newpost'];

    if (isset($_FILES['images'])) {
        foreach ($_FILES["images"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $post->addImage($_FILES['images']['tmp_name'][$key]);
            }
        }
    }

    return true;
}

function handle_source($error='') {
    form_start('finish');

    if ($error != '') {
        echo "<div class=\"errorbox\">$error</div>";
    }

    echo "<p>Where did you hear about this site?</p>";

    echo "<select name=\"source\">";

    foreach(Source::getSources() as $source) {
        echo "<option value=\"". $source->getId() ."\">"
            . $source->getName() ."</option>";
    }

    echo "<option value=\"0\">Other</option>";
    echo "</select>";

    echo "<p>If you selecte other, please explain:";
    echo " <input type=\"text\" name=\"explain\" /></p>";

    form_end();
}

function finish_source() {
    $error = '<p>This question is required.</p>';

    $post = $_SESSION['newpost'];
    if (isset($_POST['source']) and is_numeric($_POST['source'])) {
        if ($_POST['source'] == 0) {
            if (isset($_POST['explain']) and trim($_POST['explain']) != '') {
                $post->otherSource($_POST['explain']);
                $post->save();
                return true;

            } else {
                $error = '<p>Explaination is a required field.</p>';
            }

        } else {
            $source = Source::getById($_POST['source']);

            if ($source) {
                $post->setSource($_POST['source']);
                $post->save();
                return true;
            }
        }

    }

    handle_source($error);
    return false;
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
    $category = $_SESSION['newpost']->getCategory();

    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $email2 = isset($_POST['email2']) ? $_POST['email2'] : '';

    if ($error != '') {
        echo "<div class=\"errorbox\">$error</div>";
    }

    echo "<p><label>Title: <input type=\"text\" name=\"title\" value=\"$title\" /></label>";
    
    if ($category->getOption('price')) {
        echo " <label>Price: $<input type=\"text\" name=\"price\" value=\"$price\" /></label>";
    }

    
    echo "</p>";

    echo "<p><label>Location: <input type=\"text\" name=\"location\" value=\"$location\" /></label></p>";

    echo "<p><label for=\"desc\">Description:</label></p>";
    echo "<p><textarea name=\"description\" id=\"desc\" rows=\"10\""
            . " cols=\"80\">$description</textarea></p>";

    echo "<p><label>Email Address: <input type=\"text\" name=\"email\" value=\"$email\" />"
            . "</label>";
    echo " <label>Confirm Email: <input type=\"text\" name=\"email2\" value=\"$email2\" />"
            . "</label></p>"
            . "<p>Your email address will only be visible to our moderators.</p>";


}


?>
