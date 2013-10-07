<?php
namespace goliatone\logging\core;


interface ILoggerFactory
{
    /**
     * Retrieves a logger for the specified class. If classType is a string, that
     * string is used as the class name, regardless of what it is. For any other
     * object, the class type of that object is used.
     * Usually we pass a Class object, but can use an instance as well.
     *
     * <p>If this method is called multiple times for the same classType,
     * the same logger will be returned rather than creating a new one.</p>
     */
    public function getLogger( $forItem );
    
}