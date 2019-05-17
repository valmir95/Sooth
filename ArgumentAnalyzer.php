<?php

class ArgumentAnalyzer{

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

            $mainArg = $args[1];

            switch ($mainArg) {
                case 'init':
                    break;
                
                case 'create':
                    //Create migration. Also requiring the name of the migration as argument.
                    if(count($args) == 3){
                        $migrationName = $args[2];
                        if(file_exists("SoothMigrations/migrations")){
                            $migrationTimeStamp = time();
                            $migrationFileName = $migrationTimeStamp . "_migration_" . $migrationName . ".json";
                            $migrationFile = fopen("SoothMigrations/migrations/" . $migrationFileName, "w");
                            $migrationFileAssoc = ['createdAt' => $migrationTimeStamp, 'migrationName' => $migrationName ,'query' => ""];
                            fwrite($migrationFile, json_encode($migrationFileAssoc, JSON_PRETTY_PRINT));
                        }
                        else{
                            throw new Exception('Migration structure not properly set. Have you ran "Sooth init" ?');
                        }
                    }
                    else{
                        throw new Exception('Migration name not specified. The syntax is "Sooth create <migration_name>"');
                    }
                
                default:
                    throw new Exception("Argument not supported");
            }
        }
    }
}