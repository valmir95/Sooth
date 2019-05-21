<?php

class Command{
    /**
     * The first command term
     *
     * @var string
     */
    private $mainCommandTerm;

    /**
     * Every command term coming after the main command term
     *
     * @var array
     */
    private $subCommandTerms;

    public function __construct($mainCommandTerm, $subCommandTerms = []){
        $this->mainCommandTerm = $mainCommandTerm;
        $this->subCommandTerms = $subCommandTerms;
    }


    /**
     * Returns a Command object from the $argv
     *
     * @param array $argv
     * @return Command
     */
    public static function fromArgv($argv){
        array_shift($argv);
        if(count($argv) > 0){
            $mainCommandTerm = $argv[0];
            $subCommandTerms = [];
            if(count($argv) > 1){
                array_shift($argv);
                $subCommandTerms = $argv;
            }
            return new Command($mainCommandTerm, $subCommandTerms);
        }

        return null;

    }


    /**
     * Getter
     *
     * @return string
     */
    public function getMainCommandTerm(){
        return $this->mainCommandTerm;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getSubCommandTerms(){
        return $this->subCommandTerms;
    }
}