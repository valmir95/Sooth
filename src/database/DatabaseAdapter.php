<?php

interface DatabaseAdapter{
    /**
     * Connects to the datasource
     *
     * @return void
     */
    public function connect();

    /**
     * Execute query and return response
     *
     * @param string $query
     * @return array
     */
    public function executeQuery($query);

}