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
class CommandAnalyzer{

    /**
     * Config object
     *
     * @var Config
     */
    private $config;

    /**
     * Array of arguments given to Sooth
     *
     * @var Command
     */
    private $command;

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
     * @param Command $command
     */
    public function __construct($config, $recordMigrator, $command){
        $this->config = $config;
        $this->recordMigrator = $recordMigrator;
        $this->command = $command;
    }



    /**
     * Executes args given to tool.
     *
     * @return void
     */
    public function executeArgs(){
        if(!is_null($this->command)){

            switch ($this->command->getMainCommandTerm()) {
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
        if (count($this->command->getSubCommandTerms()) == 0){
            $this->recordMigrator->getMigrationStructure()->createMigrationStructure();
        }
        else{
            throw new Exception('Migration syntax invalid. The syntax is "bin/vendor/sooth init"');
        }
    }

    /**
     * Migrates every migration that has not been completed.
     *
     * @return void
     */
    private function migrate(){
        if (count($this->command->getSubCommandTerms()) == 0) {
            $this->recordMigrator->migrateRecords();
        }
        else{
            throw new Exception('Migration syntax invalid. The syntax is "bin/vendor/sooth migrate"');
        }
    }

    /**
     * Creates a single migration.
     *
     * @return void
     */
    private function create(){
        if(count($this->command->getSubCommandTerms()) == 1){
            $migrationName = $this->command->getSubCommandTerms()[0];
            $this->recordMigrator->createRecord($migrationName);
        }
        else{
            throw new Exception('Migration syntax invalid. The syntax is "bin/vendor/sooth create <migration_name>"');
        }
    }
}