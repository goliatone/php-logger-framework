<?php namespace goliatone\flatg\logging\core {

    interface ILogAugmenter
    {

        /**
         * @param LogMessage $message
         * @return LogMessage
         */
        public function process(LogMessage $message);
    }
}