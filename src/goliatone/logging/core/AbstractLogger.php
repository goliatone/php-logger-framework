<?php namespace goliatone\flatg\logging\core {



    /**
     * TODO Create BaseObject that implements IConfigurable, IFiltrable
     *
     * Class AbstractLogger, base implementation.
     * It will delegate all level specific methods
     * to the `log` method.
     *
     * @package goliatone\flatg\logging\core
     */
    abstract class AbstractLogger extends CoreObject implements ILogger
    {
        /**
         * @var bool
         */
        static public $DISABLED = FALSE;

        /**
         * Logs with an arbitrary level.
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         * @return null
         */
        abstract public function log($level, $message, array $context = array());

        /**
         * Detailed debug information.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function debug($message, array $context = array())
        {
            $this->log(LogLevel::$DEBUG, $message, $context);
        }

        /**
         * Interesting events.
         *
         * Example: User logs in, SQL logs.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function info($message, array $context = array())
        {
            $this->log(LogLevel::$INFO, $message, $context);
        }

        /**
         * Normal but significant events.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function notice($message, array $context = array())
        {
            $this->log(LogLevel::$NOTICE, $message, $context);
        }

        /**
         * Exceptional occurrences that are not errors.
         *
         * Example: Use of deprecated APIs, poor use
         * of an API, undesirable things that are not
         * necessarily wrong.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function warning($message, array $context = array())
        {
            $this->log(LogLevel::$WARNING, $message, $context);
        }

        /**
         * Runtime errors that do not require immediate
         * action but should typically be logged and
         * monitored.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function error($message, array $context = array())
        {
            $this->log(LogLevel::$ERROR, $message, $context);
        }

        /**
         * Critical conditions.
         *
         * Example: Application component unavailable,
         * unexpected exception.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function critical($message, array $context = array())
        {
            $this->log(LogLevel::$CRITICAL, $message, $context);
        }

        /**
         * Action must be taken immediately.
         *
         * Example: Entire website down, database
         * unavailable, etc. This should trigger
         * alerts and email you, SMSs you. Slap
         * you in the face?
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function alert($message, array $context = array())
        {
            $this->log(LogLevel::$ALERT, $message, $context);
        }

        /**
         * System is unusable.
         *
         * @param string $message
         * @param array $context
         * @return null
         */
        public function emergency($message, array $context = array())
        {
            $this->log(LogLevel::$EMERGENCY, $message, $context);
        }

    }
}