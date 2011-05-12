<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class User {
    private $info;


    public static function getById($id) {
        $where = "id='$id'";

        return User::getUser($where);
    }

    public static function getByEmail($email) {
        $where = "email='$email'";

        return User::getUser($where);
    }

    private static function getUser($where) {
        $query = "SELECT * FROM user WHERE $where";

        $db = getDatabase();
        
        $row = $db->fetchAssocRow($query);

        if ($row) {
            $user = new User();
            $user->info = $row;

            return $user;

        } else {
            return false;
        }
    }

    public function save() {
        $db = getDatabase();

        // TODO: Implement save 
    }

    public function getId() {
        return $this->info['id'];
    }

    public function getName() {
        return $this->info['name'];
    }

    public function getEmail() {
        return $this->info['email'];
    }

    public function setPassword($password) {
        $this->info['password'] = sha1($password);
    }

    public function authenticate($password) {
        return sha1($password) == $this->info['password'];
    }
}

?>
