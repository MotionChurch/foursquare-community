<?php
/* $Id: changepassword.php 134 2011-03-08 23:35:57Z jessemorgan $ */

require_once('../src/base.inc.php');

if (!isset($_SESSION['currentUser'])) {
    header('Location: ' . $CONFIG['siteroot'].'/admin/login.php');
    exit();
}

require_once('src/accounts.inc.php');

$form['errors'] = "";

if (count($_POST) > 0) {
    $errors = array();

    if (!isset($_POST['oldpassword']) or $_POST['oldpassword'] == '') {
        $errors[] = "Old Password is a required field.";
    }

    if (!isset($_POST['newpassword']) or $_POST['newpassword'] == '') {
        $errors[] = "New Password is a required field.";
    }

    if (!isset($_POST['newpassword2']) or $_POST['newpassword2'] == '') {
        $errors[] = "Confirm New Password is a required field.";
    }

    if (count($errors) == 0) {
        if ($_POST['newpassword'] != $_POST['newpassword2']) {
            $errors[] = "New password must match Confirm New Password";
        }
    
        $user = getAccount($_SESSION['currentUser']['id']);
        
        if (sha1($_POST['oldpassword']) != $user['password']) {
            $errors[] = "Old Password does not match your current password.";
        
        } else {
            // Update the password
            updatePassword($_SESSION['currentUser']['id'], $_POST['newpassword']);

            header("Location: index.php");
        }
    }
            
    
    if (count($errors) > 0) {
        $form['errors'] = "<ul><li>". implode("</li>\n<li>", $errors) ."</li></ul>";
    }
}

require_once('src/header.inc.php');

?>

<h2>Change Password</h2>

<?php
    echo $form['errors'];
?>

<form method="post">
<label>Old Password</label>
<div class="element">
    <input type="password" name="oldpassword" />
</div>

<label>New Password</label>
<div class="element">
    <input type="password" name="newpassword" />
</div>

<label>Confirm New Password</label>
<div class="element">
    <input type="password" name="newpassword2" />
</div>

<div class="buttons">
    <input type="submit" value="Change Password" />
</div>

<?php

require_once('src/footer.inc.php');

?>
