<?php namespace goliatone\flatg\logging\formatters\transformers {

    use goliatone\flatg\logging\core\ConfigurableObject;
    use goliatone\flatg\logging\helpers\Utils;
    use goliatone\flatg\logging\core\ILogMessageFormatTransformer;

    /**
     * Class BaseTransformer
     * @package goliatone\flatg\logging\formatters\transformers
     */
    class BaseTransformer extends ConfigurableObject implements ILogMessageFormatTransformer
    {
        /**
         * @var array
         */
        protected $_extraArguments = array();

        /**
         * @var null
         */
        protected $_type = null;

        /**
         * @var
         */
        protected $_delegated;

        /**
         * @param $type
         */
        public function __construct($type)
        {
            $this->_type = $type;
            $this->_delegated = 'transform';
        }

        /**
         * @return null
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return Utils::qualifiedClassName($this, false);
        }

        /**
         * @param $resource
         * @param null $provider
         * @return mixed|void
         */
        public function transform($resource, $provider = NULL)
        {

        }

        /**
         * @param $resource
         * @param $provider
         * @return mixed
         */
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