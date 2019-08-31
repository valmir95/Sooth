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

require __DIR__ . '/../src/composer_autoloader.php';
require __DIR__ . "/../src/Util/UUID.php";
require __DIR__ . "/../src/Config/Config.php";
require __DIR__ . "/../src/Command/Command.php";
require __DIR__ . "/../src/Database/AdapterFactory.php";
require __DIR__ . "/../src/Database/DatabaseAdapter.php";
require __DIR__ . "/../src/Database/MySqlAdapter.php";
require __DIR__ . "/../src/Migration/MigrationStructure.php";
require __DIR__ . "/../src/Migration/RecordMigrator.php";
require __DIR__ . "/../src/Command/CommandAnalyzer.php";
require __DIR__ . "/../src/SoothApp.php";


try{
    $command = Command::fromArgv($argv);
    $configPath = 'SoothMigrations/config.json';
    $migrationRootDir = "SoothMigrations";
    $config = Config::fromJsonFile($configPath);
    $databaseAdapter = AdapterFactory::getAdapter($config);
    $migrationStructure = new MigrationStructure($migrationRootDir);
    $recordMigrator = new RecordMigrator($config, $databaseAdapter, $migrationStructure);
    $app = new SoothApp($config, $recordMigrator, $command);
    $app->run();
}
catch(Exception $ex){
    echo $ex->getMessage();
}