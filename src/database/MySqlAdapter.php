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
//TODO: Re-evaluate and maybe only use PDO?
class MySqlAdapter implements DatabaseAdapter{
    
    //TODO: Needs to get better. Create interface for extensions? (mysqli pdo interface).
    const SUPPORTED_EXTENSIONS = ['mysqli', 'pdo'];

    /**
     * A config.php class representing the data from config.json.
     *
     * @var Config
     */
    private $config;

    /**
     * Represents the actual extension type in object form.
     *
     * @var mysqli|PDO
     */
    private $extensionObject;

    public function __construct($config)
    {
        $this->config = $config;
        $this->extensionObject = null;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function connect(){

        if(!empty($this->config->getAdapterConfig()->getExtensionName()) && !in_array($this->config->getAdapterConfig()->getExtensionName(), self::SUPPORTED_EXTENSIONS)){
            throw new Exception("Extension " . $this->config->getAdapterConfig()->getExtensionName() .  " not supported");
        }
        if($this->isMysqli()){
            $this->extensionObject = new mysqli(
                $this->config->getHost(), 
                $this->config->getUser(), 
                $this->config->getPass(), 
                $this->config->getDatabase()
            );
            if($this->extensionObject->connect_errno){
                throw new Exception("Could not connect to db!");
            }
        }
        else if($this->isPdo()){
            $dsn = "mysql:host=" . $this->config->getHost() . ";dbname=" . $this->config->getDatabase() . ";charset=" . $this->config->getCharset();
            
            //TODO: Look over more options
            $options = [
                PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
              ];
            $this->extensionObject = new PDO($dsn, $this->config->getUser(), $this->config->getPass(), $options);
            
        }
    }
    /**
     * {@inheritDoc}
     */
    //TODO: Needs to get better. Create interface for extensions? (mysqli pdo interface).
    public function executeQuery($query){

        if(is_null($this->extensionObject)){
            throw new Exception("Extension object not set. Adapter needs to run connect() before executing query");
        }

        if($this->isMysqli()){
            $queryResult = $this->extensionObject->query($query);
            if(!$queryResult){
                throw new Exception($this->extensionObject->error);
            }
            return $queryResult;
        }

        else if($this->isPdo()){
            $result = $this->extensionObject->query($query);
        }

    }

    private function isMysqli(){
        return $this->config->getAdapterConfig()->getExtensionName() == 'mysqli';
    }
    
    private function isPdo(){
        return $this->config->getAdapterConfig()->getExtensionName() == 'pdo'  || empty($this->config->getAdapterConfig()->getExtensionName());
    }
}