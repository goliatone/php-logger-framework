<?php namespace goliatone\flatg\logging\formatters {

    use goliatone\flatg\logging\core\ILogMessageFormatter;
    use goliatone\flatg\logging\core\LogMessage;

    /**
     * Class CompoundFormatter.
     * TODO: Rename to CompositeFormatter.
     * TODO: Extend TypedCollection(?)
     *
     * @package goliatone\flatg\logging\formatters
     */
    class CompoundFormatter implements ILogMessageFormatter
    {


        protected $_formatters = array();


        public function add($id, ILogMessageFormatter $formatter)
        {
            $this->_formatters[$id] = $formatter;
        }

        /**
         * @param LogMessage $message
         * @return mixed
         */
        public function format(LogMessage $message)
        {
            foreach($this->_formatters as $formatter)
            {
                $message = $formatter->format($message);
            }
        }

        public function getName()
        {
            return __CLASS__;
        }
    }
}