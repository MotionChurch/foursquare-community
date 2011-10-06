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

    private $moderator;
    private $exceptionfor;

    public function __construct() {
        $this->moderators = array();
        $this->exceptions = array();
    }

    public function getNumberOfModerators() {
        return count($this->moderators);
    }


    // Iterator methods

    public function rewind() {
        $this->year = date('o') + 0;
        $this->week = date('W') + 0;
        $this->expos = 0;
        $this->nextModerator();
    }

    public function current() {
        return $this->moderator;
    }

    public function key() {
        return strtotime($this->year . 'W' .
            ($this->week < 10 ? '0' : '') . $this->week);
    }

    public function next() {
        if ($this->week == 53) {
            // Check for leap year
            if (date('N', mktime(0, 0, 0, 1, 1, $this->year)) == 4
                or date('N', mktime(0, 0, 0, 12, 31, $this->year)) == 4) {
                
                $this->week++;
            
            } else {
                $this->week = 1;
                $this->year++;
            }

        } else if ($this->week > 53) {
            $this->week = 1;
            $this->year++;
        
        } else {
            $this->week++;
        }

        $this->nextModerator();
    }

    private function nextModerator() {
        // Clear out the exception flag.
        $this->exceptionfor = null;

        // Get the scheduled mod.
        $modpos = $this->week % $this->getNumberOfModerators();
        $this->moderator = User::getById($this->moderators[$modpos]['user_id']);

        // Check for exceptions
        if (count($this->exceptions) > 0) {
            // Skip exceptions prior to the current() date.
            while (
                // We have exceptions to search
                $this->expos < (count($this->exceptions) - 1) and
                // and the year is less than the current() year
                ($this->exceptions[$this->expos]['year'] < $this->year or
                // or if it is the current() year, but less than the week.
                ($this->exceptions[$this->expos]['year'] == $this->year
                and $this->exceptions[$this->expos]['week'] < $this->week))
                ) {

                $this->expos++;
            }

            // Check if any of the top exception apply today.
            while($this->exceptions[$this->expos]['year'] == $this->year
                and $this->exceptions[$this->expos]['week'] == $this->week) {

                if ($this->exceptions[$i]['user_id'] == $this->moderator->getId()) {
                    // Yes, return the replacement
                    $this->exceptionfor = $this->moderator;
                    $this->moderator = User::getById($this->exceptions[$this->expos]['user_id']);
                    break;
                }

                $this->expos++;
            }
        }
    }

    public function valid() {
        // The schedule continues forever.
        return true;
    }

    public function isException() {
        return $this->exceptionfor !== null;
    }

    public function getExceptionFor() {
        return $this->exceptionfor;
    }

    public function query() {
        $db = getDatabase();

        // Get the moderators
        $query = "SELECT * FROM moderator_schedule ORDER BY position"; 
        $this->moderators = $db->fetchAssocRows($query);

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

