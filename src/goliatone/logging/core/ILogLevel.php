<?php
namespace goliatone\logging\core;


interface ILogLevel
{
    public function passes(ILogLevel $level);
    
    public function filters(ILogLevel $level, $extrict = FALSE);
    
    public function equalTo(ILogLevel $level);
    
    public static function getFromName(String $name);
    
}