<?php

class Database
{

	private $serverName = "localhost";
	private $username   = "root";
	private $password   = "21012123op";
	private $database   = "thrifting_apps";

    public $con = null;

    public function getConnection(){
        try { 
            $this->con = new mysqli($this->serverName, $this->username, $this->password, $this->database);
            if ($this->con->connect_error) {
                throw new Exception("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }  
        return $this->con;
    }
}
