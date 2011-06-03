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
    const GENERATED_PASSSWORD_LENGTH = 12;

    private $info;
    private $indatabase;

    public function __construct($info=null) {
        $this->info = is_null($info) ? array() : $info;

        if ($info !== null and isset($info['id'])) {
            $this->indatabase = true;

        } else {
            $this->indatabase = false;
        }
    }
    
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
            $user->indatabase = true;

            return $user;

        } else {
            return false;
        }
    }

    public function save() {
        $db = getDatabase();

        // Cleanup Info
        foreach ($this->info as $key=>$value)
            $info[$key] = addslashes($value);

        // Save or create?
        if ($this->indatabase) {
            try {
                $db->update('user', $info, "WHERE `id`='"
                    . $this->getId() ."'");
                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }

        } else {
            // Creating... set special fields.
            try {
                $ret = $db->insert('user', $info);

                if ($ret) {
                    $this->info['id'] = $ret;
                    $this->indatabase = true;
                    $this->sendNewPassword();
                }

                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }
        }
    }

    public function delete() {
        $db = getDatabase();

        $db->delete('user', 'id=' . $this->getId());

        $this->indatabase = false;
    }

    public function getId() {
        return $this->info['id'];
    }

    public function getName() {
        return $this->info['name'];
    }

    public function setName($value) {
        $this->info['name'] = $value;
    }

    public function getEmail() {
        return $this->info['email'];
    }

    public function setEmail($value) {
        $this->info['email'] = $value;
    }

    public function getNotify() {
        return $this->info['notify'];
    }

    public function setNotify($value) {
        $this->info['notify'] = $value ? 1 : 0;
    }

    public function getSource() {
        return $this->info['source_id'];
    }

    public function setSource($value) {
        $this->info['source_id'] = $value;
    }

    public function setPassword($password) {
        $this->info['password'] = sha1($password);
    }

    public function authenticate($password) {
        return sha1($password) == $this->info['password'];
    }

    public function isAdmin() {
        return $this->info['admin'] == 1;
    }

    public function setAdmin($value) {
        $this->info['admin'] = $value ? 1 : 0;
    }

    public function sendNewPassword() {
        $password = $this->generatePassword();
        $url = buildUrl('moderate/');

        $email = new Email($this->getEmail());
        $email->setSubject('Your ' . $GLOBALS['CONFIG']['sitetitle'] . ' Moderation Account');
        $email->appendMessage('Your ' . $GLOBALS['CONFIG']['sitetitle']
            . " moderation account password is $password\n\n"
            . "You can access the moderation page at $url.\n");

        $email->send();
    }

    public function generatePassword() {
        $chars = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";
        $i = 0;
        $password = "";
        while ($i <= self::GENERATED_PASSSWORD_LENGTH) {
            $password .= $chars{mt_rand(0, strlen($chars)-1)};
            $i++;
        }       

        $this->userinfo['password'] = sha1($password);

        return $password;
    }       

}

?>
