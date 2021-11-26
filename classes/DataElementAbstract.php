<?php
namespace local\stim\it_support_stim\classes;
abstract class DataElementAbstract
{
    protected $domain;
    protected $debug;
    protected $pathConfigDir;
    public function __construct()
    {
        $this->domain = $_SERVER['HTTP_ORIGIN'];
        $this->pathConfigDir = $this->getPathConfigFile();
    }

    public abstract function  getData() ;

    public function getDomain()
    {
        return $this->domain;
    }

    private function getPathConfigFile()
    {
        $path = ( isset( $_SERVER['SCRIPT_URL'] ) && strpos( $_SERVER['SCRIPT_URL'], "api" ) )? str_replace( "classes", "config/", __DIR__ ) : "config/";
        return $path;
    }
}