<?php namespace goliatone\flatg\logging\loggers {

    use \DateTime;

    use goliatone\flatg\logging\Debugger;
    use goliatone\flatg\logging\helpers\Utils;
    use goliatone\flatg\logging\core\LogLevel;
    use goliatone\flatg\logging\core\LogMessage;
    use goliatone\flatg\logging\core\ILoggerAware;
    use goliatone\flatg\logging\filters\LogFilter;
    use goliatone\flatg\logging\core\ILogAugmenter;
    use goliatone\flatg\logging\core\ILogPublisher;
    use goliatone\flatg\logging\core\AbstractLogger;
    use goliatone\flatg\logging\core\ILogMessageFormatter;
    use goliatone\flatg\logging\augmenters\CallbackAugmenter;
    use goliatone\flatg\logging\publishers\CompoundPublisher;

    /**
     * Class DefaultLogger
     * @package goliatone\flatg\logging\loggers
     */
    class DefaultLogger extends AbstractLogger
    {

        /**
         * @var CompoundPublisher
         */
        protected $_publisher;

        /**
         * @var array
         */
        protected $_augmenters = array();

        /**
         * @var bool
         * @access protected
         */
        protected $_enabled = TRUE;

        /**
         * @var LogLevel
         * @access protected
         */
        protected $_threshold;

        /**
         * @var
         */
        protected $_fullyQualifiedClassName;

        /**
         * @var string
         */
        protected $_name    = '';

        /**
         * @var null
         */
        protected $_owner   = null;


        /**
         * @var \goliatone\flatg\logging\filters\LogFilter
         */
        protected $_filter  = null;

        /**
         * TODO: Do we want to force clients to be ILoggerAware?
         *       Prob not!
         * @param ILoggerAware $owner
         */
        public function __construct(/*ILoggerAware*/ $owner = null, $config = array())
        {
            $this->setOwner($owner);
            $this->reset();
            if($config) $this->configure($config);
        }

        public function reset()
        {
            $this->_filter    = new LogFilter();
            $this->_publisher = new CompoundPublisher();
            $this->_threshold = LogLevel::$ALL;
        }

        /**
         * Logs with an arbitrary level.
         *
         * @param int|string|LogLevel $level
         * @param string $message
         * @param array  $context
         * @return $this
         */
        public function log($level, $message, array $context = array())
        {
            //Ensure we have a LogLevel instance
            $level = LogLevel::getLevel($level);

            //Can we log this level?
            //First filter pass. We should have filter.isPreProcess()
            if($this->isEnabledFor($level)) return;

            //Build LogMessage
            $msg = $this->buildMessage($level, $message, $context, 3);

            //apply augmenters, this should extend the context with
            //custom data, ie: memory usage, request info.
            $msg = $this->applyAugmenters($msg);

            //After creating log, and augmenting it we render message.
            $msg->updateMessage();

            //TODO: So, apparently filters do need a full LogMessage to do it's
            //thing. Could we also take levels? Packages? so we have siftLevel/siftNamespace?
            if($this->filtersMessage($msg)) return;

            //Send to all the publishers that have been registered.
            //Publishers still have a change to decide if they
            //want to handle this event or not.
            $this->publish($msg);

            return $this;
        }

        /**
         * @param $level
         * @param $message
         * @param array $context
         * @param int $stackTraceSkip
         * @return \goliatone\flatg\logging\core\LogMessage
         */
        public function buildMessage($level, $message, array $context = array(), $stackTraceSkip = 3)
        {
            //TODO: We should have configured a stringifier here as well.
            if(!is_string($message)) $message = print_r($message, true);

            $msg = new LogMessage($level, $message, $context);
            $msg->setLogger($this->getName());
            $msg->setTimestamp(new DateTime('NOW'));
            $msg->setStackTrace(Debugger::backtrace($stackTraceSkip));

            return $msg;
        }

        /**
         * @param $callable
         */
        public function addAugmenter($callable)
        {
            $augmenter = $callable;

            if(!($callable instanceof ILogAugmenter))
            {
                if(is_callable($callable)) $augmenter = new CallbackAugmenter($callable);
            }

            $this->_augmenters[] = $augmenter;
        }

        /**
         * Procedures add metadata to the `$message`.
         * You should register procedures directly on
         * the logger.
         *
         * @param  LogMessage $message
         * @return LogMessage
         */
        public function applyAugmenters(LogMessage $message)
        {
            foreach($this->_augmenters as $process)
            {
                $message = $process->process($message);
            }

            return $message;
        }

        /**
         * @param $level
         * @return bool
         */
        public function isEnabledFor(LogLevel $level)
        {
            //Is logging disabled globally?
            if(static::$DISABLED) return TRUE;

            //Is this logger explicitly disabled?
            if($this->_enabled === FALSE) return TRUE;

            //Provided level is filtered by the threshold!
            if($this->_threshold->filters($level)) return TRUE;

            //TODO: Can we filter based on level? Package?
//            if($this->_filter->sift($level)) return TRUE;

            // we have up or what package is disabled, etc.
            return FALSE;
        }


        /**
         * @param LogMessage $message
         */
        public function publish(LogMessage $message)
        {
            $this->_publisher->publish($message);
        }

        /**
         * @param ILogPublisher $publisher
         * @return $this
         */
        public function addPublisher(ILogPublisher $publisher)
        {
            $id = $publisher->getName();
            $this->_publisher->add($id, $publisher);
            return $this;
        }

        /**
         * @param bool $enabled
         */
        public function setEnabled($enabled)
        {
            $this->_enabled = $enabled;
        }

        /**
         * @return bool
         */
        public function getEnabled()
        {
            return $this->_enabled;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * @return null
         */
        public function getOwner()
        {
            return $this->_owner;
        }

        /**
         * @param null $owner
         */
        public function setOwner($owner = NULL)
        {
            $this->_owner = $owner;

            if(! $owner) return;

            $this->_name = Utils::qualifiedClassName($owner, FALSE);
            $this->_fullyQualifiedClassName = Utils::qualifiedClassName($owner);
        }

        /**
         * @return mixed
         */
        public function getFullyQualifiedClassName()
        {
            return $this->_fullyQualifiedClassName;
        }

        /**
         * @param \goliatone\flatg\logging\core\LogLevel $threshold
         */
        public function setThreshold($threshold)
        {
            $this->_threshold = $threshold;
        }

        /**
         * @return \goliatone\flatg\logging\core\LogLevel
         */
        public function getThreshold()
        {
            return $this->_threshold;
        }

    }
}