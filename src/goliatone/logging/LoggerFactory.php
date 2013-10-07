<?php
namespace goliatone\logging;

use goliatone\logging\core\ILoggerFactory;

/**
 * 
 */
class LoggerFactory implements ILoggerFactory {
	private static $LF;
    
    protected $_mamanger;
    protected $_config;
    
	private function __construct($argument) {
		
	}
    
    static public function getInstance()
    {
        return self::$LF || (self::$LF = new LoggerFactory());    
    }
    
    /**
     * Returns the logger for the specified class. If classType is a String, that
     * string is used as the class name.
     * For any other object, the class type of that object is used.
     * Most commonly, a Class object would be used.
     *
     * <p>If this is called multiple times for the
     * same classType, the same logger will be returned rather than creating a new
     * one.
     * </p>
     */
    static public function instance($id)
    {
        return self::$LF->getLogger($id);
    }
    
    public function setManager(ILoggerManager $manager)
    {
        $this->_manager = $manager;
    }
    
    public function buildConfig( $config ) /*ILoggerConfig*/
    {
        return $this->_config->buildConifg($config);
    }
    
    public function configure( ILoggerConfig $config ) 
    {

        /*
         * Let's set the used logger:
         * - NullLogger
         * - DefaultLogger
         */
        if ( $config->notNullLogger ) 
        {
            $Logger = $config->logger;
            $this->_manager.setLogger( $Logger );
            //Debug.trace( getQualifiedClassName( DEBUGGER ) );
        }


        /*
         * Let's set the used debugger:
         * - NullDebugger
         * - DefaultDebugger
         */
        if ( $config->notNullDebugger ) 
        {
            $Debugger = $config->debugger;
            $this->_manager.setDebugger( $Debugger );
        }

        /*
         * Create all publishers:
         */
        $publishers = $config->publishers;


        foreach( $publishers as $publisher ) 
        {

            $Target   = $publisher->targetBuild;
            $Formatter = $publisher->formatterBuild;
            $delayed  = $publisher->delayed;
            $patterns = $publisher->formaterPatterns;

            /*
             *
             */
            $target = new $Target( $delayed );
            $target->configure( $publisher->config );

            /*
             * We can configure our formater.
             */
            $formatter = new $Formatter( );
            $formatter->setLevelPatterns( $patterns );

            /*
             * REVISION create instance, configure instance, and pass that.
             */
            $this->_manager->addPublisher( $target, $formatter );

         }

            /*
             * Set global threshold.
             */
            $threshold = strtoupper($config->threshold);
            $this->_manager->setGlobalThreshold( $threshold );

            /**
             * Set disabled levels.
             */
            $disabledLevels = $config->disabledLevels;
            $this->_manager->disableLevels( $disabledLevels );


            /*
             * Set disabled packages:
             */
            $disablePackages = $config->disabledPackages;
            $this->_manager->disablePackages( $disablePackages );

        }

    /**
     * 
     */
    public function getLogger($forItem)/*ILogger*/ 
    {
        return $this->_manager->getLogger( $forItem );
    }
    
}
