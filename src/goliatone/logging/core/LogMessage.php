<?php namespace goliatone\flatg\logging\core {

    use ArrayIterator;
    use \ArrayObject;
    use goliatone\flatg\logging\core\LogLevel;
    use goliatone\flatg\logging\Debugger;
    use goliatone\flatg\logging\helpers\Utils;

    /**
     * TODO: Make it extend ArrayObject
     *
     * Class LogMessage
     *
     * Every time we set a property using one of its
     * setters we set a flag marking the object as dirty
     * and needing of another rendering- call to updateMessage.
     *
     * If we set a property using Array like notation we
     * do not set the flat, so the change goes unnoticed.
     *
     * @method setLevel() setLevel(LogLevel $level)
     * @method string getLevel()
     * @method setLogger() setLogger(\string $logger)
     * @method string getLogger()
     * @method setMessage() setMessage(\string $message)
     * @method string getMessage()
     * @method string getRawMessage()
     * @method setStackTrace() setStackTrace(StackTrace $stackTrace)
     * @method string getStackTrace()
     * @method string setTimestamp() setTimestamp(\DateTime $timestamp)
     * @method string getTimestamp()
     * @method setAddress() setAddress(\string $machine)
     * @method string getAddress()
     *
     * @package goliatone\events\core
     */
    class LogMessage extends ArrayObject
    {


        /**
         * @var string
         */
        protected $_rawMessage = '';

        /**
         * When solving the string template and
         * a token is not found in context,
         * should we remove it from final output
         * or remove it?
         *
         * @var bool Default FALSE
         */
        public $consumeUnusedTokens = FALSE;

        /**
         * Flag indicating if new data has
         * been added suggesting that the message
         * might need an update render.
         *
         * @var bool
         */
        protected $_isDirty = FALSE;


        /**
         * @var array
         */
        protected $_context = array();


        /**
         * @param $level
         * @param $message
         * @param array $context
         * @param $name
         * @param int $stackTraceSkip
         * @return LogMessage
         */
        static public function factory($level, $message, array $context = array(), $name, $stackTraceSkip=3)
        {
            $log = new LogMessage($level, $message, $context, FALSE);
            $log->setLogger($name);

            $log->setTimestamp(new \DateTime('NOW'));

            $stackTrace = Debugger::backtrace($stackTraceSkip);
            $log->setStackTrace($stackTrace);

            $address = Utils::getServerAddress( );
            $log->setAddress($address);

            $log->updateMessage();

            return $log;
        }

        /**
         * @param LogLevel $level
         * @param string $message
         * @param array $context
         * @param bool $autoRender
         */
        function __construct($level, $message, array $context = array(), $autoRender = TRUE)
        {

            //TODO: We should integrate extras/content with __call and
            // use @method for set{$property} if property_exists($this, '_'.$property)
            // make sure we keep both setters and offsetSet in sync.

            $this->_context    = $context;
            $this->_rawMessage = $message;

            parent::__construct($this->_context,
                                \ArrayObject::STD_PROP_LIST |
                                \ArrayObject::ARRAY_AS_PROPS
                               );

            $this->setLevel($level);
            $this->setMessage($message);
            if($autoRender) $this->updateMessage();
        }

        /**
         * @param $name
         * @param $arguments
         * @return mixed
         */
        function __call($name, $arguments)
        {
            if(preg_match("/^(set|get)/i", $name))
            {
                $prefix = substr($name, 0, 3);
                $key    = strtolower(substr($name, 3));
                switch($prefix)
                {
                    case 'set':
                        $value = $arguments[0];
                        $this->offsetSet($key, $value);
                        $this->_isDirty = TRUE;
                        break;
                    case 'get':
                        //Access prop stored
                        if($this->offsetExists($key))
                            return $this->offsetGet($key);
                        //We passed a default value on get{$name}($default)
                        if(count($arguments) > 0)
                            return $arguments[1];
                    default:
                        print('Upsy! The method is not defined, so I don\'t know what to do!');

                }
            }
        }

        /**
         * @return array
         */
        public function getContext()
        {
            //TODO: How expensive is this?
            return $this->getArrayCopy();
        }

        /**
         *
         */
        public function updateMessage()
        {
            //TODO, should we implement stringTemplate here and remove dep on Utils?
            $message = Utils::stringTemplate($this->getMessage(), $this->getContext(), $this->consumeUnusedTokens);
            $this->setMessage($message);
            $this->_isDirty = FALSE;
        }
    }
}
