<?php 
namespace goliatone\logging\core;

interface ILogMessage
{
    /**
     *
     */
    public function setLogger( $logger );

    /**
     *
     */
    public function getLogger( );

    /**
     *
     */
    public function setMessage( $message );

    /**
     *
     */
    public function getMessage( );

    /**
     *
     */
    public function setLevel( LogLevel $level );

    /**
     *
     */
    public function getLevel( )/*LogLevel*/;

    /**
     *
     */
    public function setTimestamp( $t );

    /**
     *
     */
    public function getTimestamp( );

    /**
     * TODO: Change to setContext
     */
    public function setObject( $o );

    /**
     *
     */
    public function getObject( );

    /**
     *
     */
    public function getLine( );

    /**
     *
     */
    public function getMethodName( );

    /**
     *
     */
    public function getClassName( );

    /**
     *
     */
    public function getStackTrace( )/*IStackTrace*/;

    /**
     *
     */
    public function setStackTrace( IStackTrace $stackTrace);

    /**
     * 
     */
    public function getFile( );

    // public function clone( ):ILogMessage;
}