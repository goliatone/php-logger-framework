<?php

namespace goliatone\logging\core;

interface ILoggerFilterManager
{
    /**
     *
     * @param   pk
     * @param   level
     * @return  Boolean
     */
    public function isFiltered( /*String*/ $pk, ILogLevel $level );


    /**
     *
     * @param   name
     * @return  ILoggerFilter
     */
    public function getLogFilterFor( String $name )/*:ILoggerFilter*/;

    /**
     *
     * @param   pk
     * @param   level
     * @param   extrict
     */
    public function addLogFilter( /*String*/ $pk, ILogLevel $level, $extrict = FALSE );

    /**
     *
     * @param   pk
     * @param   level
     */
    public function update( /*String*/ $pk, ILogLevel $level );

    /**
     *
     * @param   pk
     * @return  Boolean
     */
    public function contains( /*String*/ $pk );
}