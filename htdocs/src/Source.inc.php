<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

require_once "base.inc.php";

class Source {
    private $info;


    public function __construct($info=null) {
        $this->info = $info;
    }

    public static function getSources() {
        $db = getDatabase();

        $query = "SELECT * FROM source ORDER BY name";

        $rows = $db->fetchAssocRows($query);
       
        $result = array();
        foreach ($rows as $row) {
            $source = new Source($row);
            $result[] = $source;
        }

        return $result;
    }

    public static function getById($id) {
        $where = "id='$id'";

        return Source::getSource($where);
    }

    private static function getSource($where) {
        $query = "SELECT * FROM source WHERE $where";

        $db = getDatabase();
        
        $row = $db->fetchAssocRow($query);

        if ($row) {
            $source = new Source();
            $source->info = $row;

            return $source;

        } else {
            return false;
        }
    }

    public function save() {
        $db = getDatabase();

        // TODO: Implement Save
    }

    public function getId() {
        return $this->info['id'];
    }

    public function getName() {
        return htmlspecialchars($this->info['name']);
    }
}

?>


