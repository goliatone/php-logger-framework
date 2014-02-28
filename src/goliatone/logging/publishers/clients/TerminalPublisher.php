<?php namespace goliatone\flatg\logging\publishers\clients {

    use goliatone\flatg\logging\core\LogMessage;
    use goliatone\flatg\logging\helpers\Utils;


    /**
     * Class TerminalPublisher
     * @package goliatone\flatg\logging\publishers\clients
     */
    class TerminalPublisher extends AbstractPublisher
    {

        /**
         * @var string
         */
        public $header = <<< HEADER
*************************************
GOLIATONE TALKING OUT LOUD!!
Start Time: {start_time}
*************************************\n
HEADER;

        /**
         * @var string
         */
        public $footer = <<< FOOTER
-------------------------------------
TOTAL LOGS   : {total_logs}
MEMORY PEAK  : {memory_peak}
MEMORY USAGE : {memory_usage}
-------------------------------------\n
FOOTER;


        /**
         * @inheritdoc
         */
        public function onPublish(LogMessage $message)
        {
            $this->applyFormat($message);
            echo $message->getMessage();
        }

        /**
         * TODO: Should we wait to have one log event before publishing this?
         */
        public function begin()
        {
            $strTime = date('H:i:s', $this->startTime);
            $header  = $this->stringTemplate($this->header, array('start_time'=>$strTime));
            echo  $header;
        }

        /**
         * TODO: Should we wait to have one log event before publishing this?
         */
        public function terminate()
        {
            $total   = count($this->messages);
            $usage   = Utils::fileSizeToString(memory_get_usage(true));
            $peak    = Utils::fileSizeToString(memory_get_peak_usage(true));
            $context = array('total_logs'=>$total,
                             'memory_peak'=>$peak,
                             'memory_usage'=>$usage
                            );

            $footer  = $this->stringTemplate($this->footer, $context);
            echo  $footer;
        }

    }
}