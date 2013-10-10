<?php
namespace goliatone\logging\core;


interface IStackTrace
{
    /**
     * 
     */
    public function getMethodName()/*:String*/;
    /** @private **/
    public function setMethodName(/*:String*/$value)/*:void*/;
    
    /**
     * 
     */
    public function getClassName()/*:String*/;
    /** @private **/
    public function setClassName(/*:String*/$value)/*:void*/;
    
    /**
     * 
     */
    public function getLine()/*:String*/;
    /** @private **/
    public function setLine(/*:String*/$value)/*:void*/;
    
    /**
     * 
     */
    public function getFile()/*:String*/;
    /** @private **/
    public function setFile(/*:String*/$value)/*:void*/;
    
    /**
     * 
     */
    public function getRaw()/*:String*/;
    /** @private **/
    public function setRaw(/*:String*/$value)/*:void*/;
    
    /**
     * 
     * @return
     */
    public function toString( )/*:String*/;
}
