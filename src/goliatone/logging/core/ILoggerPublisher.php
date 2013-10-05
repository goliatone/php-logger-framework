<?php

namespace goliatone\logging\core;



interface IloggerPublisher 
{
    /**
     * 
     */
    public function configure($config);   
    
    /**
     * 
     */
    public function publish($message);
}