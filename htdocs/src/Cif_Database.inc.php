<?php
require_once('Cif_Database_Exception.inc.php');

/**
 * @category Cif
 * @package Cif_Database
 *
 * @author Jesse Morgan <jesse@jesterpm.net>
 * @copyright Copyright (c) 2009, Jesse Morgan
 * @version $Id: Cif_Database.inc.php 134 2011-03-08 23:35:57Z jessemorgan $
 */

/**
 * Cif_Database is an object providing an
 * interface to manipulate a MySQL database.
 *
 * @package Cif_Database
 */
class Cif_Database { 
    /**
     * Creates a new Cif_Database_Database object and connects to the database.
     *
     * @param string $host MySQL Server to connect to.
     * @param string $user Username to connect with.
     * @param string $password Password to connect with.
     * @param string $database Database to select.
     * @throws Cif_Database_Exception if the database can not be opened.
     */
    public function __construct($host, $user, $password, $database) {
        if (!mysql_connect($host, $user, $password)) {
            throw new Cif_Database_Exception("Failed to connect to database.");
        }

        if (!mysql_select_db($database)) {
            throw new Cif_Database_Exception("Failed to select database.");
        }
    }

    /**
     * Fetch one row from the database with the given query.
     *
     * @param string $query The MySQL query.
     * @return array Array of fields mapped to values.
     * @throws Cif_Database_Exception if the query fails.
     */
    public function fetchAssocRow($query) {
        $result = mysql_query($query);

        // A query error occured.
        if (!$result) {
            throw new Cif_Database_Exception("Query Failed.");
        }

        return $this->_cleanRow(mysql_fetch_assoc($result));
    }

    /**
     * Fetch multiple rows from the database with the given query.
     *
     * @param string $query The MySQL query.
     * @return array Array containing arrays of fields mapped to values for each row.
     * @throws Cif_Database_Exception if the query fails.
     */
    public function fetchAssocRows($query) {
        $result = mysql_query($query);

        // A query error occured.
        if (!$result) {
            throw new Cif_Database_Exception("Query Failed.");
        }

        $rows = array();
        while ($row = mysql_fetch_assoc($result)) {
            $rows[] = $this->_cleanRow($row);
        }

        return $rows;
    }

    /**
     * Update a specified table in the database with the values given.
     *
     * @param string $table The table to update.
     * @param array $values Array of fields mapped to values to update.
     * @param string $append Optional string to be appended to the MySQL query.
     * @throws InvalidArgumentException if the table name or values list are empty.
     * @throws Cif_Database_Exception if the query fails.
     */
    public function update($table, $values, $append = "") {
        // If the table name is empty, or they didn't provide an array of updates,
        // throw an exception.
        if ($table == "" or !is_array($values)) {
            throw new InvalidArgumentException();
        }

        // Prep the $values for the update.
        foreach ($values as $field=>$value) {
            if ($value === NULL) {
                $updatefields[] = "`$field`=NULL";

            } else {
                $updatefields[] = "`$field`='$value'";
            }
        }

        $result = mysql_query("UPDATE `$table` SET ". implode(",", $updatefields) ." $append");

        if (!$result) {
            throw new Cif_Database_Exception("Update Failed.");
        }
    }

    /**
     * Insert a collection of rows into the database.
     *
     * @param string $table The table to update.
     * @param array $row Arrays of fields mapped to values for the new row.
     * @return int Auto-incremented id of the new row.
     * @throws InvalidArgumentException if the table name or values list are empty.
     * @throws Cif_Database_Exception if the query fails.
     */
    function insert($table, $row) {
        if (!is_array($row)) {
            throw new InvalidArgumentException();
        }

        foreach ($row as $field=>$value) {
            $fields[] = "`$field`";

            if ($value === NULL) {
                $values[] = "NULL";

            } else {
                $values[] = "'$value'";
            }
        }

        $result = mysql_query("INSERT INTO `$table` (". implode(', ', $fields) .") VALUES (". implode(", ", $values) .")");

        if ($result) {
            $id = mysql_insert_id();

            return $id;

        } else {
            throw new Cif_Database_Exception("Insert Failed.");
        }
    }

    /**
     * Delete rows from the specified table.
     *
     * @param string $table The table to delete from.
     * @param string $where The string to append to the end of the query.
     * @throws Cif_Database_Exception if the query fails.
     */
    function delete($table, $where) {
        $result = mysql_query("DELETE FROM `$table` WHERE $where");

        if (!$result) {
            throw new Cif_Database_Exception("Delete Failed.");
        }
    }

    /**
     * Run a query against the database.
     *
     * @param string $query The query to run.
     * @throws Cif_Database_Exception if the query fails.
     */
    function raw($command) {
        $result = mysql_query($command);

        if (!$result) {
            throw new Cif_Database_Exception("Raw Command.");
        }
    }

    /**
     * Strip the slashes from every field of the given row.
     *
     * @param array $row Array of fields mapped to values.
     * @return array Array of fields mapped to values, without slashes.
     */
    private function _cleanRow($row) {
        if (!$row) return NULL;

        foreach ($row as $key => $value) {
            $new[$key] = stripslashes($value);
        }
        return $new;
    }

}

?>
