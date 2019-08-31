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

use Yosymfony\Toml\Toml;

class RecordMigrator{

    /**
     * A config.php class representing the data from config.json
     *
     * @var Config
     */
    private $config;

    /**
     * Database adapter
     *
     * @var DatabaseAdapter
     */
    private $databaseAdapter;
    /**
     * Migration structure class
     *
     * @var MigrationStructure
     */
    private $migrationStructure;

    /**
     * RecordMigrator constructor
     *
     * @param Config $config
     * @param DatabaseAdapter $databaseAdapter
     * @param MigrationStructure $migrationStructure
     */
    public function __construct($config, $databaseAdapter, $migrationStructure){
        $this->config = $config;
        $this->databaseAdapter = $databaseAdapter;
        $this->migrationStructure = $migrationStructure;
    }


    /**
     * Migrates all records from migrations folder
     * @return void
     */
    public function migrateRecords(){
        $directory = $this->migrationStructure->getMigrationRootDir() . "/migrations/";
        if(file_exists($directory) && !is_null($this->config)){
            $migrationFileNames = $this->migrationStructure->getMigrationFiles();
            $completedMigrationsFileContent = file_get_contents($this->migrationStructure->getMigrationRootDir() . "/completedMigs.json");
            $completedMigrationsFileAssoc = json_decode($completedMigrationsFileContent, true);
            $this->databaseAdapter->connect();
            
            foreach($migrationFileNames as $fileName){
                $this->migrateRecord($fileName, $completedMigrationsFileAssoc);
            }
    
            $completedMigrationsFile = fopen($this->migrationStructure->getMigrationRootDir() . "/completedMigs.json", "w");
            fwrite($completedMigrationsFile, json_encode($completedMigrationsFileAssoc, JSON_PRETTY_PRINT));
        }
        else{
            throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
        }
    }

    /**
     * Migrates a single record
     *
     * @param string $fileName
     * @param array $completedMigrationsFileAssoc
     * @return void
     */
    private function migrateRecord($fileName, &$completedMigrationsFileAssoc){
        if(!in_array($fileName, $completedMigrationsFileAssoc['completed'])){
            $migrationFileAssoc = Toml::parseFile($this->migrationStructure->getMigrationRootDir() . "/migrations/" . $fileName);
            if(!empty(trim($migrationFileAssoc['migration_details']['query']))){
                try {
                    $this->databaseAdapter->executeQuery($migrationFileAssoc['migration_details']['query']);
                    echo $fileName . " Sucessfully migrated \n";
                } catch (Exception $ex) {
                    throw new Exception($fileName . " migration failed. Error: " . $ex->getMessage());
                }
                $completedMigrationsFileAssoc['completed'][] = $fileName;
            }
        }
    }


    /**
     * Creates a single migration-record
     *
     * @param string $migrationName
     * @return void
     */
    public function createRecord($migrationName){
        if(file_exists($this->migrationStructure->getMigrationRootDir() . "/migrations") && !is_null($this->config)){
            $migrationTimeStamp = time();
            $uniqueId = UUID::v4();
            $migrationFileName = $migrationTimeStamp . "_" . $migrationName . ".toml";
            $migrationFile = fopen($this->migrationStructure->getMigrationRootDir() . "/migrations/" . $migrationFileName, "w");

            /**
             * TODO: To be frank, this is kind of a mess. A cleaner solution would be to use the TOML-builder,
             * but it lacked support for escaping double-quotes needed for creating multi-line string values
             */
            $migrationFileContent = '[migration_details]' . "\n" . 'unique_id = "' . 
                                    $uniqueId . '" #Unique UUID' . "\n" . 'created_at = "' .
                                    $migrationTimeStamp . '" #Unix timestamp of file-creation' .
                                    "\n" . 'migration_name = "' .
                                    $migrationName . '" #Name of migration' . "\n" . 
                                     'query = """ """' . ' #Paste valid query between the multi-line marks';
            fwrite($migrationFile, $migrationFileContent);
        }
        else{
            throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
        }
    }

    /**
     * Getter for Migration Structure object
     *
     * @return MigrationStructure
     */
    public function getMigrationStructure(){
        return $this->migrationStructure;
    }
}