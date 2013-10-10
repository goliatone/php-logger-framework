<?php
namespace goliatone\logging\core;

interface IPublisherVO
{
   /**
     *
     */
    public function getPublisher()/*:ILoggerPublisher*/;
    /** @private **/
    public function setPublisher(ILoggerPublisher $value);
    
    /**
     *
     */
    public function getFormater()/*:ILoggerFormatter*/;
    /** @private **/
    public function setFormater(ILoggerFormatter $value);
    
    /**
     *
     */
    public function getDelayed()/*:Boolean*/;
    /** @private **/
    public function setDelayed($value);
    
    /**
     *
     */
    public function getTargetBuild()/*:String*/;
    /** @private **/
    public function setTargetBuild($value/*:String*/);
    
    /**
     *
     */
    public function getFormaterBuild()/*:String*/;
    /** @private **/
    public function setFormaterBuild($value/*:String*/);
    
    /**
     *
     */
    public function getFormaterPatterns()/*:Array*/;
    /** @private **/
    public function setFormaterPatterns($value);
    
    /**
     *
     */
    public function getConfig( );
    /** @private **/
    public function setConfig($config);
    
    
    /**
     *
     * @return
     */
    public function toString()/*:String*/;
}