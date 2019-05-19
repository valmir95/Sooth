<?php

class MySqlAdapter implements DatabaseAdapter{
    
    //TODO: Needs to get better. Create interface for extensions? (mysqli pdo interface)
    const SUPPORTED_EXTENSIONS = ['mysqli'];
    
    /**
     * MySql extensions type ex. Mysqli, PDO
     *
     * @param string $extensionType
     */
    private $extensionType;

    /**
     * A config.php class representing the data from config.json
     *
     * @var Config
     */
    private $config;

    private $extensionObject;

    public function __construct($config, $extensionType)
    {
        //TODO: Needs to get better. Create interface for extensions? (mysqli pdo interface)
        if(!in_array(strtolower($extensionType), self::SUPPORTED_EXTENSIONS)){
            throw new Exception("Extensiontype $extensionType not supported");
        }
        $this->extensionType = $extensionType;
        $this->config = $config;
    }

    public function connect(){
        if($this->extensionType == 'mysqli'){
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
        else if($this->extensionType == 'pdo'){

        }
    }
    /**
     * {@inheritDoc}
     */
    //TODO: Needs to get better. Create interface for extensions? (mysqli pdo interface)
    public function executeQuery($query)
    {
        if($this->extensionType == 'mysqli'){
            $queryResult = $this->extensionObject->query($query);
            if(!$queryResult){
                throw new Exception($this->extensionObject->error);
            }
            return $queryResult;
        }
        else if($this->extensionType == 'pdo'){

        }
    }
}