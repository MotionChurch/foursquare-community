<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class Post {
    private $info;
    private $indatabase = false;


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

        return Post::getPost($where);
    }

    public static function getBySecretId($secretid) {
        $where = "secretid='$secretid'";

        return Post::getPost($where);
    }

    private static function getPost($where) {
        $query = "SELECT *, UNIX_TIMESTAMP(created) AS createdts FROM post WHERE $where";

        $db = getDatabase();
        
        $row = $db->fetchAssocRow($query);

        if ($row) {
            $user = new Post();
            $user->info = $row;

            return $user;

        } else {
            return false;
        }
    }

    public function save() {
        $db = getDatabase();

        // Cleanup Info
        foreach ($this->info as $key=>$value) $info[$key] = addslashes($value);

        // Save or create?
        if ($this->indatabase) {
            return $db->update('post', $info, "WHERE `id`='". $this->getId() ."'");

        } else {
            // Creating... set special fields.
            $info['stage'] = 'verification';
            $info['secretid'] = uniqid();
            
            $ret = $db->insert('post', $info);
             
            if ($ret) {
                $this->info['id'] = $ret;
                $this->info['stage'] = 'verification';
                $this->info['secretid'] = $info['secretid'];
            }

            return $ret;
        }
    }

    public function getId() {
        return $this->info['id'];
    }

    public function getSecretId() {
        return $this->info['secretid'];
    }

    public function getName() {
        return htmlspecialchars($this->info['name']);
    }

    public function setName($value) {
        $this->info['name'] = $value;
    }

    public function getDescription() {
        return htmlspecialchars($this->info['description']);
    }

    public function setDescription($value) {
        $this->info['description'] = $value;
    }

    public function getStage() {
        return $this->info['stage'];
    }

    public function approve() {
        $this->info['stage'] = 'approved';
    }

    public function verify() {
        $this->info['stage'] = 'verify';
    }

    public function getCreated() {
        return $this->info['created'];
    }

    public function getEmail() {
        return $this->info['email'];
    }

    public function setEmail($value) {
        $this->info['email'] = $value;
    }

    public function setCategory($value) {
        $this->info['category_id'] = $value;
    }

    public function getAge() {
        $diff = time() - $this->info['createdts'];

        if ($diff < 60) {
            return floor($diff) ." seconds ago";

        } else if ($diff < 3600) {
            return floor($diff / 60) ." minutes ago";

        } else if ($diff < 86400) {
            return floor($diff / 3600) ." hours ago";

        } else if ($diff < 604800) {
            return floor($diff / 86400) ." days ago";

        } else {
            return floor($diff / 604800) . " weeks ago";
        }
    }

    public function getLocation() {
        return $this->info['location'];
    }

    public function sendValidation() {
        $email = new Email($this->getEmail());

        $email->setSubject($GLOBAL['CONFIG']['sitetitle'] . " Email Validation");

        $url = $GLOBALS['CONFIG']['urlroot'] . '/validate.php?id=' . $this->getSecretId();
        
        $email->appendMessage("Please click on the link below to verify your email address.\n\n");
        $email->appendMessage($url);

        $email->send();
    }
}

?>
