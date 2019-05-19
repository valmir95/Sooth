<?php

class AdapterFactory{

    /**
     * An associative array of
     */
    const REGISTERED_ADAPTERS = [
        "mysql" => "MySqlAdapter",
    ];


    /**
     * Gets adapter based on adapter-name
     *
     * @param string $className
     * @param Config $config
     * @return DatabaseAdapter
     */
    public static function getAdapter($adapterName, $config){
        if(empty(self::REGISTERED_ADAPTERS[$adapterName])){
            throw new Exception($adapterName . " is not supported.");
        }
        $adapterClassName = self::REGISTERED_ADAPTERS[$adapterName];

        return new $adapterClassName($config);
    }
}