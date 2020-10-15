<?php

class Database{
    private $servername;
    private $dbusername;
    private $dbpassword;
    private $dbname; 
    protected $conn;
    
    protected function connect(){
        $this->servername = "127.0.0.1";
        $this->dbusername = "root";
        $this->dbpassword = "";
        $this->dbname = "xero-pesapal";

        $this->conn = new mysqli($this->servername, $this->dbusername, $this->dbpassword, $this->dbname)or die("Oops can't connect to database");
         
        if ($this->conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
        
    }

}