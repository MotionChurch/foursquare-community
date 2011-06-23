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
    const MAX_IMAGE_DIM = 600;

    private $info;
    private $indatabase = false;
    private $images;
    private $category;

    public function __construct($info=null) {
        $this->info = is_null($info) ? array() : $info;

        if ($info !== null and isset($info['id'])) {
            $this->indatabase = true;

        } else {
            $this->indatabase = false;
        }

        $this->images = null;
        $this->category = null;
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
            $info['secretid'] = uniqid('', true);
            $info['created'] = date('Y-m-d H:i:s');
            
            try {
                $ret = $db->insert('post', $info);

                if ($ret) {
                    $this->info['id'] = $ret;
                    $this->info['stage'] = 'verification';
                    $this->info['secretid'] = $info['secretid'];
                    $this->indatabase = true;
                }

                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }
        }
    }

    public function delete() {
        // Rather than deleting the post, archive it.
        $this->info['stage'] = 'deleted';
        $this->save();
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

    public function getSource() {
        if ($this->info['source_id'] != 0) {
            return $this->info['source_id'];
        
        } else {
            return false;
        }
    }

    public function getOtherSource() {
        return $this->info['reason'];
    }

    public function setSource($value) {
        $this->info['source_id'] = $value;
    }

    public function otherSource($value) {
        $this->info['source_id'] = 0;
        $this->info['reason'] = $value;
    }

    public function approve() {
        $this->info['stage'] = 'approved';
        $this->sendAcceptance();
    }

    public function verify() {
        if ($this->getStage() == 'verification') {
            $this->info['stage'] = 'moderation';
            $this->notifyModerators();
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

    public function getPublicEmail() {
        return 'posting-' . $this->getId() .'@'. $GLOBALS['CONFIG']['emaildomain'];
    }

    public function setEmail($value) {
        $this->info['email'] = $value;
    }

    public function setCategory(Category $cat) {
        $this->info['category_id'] = $cat->getId();
        $this->category = $cat;
    }

    public function getCategory() {
        if ($this->category == NULL) {
            $this->category = Categories::getById($this->info['category_id']);
        }

        return $this->category;
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

    public function setLocation($value) {
        $this->info['location'] = $value;
    }

    public function getPrice() {
        return $this->info['price'];
    }

    public function setPrice($value) {
        $this->info['price'] = $value;
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
            // Quietly ignore that the file isn't an image.
            return false;
        }

        // Resize Images if needed
        if ($info[0] > self::MAX_IMAGE_DIM
            or $info[1] > self::MAX_IMAGE_DIM) {

            $ratio = $info[0] / $info[1];

            if ($ratio > 1) {
                // Width limited
                $width = min($info[0], self::MAX_IMAGE_DIM);
                $height = $info[1] * $width / $info[0];

            } else {
                // Height limited
                $height = min($info[1], self::MAX_IMAGE_DIM);
                $width = $info[0] * $height / $info[1];
            }
            
            switch ($info[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file);
                    break;

                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file);
                    break;

                default:
                    // We quietly ignore if the image is not a valid type.
                    return false;
            }

            $newimage = imagecreatetruecolor($width, $height);

            imagecopyresampled($newimage, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
            
            imagejpeg($newimage, $file);

            imagedestroy($newimage);
            imagedestroy($image);
        }

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

        $url = buildUrl('validate.php?id=' . $this->getSecretId());
        
        $email->appendMessage("Please click on the link below to verify your email address.\n\n");
        $email->appendMessage($url);

        $email->send();
    }

    public function sendAcceptance() {
        $email = new Email($this->getEmail());

        $email->setSubject($GLOBALS['CONFIG']['sitetitle'] 
            . " Posting Approved");

        $email->appendMessage("Your posting titled ". $this->getName()
            ." has been approved by our moderation team.\n\n");

        // View URL
        $url = buildUrl('postings/' . $this->getId() . '.html');
        $email->appendMessage("You can view your post at $url.\n\n");

        // Delete URL
        $url = buildUrl('deletepost.php?id=' . $this->getId()
            . '&secret=' . $this->getSecretId());
        $email->appendMessage("Your posting will expire in " .
            $GLOBALS['CONFIG']['expiretime']
            . " days. If you would like to remove it sooner, go to $url.\n");

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

    public function notifyModerators() {
        $ui = new UserIterator();
        $ui->filterNotify(true);
        $ui->query();

        $url = buildUrl('postings/' . $this->getId() .'.html?moderate');

        $email = new Email('');
        $email->setSubject('[' . $GLOBALS['CONFIG']['sitetitle'] . '] New Posting to Moderate');
        $email->appendMessage("A new posting is awaiting moderation.\nYou can view the post at $url");

        if ($ui->valid()) {
            foreach($ui as $user) {
                $email->setTo($user->getEmail());
                $email->send();
            }
        }
    }
}

?>
