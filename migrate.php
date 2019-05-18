<?php
include "ArgumentAnalyzer.php";

try{
    print_r($assoc['completed']);
    if(count($argv) > 0){
        array_shift($argv);
        ArgumentAnalyzer::executeArgs($argv);
    }
}
catch(Exception $ex){
    echo $ex->getMessage();
}






