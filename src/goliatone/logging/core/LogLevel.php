<?php namespace goliatone\flatg\logging\core;


/**
 * Class LogLevel. Holds log level constants.
 * Error severity from low to high.
 * @package goliatone\flatg\logging\core
 */
final class LogLevel
{

///////////////////////////////////////////////////
// LogLevel NUMERICAL CODES
///////////////////////////////////////////////////
    /**
     * Do not let any events to go through
     */
    const OFF       = 0xFFFFFF;

    /**
     * Detailed debug information
     */
    const DEBUG     = 1;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO      = 2;

    /**
     * Uncommon events
     */
    const NOTICE    = 4;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs,
     * poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    const WARNING   = 8;

    /**
     * Runtime errors
     */
    const ERROR     = 16;

    /**
     * Critical conditions
     *
     * Example: Application component
     * unavailable, unexpected exception.
     */
    const CRITICAL  = 32;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT     = 64;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 128;


    /**
     * Allow all events to go through
     */
    const ALL       = 0;


    /**
     * @var array
     */
    public static $levels = array(
        self::OFF       => 'OFF',
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
        self::ALL       => 'ALL',
    );

///////////////////////////////////////////////////
// LogLevel DEFINITIONS.
///////////////////////////////////////////////////
    static public $OFF;
    static public $DEBUG;
    static public $INFO;
    static public $NOTICE;
    static public $WARNING;
    static public $ERROR;
    static public $CRITICAL;
    static public $ALERT;
    static public $EMERGENCY;
    static public $ALL;


    static public $CODES_TO_LEVEL  = array();
    static public $LABELS_TO_LEVEL = array();

    /**
     * @param $level
     * @return LogLevel
     * @throws \InvalidArgumentException
     */
    static public function getLevel($level)
    {
        if(is_a($level, __CLASS__)) return $level;

        if(is_int($level))
        {
            if (!isset(static::$CODES_TO_LEVEL[$level]))
            {
                throw new \InvalidArgumentException('Level '.$level.' is not defined, use one of: '.implode(', ', array_values(static::$levels)));
            }

            return static::$CODES_TO_LEVEL[$level];
        }

        if(is_string($level))
        {
            $level = strtoupper($level);

            if (!isset(static::$LABELS_TO_LEVEL[$level]))
            {
                throw new \InvalidArgumentException('Level '.$level.' is not defined, use one of: '.implode(', ', array_values(static::$LABELS_TO_LEVEL)));
            }

            return static::$LABELS_TO_LEVEL[$level];
        }

        throw new \InvalidArgumentException('Level '.$level.' is not defined, use one of: '.implode(', ', array_values(static::$LABELS_TO_LEVEL)));
    }


    /**
     * @var string
     */
    protected $_label = '';


    /**
     * @var int
     */
    protected $_code = -1;

    /**
     * @param $label
     * @param $code
     */
    public function __construct($label, $code)
    {
        $this->_code  = $code;
        $this->_label = strtoupper($label);

        //Register level for easy look up:
        self::$CODES_TO_LEVEL[$code]   = $this;
        self::$LABELS_TO_LEVEL[$label] = $this;
    }

    public function filters($level, $strict = FALSE)
    {
        $level = self::getLevel($level);

        if($strict) return $this->getCode() !== $level->getCode();
        else return ($this->getCode() >= $level->getCode());
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    public function __toString()
    {
        return $this->getLabel();
    }
}

//////////////////////////////////////////////
/*
 * I know, this is ugly, but oh well.
 * We want to have a static reference to
 * an actual LogLevel instance.
 * We create them at runtime.
 */
//////////////////////////////////////////////
if(!LogLevel::$ALL)
{
    foreach(LogLevel::$levels as $code => $label)
    {
        LogLevel::$$label = new LogLevel($label, $code);
    }
}


