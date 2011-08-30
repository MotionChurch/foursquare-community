<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class ModerationSchedule implements Iterator {
    private $moderators;
    private $exceptions;

    private $year;
    private $week;
    private $expos;

    public function __construct() {
        $this->moderators = array();
        $this->exceptions = array();
    }

    public function getNumberOfModerators() {
        return count($this->moderators);
    }


    // Iterator methods

    public function rewind() {
        $this->year = date('o');
        $this->week = date('W') + 0;
        $this->expos = 0;
    }

    public function current() {
        // Get the scheduled mod.
        $modpos = $this->week % $this->getNumberOfModerators();
        $moderator = $this->moderators[$modpos]['user_id'];

        // Check for exceptions
        if (count($this->exceptions) > 0) {
            // Skip exceptions prior to the current() date.
            while (
                // We have exceptions to search
                $this->expos < count($this->exceptions) and
                // and the year is less than the current() year
                ($this->exceptions[$this->expos]['year'] < $this->year or
                // or if it is the current() year, but less than the week.
                ($this->exceptions[$this->expos]['year'] == $this->year
                and $this->exceptions[$this->expos]['week'] < $this->week))
                ) {

                $this->expos++;
            }

            // Check if the top exception is for today.
            if ($this->exceptions[$this->expos]['year'] == $this->year
                and $this->exceptions[$this->expos]['week'] == $this->week
            ) {
                // Yes, return the replacement
                $moderator = $this->exceptions[$this->expos]['user_id'];
            }
        }
        
        return User::getById($moderator);
    }

    public function key() {
        return strtotime($this->year . 'W' .
            ($this->week < 10 ? '0' : '') . $this->week);
    }

    public function next() {
        if ($this->week == 53) {
            $this->week = 1;
            $this->year++;
        
        } else {
            $this->week++;
        }
    }

    public function valid() {
        // The schedule continues forever.
        return true;
    }

    private function query() {
        $db = getDatabase();
        $this->rewind();

        // Get the moderators
        $query = "SELECT * FROM moderation_schedule ORDER BY position"; 
        $this->moderators = $db->fetchAssocRows($query);

        // Get the exceptions
        $year = date('o');
        $week = date('W');

        $query = "SELECT * FROM moderator_exceptions"
            . " WHERE year >= $year AND week >= $week"
            . " ORDER BY year, week";
        $this->exceptions = $db->fetchAssocRows($query);
    }
}

?>

