<?php namespace goliatone\flatg\logging\augmenters {

    use goliatone\flatg\logging\core\ILogAugmenter;
    use goliatone\flatg\logging\core\LogMessage;

    /**
     * Class ExceptionAugmenter.
     * If it
     *
     * @package goliatone\flatg\logging\augmenters
     */
    class ExceptionAugmenter implements ILogAugmenter
    {
        /**
         * @param LogMessage $message
         * @return LogMessage
         */
        public function process(LogMessage $message)
        {
            // TODO: Implement process() method.
        }

    }
}