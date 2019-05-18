<?php

//Represents the config.json
class Config{
    /**
     * String representing type of adapter, ex. MySQL
     *
     * @var string
     */
    private $adapter;

    /**
     * String representing the host
     *
     * @var string
     */
    private $host;

    /**
     * String representing the name of the database
     *
     * @var string
     */
    private $database;

    /**
     * String representing the username
     *
     * @var string
     */
    private $user;

    /**
     * String representing the password
     *
     * @var string
     */
    private $pass;

    /**
     * Integer representing the port
     *
     * @var int
     */
    private $port;


    public function __construct($adapter, $host, $database, $user, $pass, $port)
    {
        $this->adapter = $adapter;
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    /**
     * Get Config object from json file
     *
     * @param string $json
     * @return Config|null
     */
    public static function fromJsonPath($configPath){
        if(file_exists($configPath)){
            $configJson = file_get_contents($configPath);
            $configAssoc = json_decode($configJson, true);
        
            return new Config(
                $configAssoc['adapter'], $configAssoc['host'], 
                $configAssoc['database'], $configAssoc['user'], 
                $configAssoc['pass'], $configAssoc['port']
            );
        }
        return null;
    }


    /**
     * Getter for adapter
     *
     * @return string
     */
    function getAdapter(){
        return $this->adapter;
    }
    /**
     * Getter for host
     *
     * @return string
     */
    function getHost(){
        return $this->host;
    }
    /**
     * Getter for database name
     *
     * @return string
     */
    function getDatabase(){
        return $this->database;
    }
    /**
     * Getter for username
     *
     * @return string
     */
    function getUser(){
        return $this->user;
    }
    /**
     * Getter for pass
     *
     * @return string
     */
    function getPass(){
        return $this->pass;
    }
    /**
     * Getter for port
     *
     * @return int
     */
    function getPort(){
        return $this->port;
    }
}