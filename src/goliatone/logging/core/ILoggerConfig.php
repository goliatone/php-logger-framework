<?php


interface ILoggerConfig
{
    /**
     *
     * @param   id
     */
    public function setId( /*:String*/$id );

    /**
     * TODO: We have different deserializers => YAML/JSON/DDBB/XML/ETC
     * @param   $config
     */
    public function load( $config );

    /**
     *
     * @param   mode
     */
    public function setMode( /*:String*/$mode );

    /**
     *
     */
    public function reset( );

    /**
     *
     */
    public function getManagerPackage( )/*:String*/;

    /**
     *
     */
    public function getThreshold()/*:String*/;

    /**
     *
     */
    public function getPublishers()/*:Array*/;

    /**
     *
     */
    public function getLevelFilters()/*:Array*/;

    /**
     *
     */
    public function getDisabledLevels()/*:Array*/;

    /**
     *
     */
    public function getDisabledPackages()/*:Array*/;

    /**
     *
     */
    public function getDebugger()/*:String*/;

    /**
     *
     */
    public function getLogger()/*:String*/;

    /**
     *
     */
    public function getNotNullDebugger()/*:Boolean*/;

    /**
     *
     */
    public function getNotNullLogger()/*:Boolean*/;
}