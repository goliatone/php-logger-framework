<?php namespace goliatone\flatg\logging\publishers\clients {

    use goliatone\events\core\ILogMessageFormatter;
    use goliatone\events\core\ILogPublisher;
    use goliatone\flatg\logging\core\LogMessage;

    class FilePublisher implements ILogPublisher
    {
        public function getName()
        {
            // TODO: Implement getName() method.
        }

        public function publish(LogMessage $message)
        {
            // TODO: Implement publish() method.
        }

        public function addFormatter(ILogMessageFormatter $formatter)
        {
            // TODO: Implement addFormatter() method.
        }

        public function terminate()
        {

        }

    }
}