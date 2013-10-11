<?php
namespace goliatone\logging\filters;

use goliatone\logging\core\ILoggerFilter;

class LoggerFilter implements ILoggerFilter 
{
    /** @private **/
    protected $_priority = 0;

    /** @private **/
    protected $_package;

    /** @private  ILogLevel**/
    protected $_level;

    /** @private **/
    protected $_manager;

    /** @private **/
    protected $_enabled = TRUE;

    /** @private **/
    protected $_extrict = FALSE;

    /** @private **/
    protected $_isRoot = FALSE;
    
    /**
     * 
     */
    public function __construct( $packageFilter, ILogLevel $level, $enabled = TRUE ) 
    {
        $this->_level   = $level;
        $this->_enabled = $enabled;
        $this->_package = $packageFilter;
    }
    
    public function getParent( )/*:ILoggerFilter*/ 
    {
        if ( $this->_isRoot ) return NULL;
        $name = implode('.', $this->_package);
        return $this->_manager->getLogFilterFor( $name[0] );
    }

    /**
     *
     * @param   l
     */
    public function setLevel( ILogLevel $level, ILoggerFilterManager $lock )
    {
        $this->_level = $level;
    }

    /**
     *
     * @param   $mananger
     */
    public /*internal*/ function setManager( ILoggerFilterManager $mananger ) 
    {
        $this->_manager = $mananger;
    }

    public function filter( ILogLevel $level )
    {
        return $this->_level->filters( $level, $this->getExtrict() );
        //if ( _manager.isDisabledFor( level ) ) return true;
        //if ( packageFilter && name.indexOf( packageFilter ) == -1 ) return true;
        //if ( _manager.notActive( this ) ) return true;
        //if ( _level.filters( level ) )        return true;
        //return false;
    }

    /**
     *
     * @return
     */
    public function __toString( ) {
        return "[ LoggerFilter => level: " + $this->level + ", filterPackage: " + $this->filterPackage + "]";
    }

    /**
     *
     */
    public function getPriority( ) { return $this->_priority; }

    /**
     *
     */
    public function getLevel() { return $this->_level; }

    /**
     * @return String
     */
    public function getFilterPackage() { return $this->_package; }

    /**
     *
     * @return Boolean
     */
    public function getExtrict() { return $this->_extrict; }

    /**
     *
     */
    public function setExtrict(/*Boolean*/ $value) {
        $this->_extrict = value;
    }

}