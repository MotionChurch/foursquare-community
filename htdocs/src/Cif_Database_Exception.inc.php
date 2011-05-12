<?php
/**
 * @category Cif
 * @package Cif_Database
 *
 * @author Jesse Morgan <jesse@jesterpm.net>
 * @copyright Copyright (c) 2009, Jesse Morgan
 * @version $Id: Cif_Database_Exception.inc.php 134 2011-03-08 23:35:57Z jessemorgan $
 */

/**
 * Cif_Database_Exception is a MySQL specific exception.
 *
 * @package Cif_Database
 */
class Cif_Database_Exception extends Exception {
    /**
     * Constructor for the Cif_Database_Exception.
     * Creates a new Exception with the mysql error messages as the message.
     *
     * @param string $message Message to prepend to the Exception message.
     */
    public function __construct($message) {
        parent::__construct($message . " Error: ". mysql_error(), mysql_errno());
    }



}

?>