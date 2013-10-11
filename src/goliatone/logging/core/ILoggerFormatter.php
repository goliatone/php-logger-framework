<?php

namespace goliatone\logging\core;

interface ILoggerFormatter
{
    /**
     * 
     * @param   message
     * @return
     */
    public function format( ILogMessage $message )/*:String*/;

    /**
     * 
     * @param   ...patterns
     */
    public function setLevelPatterns( /*...*/ $patterns );

    /**
     * 
     * @param   level
     * @return  String
     */
    public function getPatternFor( $level/*:String*/ );


    /**
     * 
     * @param   level
     * @return  Boolean
     */
    public function isStackNeeded( $level/*:String*/ );

    /**
     * 
     */
    public function getDefaultPattern()/*:String*/;
    /** @private **/
    public function setDefaultPattern($value/*:String*/);

}