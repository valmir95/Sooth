<?php
class Version{
    /**
     * Major version
     *
     * @var int
     */
    private $majorVersion;

    /**
     * Minor version
     *
     * @var int
     */
    private $minorVersion;

    /**
     * Patch version
     *
     * @var int
     */
    private $patchVersion;

    /**
     * Constructor for Version
     *
     * @param int $majorVersion
     * @param int $minorVersion
     * @param int $patchVersion
     */
    public function __construct($majorVersion, $minorVersion, $patchVersion){
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->patchVersion = $patchVersion;
    }

    /**
     * Compares two versions. 
     *
     * if version1 > version2 return 1
     * if version1 < version2 return -1
     * if version1 == version2 return 0
     * @param Version $version1
     * @param Version $version2
     * @return int
     */
    public static function compareVersions(Version $version1, Version $version2){
        //Comparing major versions
        if($version1->getMajorVersion() > $version2->getMajorVersion()) return 1;
        else if($version1->getMajorVersion() < $version2->getMajorVersion()) return -1;
        //Comparing minor versions
        else{
            if($version1->getMinorVersion() > $version2->getMinorVersion()) return 1;
            else if($version1->getMinorVersion() < $version2->getMinorVersion()) return -1;
            //Comparing patch versions
            else{
                if($version1->getPatchVersion() > $version2->getPatchVersion()) return 1;
                else if($version1->getPatchVersion() < $version2->getPatchVersion()) return -1;
                //Equal
                else return 0;
            }
        }
    }

    /**
     * Constructs and returns a Version object from string (e.g 2.1.1)
     *
     * @param string $stringVersion
     * @return void
     */
    public static function stringToVersion($stringVersion){
        $versionParts = explode(".", $stringVersion);
        if(count($versionParts) == 3){
            return new Version($versionParts[0], $versionParts[1], $versionParts[2]);
        }
        throw new Exception("Version format is wrong. Use semantic versioning format. E.g (2.0.0)");
    }

    /** Getters */

    /**
     * Getter
     *
     * @return int
     */
    public function getMajorVersion(){
        return $this->majorVersion;
    }

    /**
     * Getter
     *
     * @return int
     */
    public function getMinorVersion(){
        return $this->minorVersion;
    }

    /**
     * Getter
     *
     * @return int
     */
    public function getPatchVersion(){
        return $this->patchVersion;
    }

    /**
     * Returns a formatted version string (eg. 10.4.7)
     *
     * @return string
     */
    public function getFormattedVersionString(){
        return $this->majorVersion . "." . $this->minorVersion . "." . $this->patchVersion; 
    }
}