<?php namespace goliatone\flatg\logging\filters {

    use goliatone\flatg\logging\core\LogMessage;

    /**
     * Class FilterStringMatch
     * @package goliatone\flatg\logging\filters
     */
    class FilterRegxMatch extends AbstractFilter
    {
        /**
         * @var string
         */
        public $patternToMatch = null;

        /**
         * @param null $patternToMatch
         */
        public function __construct($patternToMatch = null)
        {
            $patternToMatch && $this->patternToMatch = $patternToMatch;
        }

        /**
         * @param LogMessage $message
         * @return int
         */
        protected function doSift(LogMessage $message)
        {
            $text = $message->getMessage();

            if(!$text || !$this->patternToMatch) return FilterStringMatch::NEUTRAL;

            if(preg_match($this->patternToMatch, $text) === false) return $this->_onMatch;

            return $this->_onMisMatch;
        }
    }
}