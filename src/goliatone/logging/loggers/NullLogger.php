<?php namespace goliatone\flatg\logging\loggers {

    use goliatone\flatg\logging\core\AbstractLogger;

    class NullLogger extends DefaultLogger
    {

        /**
         * This is a NOOP but with style.
         * Instead of doing conditionals we
         * just let the messages go...
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         * @return null
         */
        public function log($level, $message, array $context = array())
        {
            //NOTHING HERE, THIS IS A NullLogger after all.
        }
    }
}