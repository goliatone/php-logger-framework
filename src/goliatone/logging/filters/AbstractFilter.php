<?php namespace goliatone\flatg\logging\filters {

    use goliatone\flatg\logging\core\ILogFilter;
    use goliatone\flatg\logging\core\ILogLevel;
    use goliatone\flatg\logging\core\LogLevel;
    use goliatone\flatg\logging\core\LogMessage;

    /**
     * TODO: Implement MaxBurst, Rate, Threshold, Time
     * RegExp, Marker, Map, DynamicThreshold.  and
     * FilterManager.
     *
     * Class AbstractFilter
     * @package goliatone\flatg\logging\filters
     */
    abstract class AbstractFilter implements ILogFilter
    {
        /**
         * We set DENY to 0 since it will evaluate
         * to falsy.
         */
        const DENY    = 0;

        /**
         * Filter does not care about the current event.
         */
        const NEUTRAL = -1;

        /**
         * Filter can take this event.
         */
        const ACCEPT  = 1;

        /**
         * @var LogLevel
         * @access protected
         */
        protected $_level = null;

        /**
         * @var string
         * @access protected
         */
        protected $_namespace = null;

        /**
         * @var bool
         * @access protected
         */
        protected $_isPreProcess = FALSE;


        /**
         * @var int
         * @access protected
         */
        protected $_onMatch     = AbstractFilter::DENY;

        /**
         * @var int
         * @access protected
         */
        protected $_onMisMatch  = AbstractFilter::NEUTRAL;


        /**
         * @var bool
         */
        protected $_initialized = false;

        /**
         * @var ILogFilter
         */
        protected $_next        = null;

        /**
         *
         */
        public function __construct()
        {
            $this->initialize();
        }

        /**
         *
         */
        public function initialize()
        {
            if($this->_initialized) return;
            $this->_initialized = true;

            //TODO Move to BaseObject

            //TODO: We should pick this one up based on Environment.
            //so that in DEV we get $ALL, and in PROD we get $ERROR.
            $this->_level = LogLevel::$ALL;

            $this->doInitialize();
        }

        /**
         *
         */
        public function doInitialize()
        {

        }

        /**
         * Returns TRUE if the filter can
         * be published. If the LogMessage
         * belongs to a `namespace` that is
         * disabled, then we can `filter` else
         * we collect it.
         *
         * @param  LogMessage $message
         * @return bool
         */
        public function sift(LogMessage $message)
        {
            //TODO: Think about the values of DENY|ACCEPT|NEUTRAL only 0 evaluates to false
            $filter = $this->_next;
            while($filter !== null) {
                switch ($filter->sift($message)) {
                    case AbstractFilter::DENY   : return AbstractFilter::DENY;
                    case AbstractFilter::ACCEPT : return AbstractFilter::ACCEPT;
                    case AbstractFilter::NEUTRAL: $filter = $filter->getNext();
                }
            }

            return $this->doSift($message);
        }

        /**
         * @param LogMessage $message
         * @return int
         */
        protected function doSift(LogMessage $message)
        {
            return AbstractFilter::NEUTRAL;
        }

        /**
         * Returns `TRUE` if the filter
         * is to be applied as a pre process.
         *
         * Defaults to `FALSE`
         *
         * @return bool
         */
        public function isPreProcess()
        {
            return $this->_isPreProcess;
        }

        /**
         * @return ILogFilter
         */
        public function getNext()
        {
            return $this->_next;
        }

        /**
         * @param ILogFilter $filter
         * @return $this
         */
        public function addNext(ILogFilter $filter)
        {
            if($this->_next) $this->_next->addNext($filter);
            else $this->_next = $filter;

            return $this;
        }

        /**
         * @return $this
         */
        public function clear()
        {
            $this->_next = null;
            return $this;
        }

        /**
         * @param  LogLevel $level
         * @return mixed
         */
        public function setLevel(LogLevel $level)
        {
            $this->_level = $level;
        }

        /**
         * @return ILogLevel
         */
        public function getLevel()
        {
            return $this->_level;
        }

        /**
         * Defaults to `NULL`, thus not
         * filtering any namespace.
         *
         * @return string
         */
        public function getNamespace()
        {
            return $this->_namespace;
        }

        /**
         * @param  int $action
         * @return $this
         */
        public function onMatchShould($action)
        {
            $this->_onMatch = $action;
            return $this;
        }

        /**
         * @param  int $action
         * @return $this
         */
        public function onMisMatchShould($action)
        {
            $this->_onMisMatch = $action;
            return $this;
        }
    }
}