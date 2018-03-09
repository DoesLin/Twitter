<?php
use Symfony\Component\Yaml\Yaml;

class Db {
    private static $connection = null;

    public static function get_db_connection() {
        return self::$connection;
    }

    public static function dbc() {
        return self::get_db_connection();
    }

    public static function connect($testing=false) {
        $config = Yaml::parse(file_get_contents(dirname(__FILE__)."/../config/db.yaml"));
        $o = $testing ? $config["test"] : $config["app"];
        $port = isset($o["port"]) ? ";port=".$o["port"]."" : "";
        
        self::$connection = new PDO('mysql:host='.$o["server"].''.$port.';dbname='.$o["db"].';charset=utf8', $o["username"], $o["password"]);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function init_db($with_fixtures=false) {
        
    }

    public static function flush() {
        foreach(self::$connection->query("SHOW TABLES") as $t) {
            // Disable foreign key checks, empty the tables then re activate the foreign key checks.
            // Without the re activation, the "ON DELETE CASCADE" won't work, since the foreign key checks are disabled.
            self::$connection->query("SET foreign_key_checks=0; DELETE FROM " . $t[0] . "; SET foreign_key_checks=1;");
        }
    }
}
