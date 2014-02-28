<?php namespace goliatone\flatg\logging\formatters\transformers {

    //We do register defaults, but we should be able to do it from outside.

    use goliatone\flatg\logging\core\ILogMessageFormatTransformer;
    use goliatone\flatg\logging\formatters\transformers\ArrayTransformer;
    use goliatone\flatg\logging\formatters\transformers\ObjectTransformer;
    use goliatone\flatg\logging\formatters\transformers\ResourceTransformer;


    /**
     * Handles basic string conversion of different types:
     *  "boolean"
     *  "integer"
     *  "double"
     *  "string"
     *  "array"
     *  "object"
     *  "resource"
     *  "NULL"
     *  "unknown type"
     *
     * Class TransformManager
     * @package goliatone\flatg\logging\formatters\transformers
     */
    class TransformManager extends BaseTransformer
    {
        /**
         * @var array
         */
        protected $_handlers = array();

        /**
         * TODO: Move all config management to base class.
         * @var array
         */
        protected $_configs = array();

        /**
         * @param array $config
         */
        public function __construct($config=array())
        {
            parent::__construct("TransformerManager");

            $this->initialize($config);
        }

        /**
         * @param array $config
         */
        public function initialize($config = array())
        {
            $this->registerDefaultHandlers();
            $this->configure($config);
        }

        /**
         *
         */
        public function registerDefaultHandlers()
        {
            $this->register('double',  function($value){return sprintf("%f", $value);});
            $this->register('integer', function($value){return sprintf("%d", $value);});

            $this->register('string',  function($value){ return $value;});
            $this->register('boolean', function($value){ return $value === TRUE ? "TRUE" : "FALSE";});
            $this->register('NULL',    function(){ return "NULL";});
            $this->register('unknown type', function(){ return "unknown type";});

            $this->register('array',    new ArrayTransformer('array'));
            $this->register('resource', new ResourceTransformer('resource'));
            $this->register('object',   new ObjectTransformer('object'));
            $this->register('DateTime', new DateTimeTransformer('DateTime'));

            $this->register('default', function($value){return print_r($value, TRUE);});
        }

        //TODO: Use __invoke on BaseTransformer...
        /**
         * @param $type
         * @param $Class
         * @return $this
         */
        protected function wrapClass($type, $Class)
        {
            $handler = function($value, $provider) use($type, $Class){

                try{
                    $handler = new $Class($type);
                    return $handler->transform($value, $provider);
                } catch(\Exception $e) {
                    return $e->getMessage();
                }
            };

            return $this->register($type, $handler);
        }

        /**
         * @param $type
         * @param callable|Class $handler
         * @throws \InvalidArgumentException
         * @return $this
         */
        public function register($type, $handler)
        {
            if(!is_callable($handler)) throw new \InvalidArgumentException("Provided handler for {$type} has to be callable");

            if(is_callable($handler) && !($handler instanceof ILogMessageFormatTransformer))
            {
                $handler = new CallbackWrapperTransformer($type, $handler);
            }

            //We overwrite other handlers.
            $this->_handlers[$type] = $handler;
            return $this;
        }

        /**
         * @param array $context
         * @return array
         */
        public function parseContext(array $context=array())
        {
            return array_map(array($this, 'transform'), $context);
        }

        /**
         * @param $resource
         * @param null $provider
         * @param array|null $options
         * @return mixed|string|void
         */
        public function transform($resource, $provider = NULL, array $options = array())
        {
            //If we did not get handled a provider, we are root.
            $provider || ($provider = $this);

            $type = gettype($resource);

            $handler = $this->getHandler($type);
            if($options) $handler->configure($options);

            try {
                $value = $handler->transform($resource, $provider);
            } catch(\Exception $e) {
                $value = $e->getMessage();
                return $value;
            }

            return $value;
        }

        public function configHandler($type, $config=array())
        {
            $options = $this->setConfig($type, $config);
            $handler = $this->getHandler($type);
            $handler->configure($options);
            return $this;
        }

        public function setConfig($for, $config =array(), $isGlobal = FALSE)
        {
            $defaults = $this->getConfig($for, $config);
            $this->_configs[$for] = array_replace_recursive($defaults, $config);
            return $this->_configs[$for];
        }

        public function getConfig($type, $defaults = array(), $isGlobal=FALSE)
        {
            if(!$this->hasConfig($type, $isGlobal)) return $defaults;
            return array_replace_recursive($defaults, $this->_configs[$type]);
        }

        public function hasConfig($type, $isGlobal = FALSE)
        {
            return array_key_exists($type, $this->_configs);
        }

        /**
         * @param $type
         * @return bool
         */
        public function hasHandler($type)
        {
            return array_key_exists($type, $this->_handlers);
        }

        /**
         * @param $type
         * @param string $default
         * @return BaseTransformer
         */
        public function getHandler($type, $default = 'default')
        {
            if(! $this->hasHandler($type)) $type = $default;

            return $this->_handlers[$type];
        }


    }
}