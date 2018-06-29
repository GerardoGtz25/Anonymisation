<?php
namespace Anonymization\Anonymization;

use Anonymization\Database\Database as Connection;

use Anonymization\Database\Query as Query;

class Anonymous {

    private $db;
    public $config;
    private $database;
    private $tables = [];
    private $fields = [];
    private $keyWord = [];
    private $queries = [];
    private $counter;

    public function __construct($configAnonymous = "/../Config/ConfigDataBlind.yml", $configDB = "/../Config/config.ini"){

        Connection::setConfig(__DIR__ . $configDB);
        $this->db = Connection::getConnection();
        $type = pathinfo($configAnonymous);
        $extension = ucfirst($type['extension']);



        if ($extension == 'Php'){

            $config = require $configAnonymous;
            $this->database = $config['Data_base'];
            $this->tables = $config['Tables'];
            $this->keyWord = $config['KeyWord'];
            $this->counter = $config['Counter'];

        }else{

            $config = (array) FactoryAnonymization::loadConfig($configAnonymous);
            $this->database = $config['config']['Data_base'];
            $this->tables = $config['config']['Tables'];
            $this->keyWord = $config['config']['KeyWord'];
            $this->counter = $config['config']['Counter'];

        }

    }

    public function start(){

        echo "echo";

        die;

        $this->blindeDatas();

        foreach ($this->tables as $key => $table){

            $this->search_table($key);

        }

        $this->create_query();
        $this->cleanDataBase();
        $this->scannerDataBase();
        $this->showQueries();

    }


    /**
     * @throws \Exception
     */
    public function blindeDatas() {
        echo "Loading Random Data for anonymization";

        $path = __DIR__ . '/../Config/AnonymousData.sql';

        if (!file_exists($path)) throw new Exception("Le fichier : {$path} n'existe pas");

        $sql = file_get_contents($path);

        try {
            $this->db->beginTransaction();
            $this->db->exec($sql);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw ($e);
        }

    }

    /**
     * @throws \Exception
     */
    public function cleanDataBase(){

        try {
            $this->db->beginTransaction();
            $this->db->exec("DROP TABLE IF EXISTS RandomData");
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw ($e);
        }

    }

    /**
     * @param $tables
     */
    public function search_table ($tables) {

        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS ";
        $query .= "WHERE TABLE_SCHEMA = '".$this->database;
        $query .= "' AND TABLE_NAME = '$tables' Group By TABLE_NAME";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $table = $stmt->fetchAll();

        if ($table){
           $this->search_fields($table[0]['TABLE_NAME']);
        }

    }

    /**
     * @param $table
     */
    public function search_fields ($table){

        $flag = true;

        while (current($this->tables[$table]["mapping"]) && $flag === true) {

            $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS ";
            $query .= "WHERE TABLE_SCHEMA = '".$this->database."' ";
            $query .= "AND TABLE_NAME = '".$table."' ";
            $query .= "AND COLUMN_NAME = '".key($this->tables[$table]["mapping"])."' ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $output = $stmt->fetchAll();

            if (empty($output)){

                $flag = false;
                echo "The field ".key($this->tables[$table]["mapping"])." was not found in the table $table" ;

            }

            next($this->tables[$table]["mapping"]);
        }

    }

    public function create_query (){

        $q = new Query();

        while (current($this->tables)) {

            $q->update($this->tables[key($this->tables)]['alias']);
            $q->set($this->tables[key($this->tables)]['mapping']);

            if (!empty($this->tables[key($this->tables)]['condition'])){

                $q->where($this->tables[key($this->tables)]['condition']);

            }

//            var_dump($q->sql);

            $a = "set @rowid:= $this->counter";
            $stmt = $this->db->prepare($a);
            $stmt->execute();
            $stmt = $this->db->prepare($q->sql);
            $stmt->execute();

            next($this->tables);

        }

    }

    public function scannerDataBase(){
        echo "\nScanning sensitive information";

        $query = "SELECT distinct TABLE_NAME from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->database."'";
        $stmt = $this->db->prepare($query);

        $stmt->execute();

        $tables =  $stmt->fetchAll();

        foreach ($tables as $table){

            $this->scannerFields($table["TABLE_NAME"]);

        }

    }

    public function scannerFields ($table){

        $query = "SELECT distinct COLUMN_NAME, DATA_TYPE from INFORMATION_SCHEMA.COLUMNS ";
        $query .= "WHERE TABLE_SCHEMA = '".$this->database."' and TABLE_NAME = '".$table."'";

        $stmt = $this->db->prepare($query);

        $stmt->execute();

        $this->fields = $stmt->fetchAll();

        foreach ($this->fields as $field){

            foreach ($this->keyWord as $key => $mot){

                if($field["DATA_TYPE"] == 'varchar' && $field["COLUMN_NAME"] != 'key' ){

                    $query = "select * from ".$table." where ".$field["COLUMN_NAME"]." = '".$key."'";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                    $output = $stmt->fetchAll();

                    if (!empty($output)){

                        $sql =  "update $table set ".$field["COLUMN_NAME"]." = '".$this->keyWord[$key]."' where ".$field["COLUMN_NAME"]." = '".$key."' ";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute();

                        array_push($this->queries, $query);

                    }

                }

            }

        }

    }

    public function showQueries () {

        echo "\nQueries where we found sensitive information";

        foreach ($this->queries as $query){

            echo "\n$query";

        }
    }

}
