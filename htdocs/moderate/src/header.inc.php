<?php
/* $Id: header.inc.php 151 2011-04-19 23:21:06Z jessemorgan $ */

if (!isset($SESSION['currentUser']['id'])) {
    if (isset($_POST['login_email']) and isset($_POST['login_password'])) {
        $db = getDatabase();

        $email = addslashes($_POST['login_email']);
        $password = sha1($_POST['password']);

        $query = "SELECT * FROM jpm_users WHERE `email`='$email' AND `password`='$password'";
        $result = $db->fetchAssocRow($query);

        if ($result) { 
            $SESSION['currentUser'] = $result;
        }
    
    }
}

?><!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?= $CONFIG['siteroot']?>/admin/admin.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('a.delete').click(function() {
                return confirm('Are you sure you want to delete this?');

            });

            $('a.delete img').hover(function() {
                    $(this).attr('src', '<?= $CONFIG['siteroot'] ?>/admin/images/delete.png');
                },
                function() {
                    $(this).attr('src', '<?= $CONFIG['siteroot'] ?>/admin/images/deletegray.png');
                });

        });
    </script>

</head>
<body>

<h1><a href="<?= $CONFIG['siteroot']?>/admin/index.php">Foursquare Admin Panel</a></h1>
<div id="nav">
    <h2>Navigation</h2>
    <ul>
        <li><a href="<?= $CONFIG['siteroot']?>/admin/online-campus">Online Services</a>
            <ul>
            <li><a href="<?= $CONFIG['siteroot']?>/admin/online-campus/attendance">Online Attendance</a></li>
            </ul>
        </li>

        <li><a href="<?= $CONFIG['siteroot']?>/troubleshoot.php">Troubleshooting Page</a></li>
        <li><a href="<?= $CONFIG['siteroot']?>/admin/accounts/">Accounts</a></li>
        <li><a href="<?= $CONFIG['siteroot']?>/admin/changepassword.php">Change Password</a></li>
        <li><a href="<?= $CONFIG['siteroot']?>/admin/login.php?logout">Logout</a></li>
    </ul>
</div>

<div id="content">
