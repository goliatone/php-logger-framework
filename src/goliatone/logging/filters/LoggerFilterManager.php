<?php
namespace goliatone\logging\filters;

use goliatone\logging\filters\LoggerFilter;
use goliatone\logging\core\ILoggerFilterManager;
use goliatone\logging\core\LogLevel;

class LoggerFilterManager implements ILoggerFilterManager
{
    /** @private **/
    protected $_filters;

    /** @private **/
    private $_rootFilter;

    /**
     * <code>LoggerFilterManager</code>
     * com.enjoymondays.logging.filters.LoggerFilterManager
     *
     */
    public function __construct() 
    {
        $this->_rootFilter = new LoggerFilter( "root", LogLevel::ALL );
        $this->_rootFilter->_isRoot = TRUE;
    }

    /**
     *
     * @param   pk
     * @param   level
     * @return  Boolean
     */
    public function isFiltered( $pk, ILogLevel $level ) 
    {
        $f = $this->getLogFilterFor( $pk );
        //Debug.trace("PACKAGE " + pk + " LEVEL " + level.name +", FILTER "+f.level.name, Debug.LEVEL_ERROR );
        //return f.level.filters( level );
        return $f->filter( $level );
    }

    /**
     *
     * @param   pk
     * @param   level
     */
    public function addLogFilter( $pk, ILogLevel $level, $extrict = FALSE ) 
    {
        if ( array_key_exists($pk, $this->_filters)) return;
        $filter = new LoggerFilter( $pk, $level );
        $filter->extrict = $extrict;
        $filter->setManager( $this );
        $this->_filters[ $pk ] = $filter;

    }

    /**
     *
     * @param   pk
     * @return  Boolean
     */
    public function contains( $pk ) 
    {
        return array_key_exists($pk, $this->_filters);
    }

    /**
     *
     * @param   pk
     * @param   level
     */
    public function update( $pk, ILogLevel $level )
    {
        $filter = $this->_filters[ $pk ];
        $filter->setLevel( $level, $this );
    }

    /**
     *
     * @param   name
     * @return  ILoggerFilter
     */
    public function getLogFilterFor( $name )
    {
        
        while( array_key_exists($name, $this->_filters) === FALSE) {
            /*
             * if there's no dot, this was a top level package,
             * so the only thing left is the root
             */
            $dotIndex = $name.lastIndexOf(".");
            if ( $dotIndex === -1 ) return $rootFilter;

            // search the next package up
            $name = $name.substring( 0, $dotIndex );
        }

        return $this->_filters[ name ];
    }



    //public function
    /**
     * @private ILoggerFilter
     */
    private function getRootFilter( ) { return $this->_rootFilter; }    
}