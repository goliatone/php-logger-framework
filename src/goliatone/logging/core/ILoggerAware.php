<?php namespace goliatone\flatg\logging\core {


    /**
     * Interface ILoggerAware
     * @package goliatone\flatg\logging\core
     */
    interface ILoggerAware
    {
        /**
         * Sets a logger instance.
         *
         * @param  ILogger $logger
         * @return ILogger
         */
        public function setLogger(ILogger $logger);
    }
}