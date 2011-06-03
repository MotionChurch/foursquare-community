<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class Page {
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

        return Page::getPage($where);
    }

    public static function getByUrl($url) {
        $where = "url='$url'";

        return Page::getPage($where);
    }

    private static function getPage($where) {
        $query = "SELECT * FROM page WHERE $where";

        $db = getDatabase();
        
        $row = $db->fetchAssocRow($query);

        if ($row) {
            $page = new Page();
            $page->info = $row;
            $page->indatabase = true;

            return $page;

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
                $db->update('page', $info, "WHERE `id`='"
                    . $this->getId() ."'");
                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }

        } else {
            // Creating...
            try {
                $ret = $db->insert('page', $info);

                if ($ret) {
                    $this->info['id'] = $ret;
                    $this->indatabase = true;
                }

                return true;

            } catch (Cif_Database_Exception $e) {
                return false;
            }
        }
    }

    public function delete() {
        $db = getDatabase();

        $db->delete('page', 'id=' . $this->getId());

        $this->indatabase = false;
    }

    public function getId() {
        return $this->info['id'];
    }

    public function getTitle() {
        return $this->info['title'];
    }

    public function setTitle($value) {
        $this->info['title'] = $value;
    }

    public function getURL() {
        return $this->info['url'];
    }

    public function setURL($value) {
        $this->info['url'] = $value;
    }

    public function getContent() {
        return $this->info['content'];
    }

    public function setContent($value) {
        $this->info['content'] = $value;
    }
}

?>
