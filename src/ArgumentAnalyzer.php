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

class ArgumentAnalyzer{

    const MIGRATION_ROOT_DIR = "SoothMigrations";

    /**
     * A config.php class representing the data from config.json
     *
     * @var Config
     */
    private $config;

    /**
     * Array of arguments given to Sooth
     *
     * @var array
     */
    private $args;



    public function __construct($config, $args){
        $this->config = $config;
        $this->args = $args;
    }



    /**
     * Executes args given to tool.
     *
     * @return void
     */
    public function executeArgs(){
        if(count($this->args) > 0){

            $mainArg = $this->args[0];

            switch ($mainArg) {
                case 'init':
                    $this->init();
                    break;
                
                case 'migrate':
                    $this->migrate();
                    break;

                
                case 'create':
                    $this->create();
                    break;
                
                default:
                    throw new Exception("Argument not supported");
            }
        }
        else{
            echo "Supported commands: \n init \n create <migration_name> \n migrate";
        }
    }


    /**
     * Creates structure of migration folder.
     *
     * @return void
     */
    private function init(){
        if(!file_exists(self::MIGRATION_ROOT_DIR)){
            mkdir(self::MIGRATION_ROOT_DIR . "/migrations", 0777, true);
            $configFile = fopen(self::MIGRATION_ROOT_DIR . "/config.json", "w");
            $defaultConfig = ['databaseType' => '', 'credentials' => ['username' => '', 'password' => '']];

            $completedMigrationsFile = fopen(self::MIGRATION_ROOT_DIR . "/completedMigs.json", "w");
            $defaultCompletedMigrationsContent = ['completed' => []];
            fwrite($completedMigrationsFile, json_encode($defaultCompletedMigrationsContent, JSON_PRETTY_PRINT));
            fwrite($configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
        }
        else{
            throw new Exception("Sooth has already been initialized. Delete SoothMigrations directory for new initialization");
        }
    }

    /**
     * Migrates every migration that has not been completed.
     *
     * @return void
     */
    private function migrate(){
        $directory = self::MIGRATION_ROOT_DIR . "/migrations/";
        if(file_exists($directory)){
            $migrationFileNames = array_diff(scandir($directory), array('..', '.'));
            $completedMigrationsFileContent = file_get_contents(self::MIGRATION_ROOT_DIR . "/completedMigs.json");
            $completedMigrationsFileAssoc = json_decode($completedMigrationsFileContent, true);
            $migrationsRan = [];
            
            foreach($migrationFileNames as $fileName){
                if(!in_array($fileName, $completedMigrationsFileAssoc['completed'])){
                    $migrationFileContent = file_get_contents(self::MIGRATION_ROOT_DIR . "/migrations/" . $fileName);
                    $migrationFileAssoc = json_decode($migrationFileContent,true);
                    //Run for every query
                    //TODO: Keep an error tally for every query and store that error message/count for every element in completedMigs.json?
                    //runSql($migrationFileAssoc['query']);
                    echo $fileName . " Sucessfully migrated \n";
                    $completedMigrationsFileAssoc['completed'][] = $fileName;
                }
            }
    
            $completedMigrationsFile = fopen(self::MIGRATION_ROOT_DIR . "/completedMigs.json", "w");
            fwrite($completedMigrationsFile, json_encode($completedMigrationsFileAssoc, JSON_PRETTY_PRINT));
        }
        else{
            throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
        }
        
    }

    /**
     * Creates a single migration.
     *
     * @return void
     */
    private function create(){
        if(count($this->args) == 2){
            $migrationName = $this->args[1];
            if(file_exists(self::MIGRATION_ROOT_DIR . "/migrations")){
                $migrationTimeStamp = time();
                $migrationFileName = $migrationTimeStamp . "_migration_" . $migrationName . ".json";
                $migrationFile = fopen(self::MIGRATION_ROOT_DIR . "/migrations/" . $migrationFileName, "w");
                $migrationFileAssoc = ['uniqueId' => uniqid("", true), 'createdAt' => $migrationTimeStamp, 'migrationName' => $migrationName ,'query' => ""];
                fwrite($migrationFile, json_encode($migrationFileAssoc, JSON_PRETTY_PRINT));
            }
            else{
                throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
            }
        }
        else{
            throw new Exception('Migration syntax invalid. The syntax is "Sooth create <migration_name>"');
        }
    }
}