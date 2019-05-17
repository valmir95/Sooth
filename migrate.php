<?php
include "ArgumentAnalyzer.php";

if(count($argv) > 0){
    array_shift($argv);
    ArgumentAnalyzer::executeArgs($argv);
}