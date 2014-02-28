<?php namespace goliatone\flatg\logging\core {


    /**
     * Publishers are responsible for delivering LogMessages
     * and publishing them to different destinations.
     * Publishers implement the ILogPublisher interface.
     *
     * Publishers can extend AbstractPublisher, which
     * implements the basic structure of a Publisher.
     *
     * Publishers, ideally, are only responsible for
     * marshaling the payload to it's target destination.
     * They should delegate formatting to a ILogMessageFormatter.
     *
     * Publishers should have a class id/name, in
     * order to be reached through the configuration
     * process.
     *
     * Publishers should be of two types, Async and Sync.
     * HTML, File, Stream, etc should be Async and if
     * possible, flushed on destroy. CLI publishers
     * can be sync, and flushed as we get events.
     *
     * Publishers can be assigned a threshold level
     * and a set of filters.
     *
     * - SysLogPublisher
     * - FilePublisher
     * - RotatingFilePublisher
     * - ChromeConsolePublisher
     * - LinePublisher
     *
     * TODO: Do we want to move terminate method
     *       to a lifecycle interface? initialize/terminate?
     * TODO: If we have more than one Logger instance, then
     *       how do we manage publishers? We should have a
     *       factory that manages instances and handles out
     *       references to the same item around... IE we
     *       don't want different loggers send events
     *       to different FilePublishers at the same
     *       time.
     *
     * Interface ILogPublisher
     * @package goliatone\flatg\logging\core
     */
    interface ILogPublisher
    {


        /**
         * @param $header
         * @return mixed
         */
        public function setHeader($header);

        /**
         * @param $footer
         * @return mixed
         */
        public function setFooter($footer);

        /**
         * @param  LogMessage $message
         * @return void
         */
        public function publish(LogMessage $message);

        /**
         * REVIEW: Do we want to have array in the signature?
         * @param array|\goliatone\flatg\logging\core\LogMessage $message
         * @return mixed
         */
        public function flush(array $message = null);

        /**
         * @param  string               $id
         * @param  ILogMessageFormatter $formatter
         * @return ILogPublisher
         */
        public function addFormatter(ILogMessageFormatter $formatter);

        /**
         * @return mixed
         */
        public function begin();

        /**
         * @return void
         */
        public function terminate();

        /**
         * @return string
         */
        public function getName();

    }
}