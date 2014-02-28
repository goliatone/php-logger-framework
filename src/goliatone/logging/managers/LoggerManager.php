<?php namespace goliatone\flatg\logging\managers {

    use goliatone\flatg\di\IOC;
    use goliatone\flatg\logging\helpers\CachedHash;
    use goliatone\flatg\logging\helpers\Utils;

    /**
     * Class LoggerManager takes care of building
     * logger instances.
     * It will store a cache for previously built
     * loggers, and if we ask multiple times for a logger
     * with the same id it will return the same instance.
     *
     * The LoggerManager also takes care of configuring the
     * object. It will create all necessary filters, publishers
     * and configure those as well.
     *
     * @package goliatone\flatg\logging\managers
     */
    class LoggerManager
    {
        /**
         * @var CachedHash
         */
        protected $cache;

        /**
         * @var \goliatone\flatg\di\IOC
         */
        protected $ioc;

        /**
         * @var array
         */
        protected $_defaults = array(
            'logger' =>array(
                'factory' => "@logging.loggers.DefaultLogger",
                'options' => array(
                    'methods' => array(
                        'addPublisher' => '@TerminalPublisher'
                    )
                )
            ),
            'NullLogger' => array(
                'factory' => '@logging.loggers.NullLogger'
            ),
            'TerminalPublisher'=>array(
                'factory' => "@logging.publishers.clients.TerminalPublisher",
                'options' => array(
                    'methods'=> array(
                        'addFormatter' => '@SimpleFormatter',
//                            'addFilter'=>'FilterStringMatch'
                    )
                )
            ),
            'SimpleFormatter'=>array(
                'factory' => "@logging.formatters.SimpleFormatter",
            ),
            'LogFilter' => array(
                'factory' => "@logging.filters.LogFilter",
            )
            /*,'FilterStringMatch'=>array(
                'factory' => "@logging.filters.FilterStringMatch",
                'options'=>array(
                    'arguments'=>array(
                        'here'
                    )
                )
            )*/
        );
        /**
         *
         */
        public function __construct()
        {
            $this->cache = new CachedHash();
            $this->ioc = new IOC();

        }


        /**
         * @param $forItem
         * @param array $options
         *
         * @return ILogger
         */
        public function buildLogger($forItem, $options = array())
        {
            //should we serialize options and package?
            $key = $this->buildKey($forItem, $options);
            return $this->cache->get($key, array($this, 'build'), $forItem, $options);
        }

        /**
         * @param $forItem
         * @param array $options
         * @return ILogger
         */
        public function build($forItem, $options=array())
        {
            //Merge config with defaults.
            $config = array_merge_recursive($this->_defaults, $options);

            //We want to build a logger. We get the package for the given
            //object. Then we check to see if there is a config item with
            //what key, if not we build for a default key: logger.
            $package   = Utils::qualifiedClassName($forItem);
            $reference = array_key_exists($package, $config) ? $package : 'logger';

            $aliases = array(
                '@logging' =>  'goliatone.flatg.logging'
            );

            $this->ioc->registerAlias($aliases);
            $this->ioc->configure($config);

            //Solve should run all solvers and return our final object.
            $logger = $this->ioc->build($reference);

            //we should configure logger.

            return $logger;
        }

        /**
         * @param $forItem
         * @param $options
         * @return string
         */
        public function buildKey($forItem, $options)
        {
            return Utils::qualifiedClassName($forItem).serialize($options);
        }
    }
}