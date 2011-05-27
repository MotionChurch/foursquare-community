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
    private $images;


    public function __construct($info=null) {
        $this->info = is_null($info) ? array() : $info;

        if ($info !== null and isset($info['id'])) {
            $this->indatabase = true;

        } else {
            $this->indatabase = false;
        }

        $images = null;
    }

    public static function getById($id) {
        $where = "id='$id'";

        return Post::getPost($where);
    }

    public static function getBySecretId($secretid) {
        $where = "secretid='$secretid'";

        return Post::getPost($where);
    }

    public static function getByImage($imgid) {
        $where = "id=(SELECT post_id FROM image WHERE id='$imgid')";

        return Post::getPost($where);
    }

    private static function getPost($where) {
        $query = "SELECT *, UNIX_TIMESTAMP(created) AS createdts FROM post WHERE $where";

        $db = getDatabase();
        
        $row = $db->fetchAssocRow($query);

        if ($row) {
            $post = new Post();
            $post->info = $row;
            $post->indatabase = true;

            return $post;

        } else {
            return false;
        }
    }

    public function save() {
        $db = getDatabase();

        // Cleanup Info
        foreach ($this->info as $key=>$value) $info[$key] = addslashes($value);

        // Remove artifical fields.
        unset($info['createdts']);

        // Save or create?
        if ($this->indatabase) {
            try {
                $db->update('post', $info, "WHERE `id`='". $this->getId() ."'");
                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }

        } else {
            // Creating... set special fields.
            $info['stage'] = 'verification';
            $info['secretid'] = uniqid();
            $info['created'] = date('Y-m-d H:i:s');
            
            try {
                $ret = $db->insert('post', $info);

                if ($ret) {
                    $this->info['id'] = $ret;
                    $this->info['stage'] = 'verification';
                    $this->info['secretid'] = $info['secretid'];
                }

                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }
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
        if ($this->getStage() == 'moderation') {
            $this->info['stage'] = 'approved';
            $this->sendAcceptance();
        }
    }

    public function verify() {
        if ($this->getStage() == 'verification') {
            $this->info['stage'] = 'moderation';
        }
    }

    public function reject($message='') {
        $this->info['stage'] = 'rejected';

        $this->sendRejection($message);
    }

    public function getCreated() {
        return $this->info['created'];
    }

    public function getTimestamp() {
        return $this->info['createdts'];
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

    public function getImages() { 
        if ($this->images == null) {
            $this->loadImages();
        }

        return $this->images;
    }

    public function addImage($file) {
        // Verify file type
        $info = @getimagesize($file);

        if (!$info) {
            return false;
        }

        // TODO Verify image dimensions?

        // Get image id
        $db = getDatabase();
        try {
            $id = $db->insert('image', array('post_id' => $this->getId()));
            $newfile = $GLOBALS['CONFIG']['uploads'] . "/$id";

            if (move_uploaded_file($file, $newfile)) {
                // Invalidate the image cache
                $this->images = null;

                return true;
            }

        } catch (Cif_Database_Exception $e) {

        }

        return false;
    }

    private function loadImages() {
        $query = "SELECT id FROM image WHERE post_id='". $this->getId() ."'";

        $db = getDatabase();
        $imgs = $db->fetchAssocRows($query);

        $this->images = array();
        foreach ($imgs as $img) {
            $this->images[] = $img['id'];
        }
    }

    public function sendValidation() {
        $email = new Email($this->getEmail());

        $email->setSubject($GLOBALS['CONFIG']['sitetitle'] . " Email Validation");

        $url = $GLOBALS['CONFIG']['urlroot'] . '/validate.php?id=' . $this->getSecretId();
        
        $email->appendMessage("Please click on the link below to verify your email address.\n\n");
        $email->appendMessage($url);

        $email->send();
    }

    public function sendAcceptance() {
        $email = new Email($this->getEmail());

        $email->setSubject($GLOBALS['CONFIG']['sitetitle'] . " Posting Approved");

        $email->appendMessage("Your posting titled ". $this->getName()
            ." has been approved by our moderation team.\n\n");

        $url = $GLOBALS['CONFIG']['urlroot'] . '/postings/'
            . $this->getId() .'.html';
        $email->appendMessage("You can view your post at $url.");

        $email->send();
    }

    public function sendRejection($message='') {
        $email = new Email($this->getEmail());

        $email->setSubject($GLOBALS['CONFIG']['sitetitle'] . " Posting Rejected");

        $email->appendMessage("Your posting titled ". $this->getName()
            ." has been rejected by our moderation team.\n\n");

        if ($message != '') {
            $email->appendMessage("The moderator left the following comment\n");
            $email->appendMessage($message);
        }

        $email->send();
    }
}

?>
