<?php namespace goliatone\flatg\logging {

    use \ReflectionClass;
    use \ReflectionMethod;
    use \ReflectionFunction;

    /**
     * TODO: Implement filters. We want to be able to filter out
     *       by package.*, or by package.FUNCTION, or FUNCTION
     * TODO: Implement quick cache, to store reflected classes(?)
     *
     * Class Debugger
     * @package goliatone\flat\logging
     */
    class Debugger
    {


        static public $enabled = TRUE;

        static public function dump()
        {
            if(! self::$enabled) return;


        }

        public static function phpini($filter=null)
        {
            if ( ! is_readable(get_cfg_var('cfg_file_path')))
            {
                return false;
            }

            if(is_callable($filter)) return $filter(parse_ini_file(get_cfg_var('cfg_file_path'), true));
            return parse_ini_file(get_cfg_var('cfg_file_path'), true);
        }

        static public function classes($filter=null)
        {
            if(is_callable($filter)) return $filter(get_declared_classes());
            return get_declared_classes();
        }

        static public function interfaces($filter=null)
        {
            if(is_callable($filter)) return $filter(get_declared_interfaces());
            return get_declared_interfaces();
        }

        static public function includes($filter=null)
        {
            if(is_callable($filter)) return $filter(get_included_files());
            return get_included_files();
        }

        static public function functions($filter=null)
        {
            if(is_callable($filter)) return $filter(get_defined_constants());
            return get_defined_constants();
        }

        static public function extensions($filter=null)
        {
            if(is_callable($filter)) return $filter(get_loaded_extensions());
            return get_loaded_extensions();
        }

        static public function variables($filter=null)
        {
            if(is_callable($filter)) return $filter(get_defined_vars());
            return get_defined_vars();
        }

        /**
         * Generate custom output for a backtrace.
         *
         * @param int $level
         * @param array $ignore
         * @return array
         */
        static public function backtrace($level = 1, $ignore = array())
        {
            $out    = array();
            $ignore = array_merge($ignore, array(__FUNCTION__, ''));

            $backtrace = array_slice(debug_backtrace(), $level);

            $keys   = array('function', 'class', 'type', 'args', 'file', 'line');
            $sizeOf = sizeof($keys);

            $getValue = function($k, $v, $default = ''){
                return isset($v[$k]) ? $v[$k] : $default;
            };

            foreach($backtrace as $_ => $value)
            {
                $function = $getValue('function', $value);

                if(in_array($function, $ignore)) continue;

                $trace = array();

                for($i=0; $i < $sizeOf; $i++)
                {
                    $key         = $keys[$i];
                    $trace[$key] = $getValue($key, $value);
                }

                $trace['args'] = self::_getArguments($trace);

                if(isset($trace['class']))
                    $trace['call'] = $trace['class'].$trace['type'].$function;

                self::_getNamespace($trace);

                $trace['filename']  = basename($trace['file']);
                $trace['source']    = self::getSource($trace['file'], $trace['line']);

                $out[] = $trace;
            }

            return $out;

        }


        static protected function _getNamespace(& $trace)
        {
            $reflection = new ReflectionClass($trace['class']);
            $trace['namespace'] = $reflection->getNamespaceName();
            $trace['name'] = $reflection->getShortName();
        }

        /**
         * @param $file
         * @param $lineNumber
         * @param int $padding
         * @return array|null
         */
        static public function getSource($file, $lineNumber, $padding = 7)
        {
            if(!$file OR !is_readable($file)) return NULL;

            $line = 0;
            $file = fopen($file, 'r');

            $end   = $lineNumber + $padding;
            $start = $lineNumber - $padding;

            $output = array();

            while(($row = fgets($file)) !== FALSE)
            {
                if(++$line > $end) break;
                if($line < $start) continue;

                $output[$line] = $row;
            }

            fclose($file);

            return $output;
        }

        /**
         * TODO: MOVE TO A FORMATTER CLASS, THAT CAN HANDLE INPUT FROM
         *       DIFFERENT PROVIDERS.
         *
         * @param $code
         * @param $lineNumber
         * @param int $padding
         * @return string
         * @access public
         */
        public function formatCode($code, $lineNumber = -1, $padding = 7)
        {
            $source = '';
            $end    = $lineNumber + $padding;
            #Zero padding for line numbers.
            $format = '% '.strlen($end).'d';

            foreach($code as $line => $row)
            {
                $row = htmlspecialchars($row, ENT_QUOTES);
                $row = preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp', $row);
                $row = '<span>'.sprintf($format, $line).'</span>'.$row;

                if($line === $lineNumber)
                {
                    $row = '<div class="highlight">'.$row.'</div>';
                } else {
                    $row = "<div>{$row}</div>";
                }

                $source .= $row;
            }

            return $source;
        }


        /**
         * @param $trace
         * @return array
         * @access protected
         */
        static protected function _getArguments($trace)
        {
            # Extract $args, $function, $class, $type from $trace.
            extract($trace);

            if(!isset($args)) return;

            $params = null;

            /** @var $function callable */
            if( !empty($class) || function_exists($function))
            {
                try {
                    if(isset($class))
                    {
                        if(!method_exists($class, $function))
                        {
                            # Missing method? Assume magic.
                            $function = '__call';
                            # If type is static, make __callStatic
                            (isset($type) && $type === '::') && $function .= 'Static';
                        }

                        $reflection = new ReflectionMethod($class, $function);

                    } else $reflection = new ReflectionFunction($function);

                    $params = $reflection->getParameters();

                } catch (Exception $e) {
                    # this might go on silently...
                }
            }

            $arguments = array();

            foreach($args as $i => $arg)
            {
                $key = isset($params[$i]) ? $params[$i]->name : $i;
                $arguments[$key] = $arg;
            }

            return $arguments;
        }
    }
}