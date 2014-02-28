<?php namespace goliatone\flatg\logging\formatters\transformers {


    /**
     * Class CallbackWrapperTransformer
     * @package goliatone\flatg\logging\formatters\transformers
     */
    class CallbackWrapperTransformer extends BaseTransformer
    {
        /**
         * @var null
         */
        protected $_handler = null;

        /**
         * @param $type
         * @param null $handler
         */
        public function __construct($type, $handler=null)
        {
            parent::__construct($type);

            if($handler) $this->setCallback($handler);
        }

        /**
         * @param $handler
         */
        public function setCallback($handler)
        {
            $this->_handler = $handler;
        }

        /**
         * @param $resource
         * @param null $provider
         * @param array $options
         * @return mixed
         */
        public function transform($resource, $provider = NULL)
        {
            return @call_user_func($this->_handler, $resource, $provider);
        }
    }
}