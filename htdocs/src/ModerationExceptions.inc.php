<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class ModerationExceptions implements Iterator {
    private $exceptions;

    private $expos;

    public function __construct() {
        $this->exceptions = array();
    }

    // Iterator methods

    public function rewind() {
        $this->expos = 0;
    }

    public function current() {
        return User::getById($this->exceptions[$this->expos]['user_id']);
    }

    public function key() {
        $year = $this->exceptions[$this->expos]['year'];
        $week = $this->exceptions[$this->expos]['week'] + 0;

        return strtotime($year . 'W' .
            ($week < 10 ? '0' : '') . $week);
    }

    public function next() {
        $this->expos++;
    }

    public function valid() {
        return $this->expos < count($this->exceptions);
    }

    public function query() {
        $db = getDatabase();

        // Get the exceptions
        $year = date('o');
        $week = date('W');

        $query = "SELECT * FROM moderator_exceptions"
            . " WHERE year >= $year AND week >= $week"
            . " ORDER BY year, week";
        $this->exceptions = $db->fetchAssocRows($query);

        $this->rewind();
    }
}

?>

