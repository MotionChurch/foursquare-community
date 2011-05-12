<?php
/* $Id: accounts.inc.php 134 2011-03-08 23:35:57Z jessemorgan $ */

function getAccount($id) {
    $query = "SELECT * FROM jpm_users WHERE"
        . "`id`='$id' OR `email`='$id'";
    
    $db = getDatabase();

    $results = array();

    try {
        $results = $db->fetchAssocRow($query);

    } catch (Cif_Database_Exception $e) {
        $results = false;
    }

    return $results;
}

function updatePassword($id, $password) {
    $db = getDatabase();

    $row['password'] = sha1($password);
    
    $db->update('jpm_users', $row, "WHERE `id`='$id'");
}

function getAccounts($s) {
    $query = "SELECT * FROM jpm_users";
    
    if (!is_null($s)) {
        $s = addslashes($s);
        $query .= " WHERE name LIKE '%$s%' OR email LIKE '%$s%'";
    }

    $query .= " ORDER BY name";
    
    $db = getDatabase();

    $results = array();

    try {
        $results = $db->fetchAssocRows($query);

    } catch (Cif_Database_Exception $e) {
        $results = array();
    }

    return $results;
}

function generatePassword() {
    $alphabet = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz123456789!@#$%*()";
    $length = strlen($alphabet);

    $password = '';
    for ($i = 0; $i < 8; $i++) {
        $pos = rand(0, $length - 1);
        $password .= substr($alphabet, $pos, 1); 
    }

    return $password;
}

?>
