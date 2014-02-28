<?php namespace goliatone\flatg\config {

    /**
     * TODO: Make generic cache data object.
     *
     *
     * @author goliatone <hello@goliatone.com>
     * @license MIT
     * @package goliatone\flatg\config
     */
    use \ArrayObject;
    use goliatone\flatg\config\drivers\ConfigDriver;

    /**
     * Class Config
     * @package goliatone\flatg\config
     */
    class Config extends \ArrayObject
    {
        /** @var array  Cached keys.*/
        protected $_cache  = array();

        /** @var array  Config values.*/
        protected $_config = array();

        /** @var string  Env mode.*/
        protected $_environment = '';

        /**
         * @var drivers\ConfigDriver
         */
        protected $driver;

        /**
         * Config constructor.
         *
         * @param mixed $initialize Can be either an array
         *                          or path to config file.
         */
        public function __construct($initialize = NULL)
        {
            //We need to set a driver before we load.
            $this->driver = new ConfigDriver();

            if(is_array($initialize)) $this->set($initialize);
            else if(is_string($initialize)) $this->load($initialize);

            parent::__construct($this->_config,
                \ArrayObject::STD_PROP_LIST |
                \ArrayObject::ARRAY_AS_PROPS);
        }

        /**
         * Retrieve a configuration setting identified by they
         * `$key` parameter.
         *
         * They key can be a path in dot syntax, where the
         * last segment is the property retrieved.
         * ```php
         * $key=backend_storage.dropbox.api_key;
         * $config['backend_storage']['dropbox']['api_key'];
         * ```
         * If the `$key` parameter is `NULL` the method returns
         * the entire config.
         *
         * The `$default` parameter lets you specify the returned
         * value in case the requested setting is undefined.
         * If `$default` is not provided it will return `NULL`
         *
         * ```php
         *   $apiKey = $config->get('backend_storage.dropbox.api_key');
         *
         *   $theme = $config->get('theme', 'default');
         * ```
         *
         * @param  string $key Setting's unique identifier.
         * @param  mixed  $default The default value.
         * @return mixed  It will setting value or `$default`
         *                if undefined.
         */
        public function get($key = NULL, $default = NULL)
        {
            //NULL key means dump it all.
            if(!$key) return $this->_config;

            //We want a set of items:
            if(is_array($key))
            {
                $out = array();
                foreach($key as $item) $out[$item] = $this->get($key);
                return $out;
            }

            //We want an specific item, retrieve from cache or cache it.
            $self = $this;
            $config = $this->_config; //In PHP 5.4 we can Closure.bind
            return $this->_cacheGet($key, function() use($self, $key, $default, &$config){
                return $self->solveKeyPath($config, $key, $default);
            });
        }

        /**
         * Register one or more configuration settings values.
         *
         * Configuration settings are typically defined by
         * returning an array from a file stored under a
         * registered config folder. However, it is
         * also possible to explicitly set configuration values using
         * the Config class.
         *
         * @param  string $key Setting's unique identifier.
         * @param  mixed  $value The default value.
         * @return goliatone\flatg\config\Config Chainable method
         */
        public function set($key, $value = NULL)
        {
            if(is_array($key)) return $this->_configMerge($key);

            $this->_cache[$key] = $value;

            $target = &$this->_config;
            //TODO: We should be able to have setPath
            $keys = explode('.', $key);
            $i = sizeof($keys);
            foreach($keys as $key)
            {
                if(!array_key_exists($key, $target)) $target[$key] = array();
                if(--$i) $target = &$target[$key];
            }
            $target[$key] = $value;

            return $this;
        }

        /**
         * We mainly have this here to use on the
         * ArrayObject interface.
         * REDAME: This does not work for dot paths!
         *
         *
         * @param $key
         * @return $this
         */
        public function del($key)
        {
            //TODO: We need to access the final target and
            //then unset that!!!
            unset($this->_config[$key], $this->_cache[$key]);
            return $this;
        }

        /**
         * @param $filename
         * @return goliatone\flatg\config\Config
         */
        public function load($filename)
        {
            $config = $this->import($filename, array(), TRUE);

            if($this->_environment) return $this->_mergeEnvironment($filename, $config);

            return $this->_configMerge($config);
        }

        /**
         * TODO: Do we want this here?
         *
         * @param $target
         * @param $key
         * @param array $defaults
         * @return $this
         */
        public function init($target, $key, $defaults = array())
        {
            $values = $this->get($key);
            $properties = get_object_vars($target);
            foreach($properties as $prop => $value)
            {
                if(property_exists($target, $prop)) $target->{$prop} = $values[$prop];
            }


            return $this;
        }

        /**
         * TODO Rename or make this actually load something :P
         * Actually, it would be a setEnvironementIf('develop', DEBUG)
         *
         *
         * @param string $env
         * @param bool $condition
         */
        public function loadEnvironment($env = '', $condition = FALSE)
        {
            //If condition is not OK, then disable env.
            $this->_environment = $condition ? $env : '';
        }

        /**
         * @param $filename
         * @param array $defaults
         * @param bool $required
         * @return array
         * @throws \InvalidArgumentException
         */
        public function import($filename, $defaults = array(), $required = FALSE)
        {

            //Here we are handling just one type of config
            //PHP arrays. Abstract import by file ext.
            $config = array();

            try
            {
                $config = $this->driver->import($filename);
            }
            catch(\Exception $e)
            {
                if($required) throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
            }

            //TODO: WHAT IF WE RETURN A Config INSTANCE from iclude?
            return is_array($config) ? array_replace_recursive($defaults, $config) : $defaults;
        }

        /**
         * @param $path
         * @param null $config
         * @return $this
         */
        public function save($path, $config=NULL)
        {
            $config = $config ? $config : $this->_config;
            $this->driver->save($path, $config);
            return $this;
        }


/////////////////////////////////////////////////////////////////////////////////
// Protected methods
/////////////////////////////////////////////////////////////////////////////////
        /**
         * @param string $key    Setting's unique identifier
         * @param mixed  $getter Function to retrieve value or the
         *                       default value.
         * @access protected
         * @return mixed         Returns the result after accessing cache.
         */
        protected function _cacheGet($key, $getter)
        {
            if(array_key_exists($key, $this->_cache)) return $this->_cache[$key];
            return $this->_cache[$key] = is_callable($getter) ? $getter() : $getter;
        }

        /**
         * @param  array    $config     Array object with want to merge
         * @access protected
         * @return goliatone\flatg\config\Config Chainable method
         */
        protected function _configMerge($config)
        {
            $this->_config = array_replace_recursive($this->_config, $config);
            //Instead of passing around _config by reference, just update.
            $this->exchangeArray($this->_config);

            return $this;
        }
        /**
         * Merge the items in the given file into the items.
         *
         * @param  string   $filename  Path to original config file
         * @param  array    $items     Array object with want to merge
         * @access protected
         * @return goliatone\flatg\config\Config Chainable method
         */
        protected function _mergeEnvironment($filename, $items)
        {
            //This should never be called, since we have a esctrict load
            //on this->load
            if(!file_exists($filename)) return $this->_configMerge($items);
            // $info = pathinfo($filename);

            /*
             * If `$_environment = 'development'`
             * We want to go from `path/to/config.php` to
             * `path/to/config.development.php`
             */
            $info = new \SplFileInfo($filename);
            $ext  = $info->getExtension();
            $pth  = $info->getPath().DIRECTORY_SEPARATOR;
            $pth .= $info->getBasename($ext).$this->_environment.".".$ext;

            $items = array_replace_recursive($items, $this->import($pth));

            return $this->_configMerge($items);
        }

        /**
         * Note that the visibility of this method is set to public
         * to be accessible inside the cache closure.
         *
         * @param mixed     $target    Source array we traverse to
         *                             find the given key.
         * @param string    $key       Unique identifier to resource.
         *                             It can be a path with dot notation.
         * @param mixed     $default   Default value if no resource is
         *                             found. It we don't set it is NULL
         * @return mixed
         */
        public function solveKeyPath(&$target, $key, $default = NULL)
        {
            //Optimizations: explode in foreach loop bad.
            //foreach loop bad, use for, sizeof and array_keys :)
            foreach(explode('.', $key) as $key) {
                if ( ! is_array($target) ||
                    ! array_key_exists($key, $target)) return $default;

                $target = &$target[$key];
            }

            return $target;
        }

/////////////////////////////////////////////////////////////////////////////////
// Getters & Setters
/////////////////////////////////////////////////////////////////////////////////


        /**
         * For now, this are here mainly
         * for unit testing. I might switch
         * to use Reflection and remove them.
         */
        public function getSource()
        {
            return $this->_config;
        }

        /**
         * Access to the cache array.
         */
        public function getCache()
        {
            return $this->_cache;
        }

/////////////////////////////////////////////////////////////////////////////////
// ArrayObject implementation
/////////////////////////////////////////////////////////////////////////////////
        /**
         * ArrayObject interface.
         */
        public function offsetGet($key)
        {
            return $this->get($key);
        }

        /**
         * ArrayObject interface.
         */
        public function offsetSet( $key , $value)
        {
            return $this->set($key, $value);
        }

        /**
         * ArrayObject interface.
         */
        public function offsetUnset($key)
        {
            return $this->del($key);
        }

    }
}
