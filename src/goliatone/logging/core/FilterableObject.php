<?php namespace goliatone\flatg\logging\core {

    /**
     * Class FilterableObject
     * @package goliatone\flatg\logging\core
     */
    class FilterableObject extends ConfigurableObject
    {
        /**
         * @var ILogFilter
         */
        protected $_filter;


        /**
         * @param LogMessage $message
         * @return bool
         */
        public function filtersMessage(LogMessage $message)
        {
            //TODO: How could we get rid of the ! operator here?
            return !$this->_filter->sift($message);
        }

        /**
         * @param ILogFilter $filter
         * @return $this
         */
        public function addFilter(ILogFilter $filter)
        {
            if($this->_filter) $this->_filter->addNext($filter);
            else $this->_filter = $filter;

            return $this;
        }

        public function clearFilters()
        {

            $this->_filter->clear();
            return $this;
        }
    }
}