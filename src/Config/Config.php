<?php
/* Sooth
 *
 * (The MIT license)
 * Copyright (c) 2019 Valmir Memeti
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
//Represents the config.json
class Config{
    /**
     * String representing type of adapter, ex. MySQL
     *
     * @var AdapterConfig
     */
    private $adapterConfig;

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


    public function __construct($adapterConfig, $host, $database, $user, $pass, $port)
    {
        $this->adapterConfig = $adapterConfig;
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
    public static function fromJsonFile($configPath){
        if(file_exists($configPath)){
            $configJson = file_get_contents($configPath);
            $configAssoc = json_decode($configJson, true);
            $adapterConfig = new AdapterConfig($configAssoc['adapter']['name'], $configAssoc['adapter']['extension']);
        
            return new Config(
                $adapterConfig, $configAssoc['host'], 
                $configAssoc['database'], $configAssoc['user'], 
                $configAssoc['pass'], $configAssoc['port']
            );
        }
        return null;
    }


    /**
     * Getter for adapter
     *
     * @return AdapterConfig
     */
    public function getAdapterConfig(){
        return $this->adapterConfig;
    }
    /**
     * Getter for host
     *
     * @return string
     */
    public function getHost(){
        return $this->host;
    }
    /**
     * Getter for database name
     *
     * @return string
     */
    public function getDatabase(){
        return $this->database;
    }
    /**
     * Getter for username
     *
     * @return string
     */
    public function getUser(){
        return $this->user;
    }
    /**
     * Getter for pass
     *
     * @return string
     */
    public function getPass(){
        return $this->pass;
    }
    /**
     * Getter for port
     *
     * @return int
     */
    public function getPort(){
        return $this->port;
    }

    
}