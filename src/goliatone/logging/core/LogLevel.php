<?php
namespace goliatone\logging\core;


/**
 * Describes log levels
 */
class LogLevel implements ILogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    
    const ALL = 'all';
    const OFF = 'off';
    
    
    public static $ALL;
    
    /**
     * 
     */
    public $name;
    
    /**
     * 
     */
    public $value;
    
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    
    public function passes(ILogLevel $level)
    {
            return !$this->filters( $level );
    }
    
    public function filters(ILogLevel $level, $extrict = FALSE)
    {
        if( $this->value === ALL) return FALSE;
        if( $this->value === OFF) return TRUE;
        if( $extrict) return $this->value !== $level->value;
        else return !($level->value > $this->value);
    }
    
    public function equalTo(ILogLevel $level)
    {
        return $this->value === $level->value;
    }
    
    public static function getFromName( String $name ) 
    {
        $name = strtoupper($name);
        return LogLevel::$name;
    }
    
    public function __toString()
    {
        return "[LogLevel: name => {$this->name}, value => {$this->value}]";
    }

}


LogLevel::$OFF = new LogLevel(LogLevel::OFF,  0x0000);

LogLevel::$DEBUG     = new LogLevel(LogLevel::DEBUG,     0x01);
LogLevel::$INFO      = new LogLevel(LogLevel::INFO,      0x02);
LogLevel::$NOTICE    = new LogLevel(LogLevel::NOTICE,    0x04);
LogLevel::$ERROR     = new LogLevel(LogLevel::ERROR,     0x08);
LogLevel::$CRITICAL  = new LogLevel(LogLevel::CRITICAL,  0x16);
LogLevel::$WARNING   = new LogLevel(LogLevel::WARNING,   0x24);
LogLevel::$ALERT     = new LogLevel(LogLevel::ALERT,     0x32);
LogLevel::$EMERGENCY = new LogLevel(LogLevel::EMERGENCY, 0x64);

LogLevel::$ALL = new LogLevel( LogLevel::ALL, 0xFFFF);