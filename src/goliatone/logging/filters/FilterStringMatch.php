<?php namespace goliatone\flatg\logging\filters {

    use goliatone\flatg\logging\core\LogMessage;

    /**
     * Class FilterStringMatch
     * @package goliatone\flatg\logging\filters
     */
    class FilterStringMatch extends AbstractFilter
    {
        /**
         * @var string
         */
        public $stringToMatch = null;

        /**
         * @param null $stringToMatch
         */
        public function __construct($stringToMatch = null)
        {
            $stringToMatch && $this->stringToMatch = $stringToMatch;
        }

        /**
         * @param LogMessage $message
         * @return int
         */
        protected function doSift(LogMessage $message)
        {
            $text = $message->getMessage();

            if(!$text || !$this->stringToMatch) return FilterStringMatch::NEUTRAL;

            if(strpos($text, $this->stringToMatch) === false) return $this->_onMisMatch;

            return $this->_onMatch;
        }
    }
}