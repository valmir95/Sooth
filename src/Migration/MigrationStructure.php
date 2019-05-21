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
class MigrationStructor{

    private $migrationRootDir;

    public function __construct($migrationRootDir){
        $this->migrationRootDir = $migrationRootDir;
    }

    public function createMigrationStructure(){
        
        if(!file_exists($this->migrationRootDir)){
            $this->createMigrationFolders();
            $this->createConfigFile();
            $this->createCompletedMigrationsFile();
        }
        else{
            throw new Exception("Sooth has already been initialized. Delete SoothMigrations directory for new initialization");
        }
    }

    private function createMigrationFolders(){
        mkdir($this->migrationRootDir . "/migrations", 0777, true);
    }

    private function createConfigFile(){
        $configFile = fopen($this->migrationRootDir . "/config.json", "w");
        $defaultConfig = ['adapter' => 'mysql', 'host' => '127.0.0.1', 'database' => 'dev_db', 'user' => 'root', 'pass' => '', 'port' => 3306, 'charset' => 'utf8'];
        fwrite($configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
    }

    private function createCompletedMigrationsFile(){
        $completedMigrationsFile = fopen($this->migrationRootDir . "/completedMigs.json", "w");
        $defaultCompletedMigrationsContent = ['completed' => []];
        fwrite($completedMigrationsFile, json_encode($defaultCompletedMigrationsContent, JSON_PRETTY_PRINT));
    }

    /**
     * Getter for migrationRootDir
     *
     * @return string
     */
    public function getMigrationRootDir(){
        return $this->migrationRootDir;
    }

}