<?php namespace goliatone\flatg\logging\publishers {

    use goliatone\flatg\logging\core\LogMessage;
    use goliatone\flatg\logging\core\ILogPublisher;
    use goliatone\flatg\logging\core\ILogMessageFormatter;

    /**
     * TODO: Rename to CompositePublisher.
     * TODO: Extend TypedCompound or whatever, we want it DRY.
     *
     * Class CompoundPublisher
     * @package goliatone\flatg\logging\publishers
     */
    class CompoundPublisher implements ILogPublisher
    {

        /**
         * @var array
         */
        protected $_publishers = array();

        /**
         * We name it add instead of addPublisher to
         * remain consistent with CompoundFormatter.
         * Should both extend a base class?
         *
         * @param $id
         * @param ILogPublisher $publisher
         * @return $this
         */
        public function add($id, ILogPublisher $publisher)
        {
            $this->_publishers[$id] = $publisher;
            return $this;
        }

        /**
         * @param string $id
         * @param \goliatone\flatg\logging\core\ILogPublisher|null $default
         * @throws \Exception
         * @return ILogPublisher
         */
        public function get($id, ILogPublisher $default=NULL)
        {
            if($this->has($id)) return $this->_publishers[$id];

            //TODO: Make NullPublisher, which is a NOOP, so we can get rid of logic.
            if($default) return $default;

            throw new \Exception("TODO: We need to handle default Publisher. Return here");
        }

        /**
         * @param string $id
         * @return bool
         */
        public function has($id){
            return array_key_exists($id, $this->_publishers);
        }

        /**
         * @param  string $id
         * @param  ILogMessageFormatter $formatter
         * @return ILogPublisher
         */
        public function addFormatter(ILogMessageFormatter $formatter)
        {
            $id = $formatter->getName();
            $this->get($id)->addFormatter($id, $formatter);

            return $this;
        }

        /**
         * @param LogMessage $message
         * @return $this
         */
        public function publish(LogMessage $message)
        {
            $this->walk(__FUNCTION__, func_get_args());

            return $this;
        }

        /**
         * @return $this|mixed
         */
        public function begin()
        {
            $this->walk(__FUNCTION__);
            return $this;
        }

        /**
         *
         */
        public function terminate()
        {
            $this->walk(__FUNCTION__);
            return $this;
        }


        /**
         * @return string
         */
        public function getName()
        {
            return "CompoundPublisher";
        }

        /**
         * @param array $messages
         * @return mixed|void
         */
        public function flush(array $messages = null)
        {
            $this->walk(__FUNCTION__, func_get_args());

            return $this;
        }

        /**
         * @param $action
         * @param array $arguments
         */
        protected function walk($action, $arguments = array())
        {
            foreach($this->_publishers as $publisher)
            {
                call_user_func_array(array($publisher, $action), $arguments);
            }
        }

        /**
         * @param $callback
         */
        public function each($callback)
        {
            $iterator = $this->getIterator();

            while($iterator->valid()) {
                $callback($iterator->current());
                $iterator->next();
            }

        }

        /**
         * @param $headers
         * @return $this
         */
        public function setHeader($headers)
        {
            foreach($headers as $id => $header)
            {
                if(!$this->has($id)) continue;
                $this->get($id)->setHeader($header);
            }

            return $this;
        }

        /**
         * @param $footers
         * @return $this
         */
        public function setFooter($footers)
        {
            foreach($footers as $id => $footer)
            {
                if(!$this->has($id)) continue;
                $this->get($id)->setFooter($footer);
            }

            return $this;
        }
    }
}