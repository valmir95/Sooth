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
     * Latest migration version
     *
     * @var Version
     */
    private $latestMigrationVersion;

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
        //Default
        $this->latestMigrationVersion = new Version(1, 0, 0);
    }


    /**
     * Migrates all records from migrations folder
     * Takes a Version parameter to upgrade to that specific version
     * @param Version $version
     * @return void
     */
    public function migrateRecords(Version $version = null){
        $directory = $this->migrationStructure->getMigrationRootDir() . "/migrations/";
        if(file_exists($directory) && !is_null($this->config)){
            $migrationFileNames = $this->migrationStructure->getMigrationFiles();
            $completedMigrationsFileContent = file_get_contents($this->migrationStructure->getMigrationRootDir() . "/completedMigs.json");
            $completedMigrationsFileAssoc = json_decode($completedMigrationsFileContent, true);
            $this->databaseAdapter->connect();
            
            foreach($migrationFileNames as $fileName){
                $this->migrateRecord($fileName, $completedMigrationsFileAssoc, $version);
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
     * @param Version $version
     * @return void
     */
    private function migrateRecord($fileName, &$completedMigrationsFileAssoc, $version){
        if(!in_array($fileName, $completedMigrationsFileAssoc['completed'])){
            $migrationFileContent = file_get_contents($this->migrationStructure->getMigrationRootDir() . "/migrations/" . $fileName);
            $migrationFileAssoc = json_decode($migrationFileContent,true);
            if(!empty(trim($migrationFileAssoc['query']))){
                if(!is_null($version)){
                    $recordVersion = Version::stringToVersion($migrationFileAssoc['version']);
                    $versionComparison = Version::compareVersions($version, $recordVersion);
                }
                if(is_null($version) || ($versionComparison == 0 || $versionComparison == 1)){
                    try {
                        $this->databaseAdapter->executeQuery($migrationFileAssoc['query']);
                        echo $fileName . " Sucessfully migrated \n";
                    } catch (Exception $ex) {
                        throw new Exception($fileName . " migration failed. Error: " . $ex->getMessage());
                    }
                    $completedMigrationsFileAssoc['completed'][] = $fileName;
                }
            }
        }
    }


    /**
     * Creates a single migration-record
     *
     * @param string $migrationName
     * @param int $version
     * @return void
     */
    public function createRecord($migrationName, $version = null){
        if(file_exists($this->migrationStructure->getMigrationRootDir() . "/migrations") && !is_null($this->config)){
            if(is_null($version)) $version = $this->latestMigrationVersion;
            $migrationTimeStamp = time();
            $uniqueId = UUID::v4();
            $migrationFileName = $migrationTimeStamp . "_" . $migrationName . ".json";
            $migrationFile = fopen($this->migrationStructure->getMigrationRootDir() . "/migrations/" . $migrationFileName, "w");
            $migrationFileAssoc = ['uniqueId' => $uniqueId, 'version' => $version->getFormattedVersionString(), 'createdAt' => $migrationTimeStamp, 'migrationName' => $migrationName ,'query' => ""];
            fwrite($migrationFile, json_encode($migrationFileAssoc, JSON_PRETTY_PRINT));
        }
        else{
            throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
        }
    }

    private function getLatestVersionFromFiles(){

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