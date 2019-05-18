<?php

class ArgumentAnalyzer{

    const MIGRATION_ROOT_DIR = "SoothMigrations";
    public function __construct()
    {
        
    }

    /**
     * Executes args given to tool.
     *
     * @param [type] $args
     * @return void
     */
    public static function executeArgs($args){
        if(count($args) > 0){

            $mainArg = $args[0];

            switch ($mainArg) {
                case 'init':
                    if(!file_exists(self::MIGRATION_ROOT_DIR)){
                        mkdir(self::MIGRATION_ROOT_DIR . "/migrations", 0777, true);
                        $configFile = fopen(self::MIGRATION_ROOT_DIR . "/config.json", "w");
                        $defaultConfig = ['databaseType' => '', 'credentials' => ['username' => '', 'password' => '']];

                        $completedMigrationsFile = fopen(self::MIGRATION_ROOT_DIR . "/completedMigrations.json", "w");
                        $defaultCompletedMigrationsContent = ['completed' => []];
                        fwrite($completedMigrationsFile, json_encode($defaultCompletedMigrationsContent, JSON_PRETTY_PRINT));
                        fwrite($configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
                    }
                    break;
                
                case 'migrate':
                    $directory = self::MIGRATION_ROOT_DIR . "/migrations/";
                    $migrationFileNames = array_diff(scandir($directory), array('..', '.'));
                    $completedMigrationsFileContent = file_get_contents(self::MIGRATION_ROOT_DIR . "/completedMigrations.json");
                    $completedMigrationsFileAssoc = json_decode($completedMigrationsFileContent, true);
                    
                    foreach($migrationFileNames as $fileName){
                        if(!in_array($fileName, $completedMigrationsFileAssoc['completed'])){
                            $migrationFileContent = file_get_contents(self::MIGRATION_ROOT_DIR . "/migrations/" . $fileName);
                            $migrationFileAssoc = json_decode($migrationFileContent,true);
                            //Run for every query
                            //TODO: Keep an error tally for every query and store that error message/count for every element in completedMigrations.json?
                            //runSql($migrationFileAssoc['query']);
                            $completedMigrationsFileAssoc['completed'][] = $fileName;
                        }
                    }

                    $completedMigrationsFile = fopen(self::MIGRATION_ROOT_DIR . "/completedMigrations.json", "w");
                    fwrite($completedMigrationsFile, json_encode($completedMigrationsFileAssoc, JSON_PRETTY_PRINT));
                    break;

                
                case 'create':
                    //Create migration. Also requiring the name of the migration as argument.
                    if(count($args) == 2){
                        $migrationName = $args[1];
                        if(file_exists("SoothMigrations/migrations")){
                            $migrationTimeStamp = time();
                            $migrationFileName = $migrationTimeStamp . "_migration_" . $migrationName . ".json";
                            $migrationFile = fopen("SoothMigrations/migrations/" . $migrationFileName, "w");
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
                    break;
                
                default:
                    throw new Exception("Argument not supported");
            }
        }
    }
}