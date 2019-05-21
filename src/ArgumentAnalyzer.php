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

    /**
     * RecordMigrator class
     *
     * @var RecordMigrator
     */
    private $recordMigrator;



    /**
     * Constructor
     *
     * @param Config $config
     * @param RecordMigrator $recordMigrator
     * @param array $args
     */
    public function __construct($config, $recordMigrator, $args){
        $this->config = $config;
        $this->recordMigrator = $recordMigrator;
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
                    throw new Exception("Argument not supported. Supported arguments: \n init \n create <migration_name> \n migrate");
            }
        }
        else{
            echo "Supported arguments: \n init \n create <migration_name> \n migrate";
        }
    }


    /**
     * Creates structure of migration folder.
     *
     * @return void
     */
    private function init(){
        $this->recordMigrator->getMigrationStructure()->createMigrationStructure();
    }

    /**
     * Migrates every migration that has not been completed.
     *
     * @return void
     */
    private function migrate(){
        $this->recordMigrator->migrateRecords();
    }

    /**
     * Creates a single migration.
     *
     * @return void
     */
    private function create(){
        if(count($this->args) == 2){
            $migrationName = $this->args[1];
            $this->recordMigrator->createRecord($migrationName);
        }
        else{
            throw new Exception('Migration syntax invalid. The syntax is "Sooth create <migration_name>"');
        }
    }
}