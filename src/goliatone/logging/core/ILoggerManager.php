<?php
namespace goliatone\logging\core;

interface ILoggerManager
{
    /**
     * 
     * @param   debugger
     */
    public function setDebugger( /*Class*/ $debugger );

    /**
     * 
     * @param   classType
     */
    public function setLogger( /*Class*/ $logger );

    /**
     * 
     * @param   classType
     */
    public function removeLogger( $classType );

    /**
     * REVISION Add support for *
     * @param   ...packages
     */
    public function disablePackages( $packages /*...*/ );

    /**
     * 
     * @param   String pk
     * @param   level
     * @param   extrict
     */
    public function addLevelFilter( $pk, ILogLevel $level, $extrict = FALSE );

    /**
     * 
     * @param   threshold
     */
    public function setGlobalThreshold( ILogLevel $threshold );


    /**
     *
     * @param   ...levels
     */
    public function disableLevels( $levels /*...*/ );

    /**
     *
     * @param   ...levels
     */
    public function enableLevels( $levels /*...*/ );

    /**
     * Factory method to create loggers. We use call it from <code>Logger.instance</code>
     *
     * @param   classType
     * @return Logger
     * @see com.enjoymondays.logging.loggers.Logger#create( )
     *
     * @internal
     */
    public function getLogger( $classType )/*ILogger*/;

    /**
     * 
     * @param   publisher
     * @param   formater
     */
    public function addPublisher( ILoggerPublisher $publisher, ILoggerFormatter $formater );

    /**
     *
     * @param   logger
     * @internal
     */
    public function makeActiveLogger( ILogger $logger = NULL );

    /**
     * 
     * @param   name
     * @param   loggerPackage
     * @param   level
     * @return
     */
    public function loggerDisabled( $name, $loggerPackage, ILogLevel $level )/*Boolean*/;
    
} 