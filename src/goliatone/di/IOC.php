<?php namespace goliatone\flatg\di {

    use \ReflectionMethod;

    /**
     * Class IOC
     * @package goliatone\flatg\di
     */
    class IOC
    {
        //We should have this as a config option
        /**
         * @var string
         */
        static public $REFERENCE_STRING = '@';

        /**
         * @var array
         */
        public $beans       = array();
        /**
         * @var array
         */
        public $buildingFor = array();
        /**
         * @var array
         */
        public $aliases     = array();

        /**
         *
         */
        public function __construct()
        {
        }

        /**
         * @param $id
         * @param null $factory
         * @param array $config
         * @return Definition
         */
        public function map($id, $factory = null, $config = array())
        {
            //TODO: Do we throw error, duplicate entry?!
            if($this->has($id)) return $this->handleError('map', $id, "Bean already registered for {$id}");

            //We might be using an @alias, also dot notation
            $factory = $this->solveAlias($factory);

            //Create registry for the entry id.
            $bean = new Bean($id, $factory, $config);

            $bean->setContainer($this);

            $this->beans[$id] = $bean;

            return $bean;
        }

        /**
         * @param array $config
         */
        public function configure($config = array())
        {
            foreach($config as $id => $bean)
            {
                $factory = array_key_exists('factory', $bean) ? $bean['factory'] : $id;
                $options = array_key_exists('options', $bean) ? $bean['options'] : array();

                $this->map($id, $factory, $options);

//                $aliases = array_key_exists('aliases', $bean) ? $bean['aliases'] : array();
//                $this->registerAlias($aliases);
            }
        }


        /**
         * @param $alias
         * @param null $name
         */
        public function registerAlias($alias, $name = null)
        {
            if(is_array($alias))
            {
                foreach($alias as $alias => $name)
                {
                    $this->registerAlias($alias, $name);
                }
            }

            if(is_string($alias) && is_string($name))
            {
                $this->aliases[$alias] = $name;
            }
        }

        /**
         * TODO: Make regex dynamic, so that we can configure the ALIAS_STRING
         *
         * @param $class
         * @param array $mapReference
         * @param string $regex
         * @return string
         */
        public function solveAlias($class, $mapReference = array(), $regex = "/@([a-zA-Z0-9_-]*)\\./")
        {
            //We should map our predefined map reference with what we are getting here.
            $mapReference = array_replace_recursive($this->aliases, $mapReference);

            //class: @logging.filters.LogFilter
            if(preg_match($regex, $class, $matches))
            {
                //key: @logging
                $key = '@'.$matches[1];
                //Do we have an entry that we can solve?
                if(array_key_exists($key, $mapReference))
                {
                    //array('@logging'=>'goliatone.flatg.logging');
                    $alias = $mapReference[$key];

                    //goliatone.flatg.logging.filters.LogFilter
                    $class = str_replace($key, $alias, $class);
                }
            }
            if($class) $class = str_replace('.', '\\', $class);

            return $class;
        }

        /**
         * @param $id
         * @param array $config
         */
        public function build($id, $config = array())
        {
            if(!$this->has($id)) return $this->handleError('build', $id, "No bean found for {$id}");

//            if(array_key_exists($id, $this->buildingFor)) return;

//            $this->buildingFor[$id] = $id;
            $bean = $this->get($id);

            $instance = $bean->build($config);
//            unset($this->buildingFor[$id]);

            return $instance;
        }

        /**
         * @param $id
         * @param null $default
         * @return Bean
         */
        public function get($id, $default = null)
        {
            if(!$this->has($id)) return $default;

            return $this->beans[$id];
        }

        /**
         * @param $id
         * @return bool
         */
        public function has($id)
        {
            return array_key_exists($id, $this->beans);
        }


        /**
         * @param $method
         * @param $id
         * @param $message
         * @throws \ErrorException
         */
        public function handleError($method, $id, $message)
        {
            $echo = "METHOD {$method}: {$message}";
            throw new \ErrorException($echo);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return "[object IOC]";
        }
    }


    /**
     * Class Bean
     * @package goliatone\flatg\di
     */
    class Bean
    {
        /**
         * @var
         */
        public $id;
        /**
         * @var array
         */
        public $config;
        /**
         * @var
         */
        public $solver;
        /**
         * @var null
         */
        public $factory;
        /**
         * @var IOC
         */
        public $container;

        /**
         * @var array
         */
        public $methods;
        /**
         * @var array
         */
        public $arguments;

        /**
         * @param $id
         * @param null $factory
         * @param array $config
         */
        public function __construct($id, $factory = null, $config = array())
        {
            $this->id      = $id;
            $this->factory = $factory;
            $this->config  = $config;

            $this->methods   = array();
            $this->arguments = array();

            if(array_key_exists('methods', $config))   $this->addMethodCalls($config['methods']);
            if(array_key_exists('arguments', $config)) $this->addArguments($config['arguments']);
            unset($config['methods']);
            unset($config['arguments']);
//            $this->solver = $this->buildFactory();

        }

        /**
         * @param $container
         */
        public function setContainer($container)
        {
            $this->container = $container;
        }

        /**
         * @return array
         */
        public function getArguments()
        {
            return $this->arguments;
        }

        /**
         * @return array
         */
        public function getMethods()
        {
            return $this->methods;
        }

        /**
         * @return CallableFactory|ClassFactory|LiteralFactory
         * @throws \Exception
         */
        public function buildFactory()
        {
            $id        = $this->id;
            $factory   = $this->factory;
            //we need container to solve dependencies
            $container = $this->container;

            //we only passed in $id, which is both id and class.
            if(!$factory) $factory = $id;

            if(is_string($factory))
            {
                //we passed an alias and then a class definition as a string.
                if(class_exists($factory)) return new ClassFactory($factory, $this);
                //We got a literal string value:
                return new LiteralFactory($factory, $this);
            }

            //we passed in either a closure, or an array callback.
            if(is_callable($factory)) return new CallableFactory($factory, $this);

            if(is_object($factory))   return new LiteralFactory($factory, $this);

            throw new \Exception("No solver found for {$id}");
        }

        /**
         * @param array $config
         * @return mixed
         */
        public function build($config = array())
        {
            if(!$this->solver) $this->solver = $this->buildFactory();
            $options = array_replace_recursive($this->config, $config);

            $instance = $this->solver->solve($this->arguments);

            $this->applyMethods($instance);
            return $instance;
        }

        /**
         * @param $instance
         */
        public function applyMethods($instance)
        {
            if(! $this->solver->takesMethodCalls) return;

            foreach($this->methods as $method => $call)
            {
                $invoke = new ReflectionMethod($instance, $method);
                foreach($call as $arguments)
                {
                    $arguments = $this->solveArguments($arguments);
                    $invoke->invokeArgs($instance, $arguments);
                }
            }
        }

        /**
         * @param $arguments
         * @return array
         * @throws \Exception
         */
        public function solveArguments($arguments)
        {
            $args = array();
            foreach($arguments as $arg)
            {
                if(is_string($arg) && strpos($arg, IOC::$REFERENCE_STRING) === 0)
                {
                    $arg = ltrim($arg, IOC::$REFERENCE_STRING);
                    //we don't have a reference
                    if(!$this->container->has($arg)) throw new \Exception("Unable to solve the reference {$arg}");
                    $arg = $this->container->build($arg);
                }
                $args[] = $arg;
            }

            return $args;
        }

        /**
         * @param $method
         * @return $this
         */
        public function addMethodCall($method /*, ...$arguments*/)
        {
            if(!array_key_exists($method, $this->methods)) $this->methods[$method] = array();
            //the same method can be called multiple times.
            $arguments = func_get_args();
            @array_shift($arguments);
            $this->methods[$method][] = $arguments;

            return $this;
        }

        /**
         * @param $methodsAndArguments
         * @return $this
         */
        public function addMethodCalls($methodsAndArguments)
        {
            foreach($methodsAndArguments as $method => $arguments)
            {
                $this->addMethodCall($method, $arguments);
            }

            return $this;
        }

        /**
         * @param $argument
         * @return $this
         */
        public function addArgument($argument)
        {
            echo $this->id." add argument ".$argument.PHP_EOL;
            $this->arguments[] = $argument;
            return $this;
        }

        /**
         * @param array $arguments
         * @return $this
         */
        public function addArguments(array $arguments)
        {
            foreach($arguments as $argument)
            {
                $this->addArgument($argument);
            }
            return $this;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return "[Bean ".$this->id.", factory: ".$this->solver."]";
        }
    }


    /**
     * Class CallableFactory
     * @package goliatone\flatg\di
     */
    class CallableFactory extends BaseFactory
    {
        /**
         * @param array $arguments
         * @return mixed
         */
        public function solve(array $arguments=array())
        {
            return call_user_func_array($this->subject, $this->getArguments($arguments));
        }
    }

    /**
     * Class ClassFactory
     * @package goliatone\flatg\di
     */
    class ClassFactory extends BaseFactory
    {
        /**
         * @param array $arguments
         * @return object
         */
        public function solve(array $arguments=null)
        {
            $_Class = $this->subject;
            $reflection = new \ReflectionClass($_Class);
            return $reflection->newInstanceArgs($arguments);
        }
    }

    /**
     * Class LiteralFactory
     * @package goliatone\flatg\di
     */
    class LiteralFactory extends BaseFactory
    {
        /**
         * @var bool
         */
        public $takesMethodCalls = false;

        /**
         * @param array $arguments
         * @return mixed
         */
        public function solve(array $arguments=array())
        {
            return $this->subject;
        }
    }

    /**
     * Class BaseFactory
     * @package goliatone\flatg\di
     */
    abstract class BaseFactory
    {
        /**
         * @var string
         */
        public $name;
        /**
         * @var
         */
        public $subject;
        /**
         * @var Bean
         */
        public $bean;

        /**
         * @var bool
         */
        public $takesMethodCalls = true;

        /**
         * @param $subject
         * @param $bean
         */
        public function __construct($subject, $bean)
        {
            $this->name = get_class($this);
            $this->subject   = $subject;
            $this->bean = $bean;
        }

        /**
         * @param array $arguments
         * @return mixed
         */
        abstract public function solve(array $arguments=array());

        /**
         * @param $arguments
         * @return mixed
         */
        public function __invoke($arguments)
        {
            return $this->solve($arguments);
        }

        /**
         * @return array
         */
        public function getArguments()
        {
            $args = empty($arguments) ? $this->bean->getArguments() : $arguments;
            $args = $this->bean->solveArguments($args);
            return array_reverse($args);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return "[Factory ".$this->name."]";
        }
    }
}