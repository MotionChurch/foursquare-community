<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class PostIterator implements Iterator {
    private $where;
    private $rows;
    private $position;

    public function __construct() {
        $this->where = array();
        $this->rows = array();
        $this->position = 0;
    }

    public function filterStage($stage) {
        $this->where[] = "stage='$stage'";
    }
    
    public function filterSource($source) {
        $this->where[] = "source_id='$source'";
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return new Post($this->rows[$this->position]);
    }

    public function key() {
        return $this->rows[$this->position]['id'];
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->rows[$this->position]);
    }

    public function query() {
        $query = "SELECT * FROM post"; 
        
        if (count($this->where) > 0) {
            $where = join(' AND ', $this->where);
            $query .= " WHERE $where";
        }

        $db = getDatabase();

        $this->rows = $db->fetchAssocRows($query);
        $this->position = 0;
    }
}

?>
