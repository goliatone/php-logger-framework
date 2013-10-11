<?php

namespace goliatone\logging\core;

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
    private $_mode = LoggerConfig::DEBUG;

    /** @private **/
    private $_config;

    /** @private **/
    private $_setup;

    /** @private **/
    private $_managerPackage;

    /** @private **/
    protected $_logger;

    /** @private **/
    protected $_debugger;

    /** @private **/
    protected $_threshold = "ALL";

    /** @private **/
    protected $_publishers;

    /** @private **/
    protected $_levelFilters;

    /** @private **/
    protected $_disabledLevels;

    /** @private **/
    protected $_disabledPackages;
        
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
        
    }

    /**
     *
     * @param   mode
     */
    public function setMode( /*:String*/$mode )
    {
        $this->_model = $mode;
    }

    /**
     *
     */
    public function reset( )
    {
        $this->_config = NULL;
        $this->_setup  = NULL;

        $this->_publishers       = array();
        $this->_levelFilters     = array();
        $this->_disabledLevels   = array();
        $this->_disabledPackages = array();
        
        $_logger = LoggerConfig::$DEFAULT_LOGGER_ID;
        $_debugger = LoggerConfig::$DEFAULT_DEBUGGER_ID;
        $_managerPackage = LoggerConfig::$DEFAULT_MANAGER_PACKAGE;
    }

    /**
     *
     */
    public function getManagerPackage( )
    {
        return $this->_managerPackage;
    }

    /**
     *
     */
    public function getThreshold()
    {
        return $this->_threshold;
    }

    /**
     *
     */
    public function getPublishers()
    {
        return $this->_publishers;
    }

    /**
     *
     */
    public function getLevelFilters()
    {
        return $this->_levelFilters;
    }

    /**
     *
     */
    public function getDisabledLevels()
    {
        return $this->_disabledLevels;
    }

    /**
     *
     */
    public function getDisabledPackages()
    {
        return $this->_disabledPackages;
    }

    /**
     *
     */
    public function getDebugger()
    {
        return $this->_debugger;
    }

    /**
     *
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     *
     * @return Boolean
     */
    public function getNotNullDebugger()
    {
         return $this->_debugger != self::$DEFAULT_DEBUGGER_ID;
    }

    /**
     *
     * @return Boolean
     */
    public function getNotNullLogger()
    {
        return $this->_logger != self::$DEFAULT_LOGGER_ID;        
    }
}