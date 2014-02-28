<?php namespace goliatone\flatg\logging\augmenters {

    use goliatone\flatg\logging\core\LogMessage;
    use goliatone\flatg\logging\core\ILogAugmenter;

    /**
     * TODO: DRY this, BaseTransformer has same __invoke cruft
     *
     * Class CallbackAugmenter
     * @package goliatone\flatg\logging\augmenters
     */
    class CallbackAugmenter implements ILogAugmenter
    {
        protected $_extraArguments = array();
        protected $_delegated;

        /**
         * @var
         */
        protected $_callable;

        /**
         * @param $callable
         */
        public function __construct($callable)
        {
            $this->wrap($callable);
            $this->_delegated = 'wrap';
        }

        /**
         * @param callable $callable
         */
        public function wrap(callable $callable)
        {
            $this->_callable = $callable;
        }

        /**
         * @param LogMessage $message
         * @return LogMessage
         */
        public function process(LogMessage $message)
        {
            return call_user_func($this->_callable, $message);
        }

        public function __invoke($resource, $provider)
        {
            $args = func_get_args();

            if(sizeof($args))
            {
                //Start at index 1 to the end, and preserve numeric array indices.
                $this->_extraArguments = array_slice($args, 2, NULL, TRUE);
            }

            return call_user_func_array(array($this, $this->_delegated), $args);
        }
    }
}