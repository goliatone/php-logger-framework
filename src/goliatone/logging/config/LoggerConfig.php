<?php

namespace goliatone\logging\core;

use goliatone\logging\core\LogLevel;
use goliatone\logging\core\ILoggerConfig;

class LoggerConfig implements ILoggerConfig
{
    /**
     *
     */
    const DEBUG  = "debug";

    /**
     *
     */
    const REMOTE = "remote";

    /**
     *
     */
    const RELEASE = "release";

    /**
     *
     */
    static public $DEFAULT_DEBUGGER_ID = "NullDebugger";

    /**
     *
     */
    static public $DEFAULT_LOGGER_ID = "NullLogger";

    /**
     *
     */
    static public $DEFAULT_MANAGER_PACKAGE = "goliatone.loggin.managers.LoggerManager";



    /** @private **/
    protected $_id;

    /** @private **/
    public $mode = LoggerConfig::DEBUG;

    /** @private **/
    private $_config;

    /** @private **/
    private $_setup;

    /** @private **/
    public $manager;

    /** @private **/
    public $logger;

    /** @private **/
    public $debugger;

    /** @private **/
    public $threshold;

    /** @private **/
    public $publishers;

    /** @private **/
    public $levelFilters;

    /** @private **/
    public $disabledLevels;

    /** @private **/
    protected $disabledPackages;
        
    /**
     *
     * @param   id
     */
    public function setId( /*:String*/$id )
    {
        $this->_id = $id;
    }

    /**
     * TODO: We have different deserializers => YAML/JSON/DDBB/XML/ETC
     * @param   xml
     */
    public function load( $config )
    {
        //TODO: Implement!!
        $this->reset();
        
        $this->_config = $config;
        
        if(array_key_exists('mode', $config)) $this->_mode = $config['mode'];
        
        $this->_setup = $config[$this->_mode];
        
        //List only the public props. If we do it from inside class we get
        //all props.
        $fields = function($obj) { return get_class_vars($obj);};
        foreach($fields as $field)
        {
            if(array_key_exists($field, $config))
            {
                $value = $config[$field];
                $setter = "set".ucfirst($field);
                if(method_exists($this, $setter)) $setter($value);
                else $this->{$field} = $value;
            }
        }
    }

    /**
     *
     * @param   mode
     */
    public function setMode( /*:String*/$mode )
    {
        $this->mode = $mode;
    }

    /**
     *
     */
    public function reset( )
    {
        $this->_config = NULL;
        $this->_setup  = NULL;

        $this->publishers       = array();
        $this->levelFilters     = array();
        $this->disabledLevels   = array();
        $this->disabledPackages = array();
        
        $this->mode = LoggerConfig::DEBUG;
        $this->threshold = LogLevel::ALL;
        $this->logger = LoggerConfig::$DEFAULT_LOGGER_ID;
        $this->debugger = LoggerConfig::$DEFAULT_DEBUGGER_ID;
        $this->manager = LoggerConfig::$DEFAULT_MANAGER_PACKAGE;
    }
    
    public function setDisabledLevels($levels)
    {
        $levels = explode(',', $levels);
        $this->disabledLevels = $levels;    
    }
    
    /**
     *
     */
    public function getManagerPackage( )
    {
        return $this->manager;
    }

    /**
     *
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     *
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     *
     */
    public function getLevelFilters()
    {
        return $this->levelFilters;
    }

    /**
     *
     */
    public function getDisabledLevels()
    {
        return $this->disabledLevels;
    }

    /**
     *
     */
    public function getDisabledPackages()
    {
        return $this->disabledPackages;
    }

    /**
     *
     */
    public function getDebugger()
    {
        return $this->debugger;
    }

    /**
     *
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @return Boolean
     */
    public function getNotNullDebugger()
    {
         return $this->debugger != self::$DEFAULT_DEBUGGER_ID;
    }

    /**
     *
     * @return Boolean
     */
    public function getNotNullLogger()
    {
        return $this->logger != self::$DEFAULT_LOGGER_ID;        
    }
}