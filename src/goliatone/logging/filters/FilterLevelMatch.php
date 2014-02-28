<?php namespace goliatone\flatg\logging\filters {

    use goliatone\flatg\logging\core\LogLevel;
    use goliatone\flatg\logging\core\LogMessage;

    /**
     * Class FilterLevelMatch
     * @package goliatone\flatg\logging\filters
     */
    class FilterLevelMatch extends AbstractFilter
    {

        /**
         * @param LogLevel $filterLevel
         */
        public function __construct(LogLevel $filterLevel = null)
        {
            $filterLevel && $this->_level = $filterLevel;
        }

        /**
         * @param LogMessage $message
         * @return int
         */
        protected function doSift(LogMessage $message)
        {
            //If we did not bother to set a level, move on.
            if(!$this->_level) return FilterLevelMatch::NEUTRAL;

            $level = $message->getLevel();

            if($this->_level->filters($level)) return $this->_onMatch;

            return $this->_onMisMatch;
        }

    }
}