<?php

namespace goliatone\logging\core;

interface ILoggerFilter
{
    /**
     *
     */
    public function getParent( )/*:ILoggerFilter*/;

    /**
     *
     * @param   logger
     * @param   level
     * @return
     */
    public function filter( ILogLevel $level )/*:Boolean*/;

    /**
     *
     */
    public function getPriority( )/*:int*/;


    public function setLevel( ILogLevel $level, ILoggerFilterManager $lock )/*:void*/;

    public function getLevel( )/*:ILogLevel*/;

    public function getFilterPackage( )/*:String*/;

    /**
     *
     * @return
     */
    public function toString( )/*:String*/;   
}