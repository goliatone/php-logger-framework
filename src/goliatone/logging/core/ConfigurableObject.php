<?php namespace goliatone\flatg\logging\core {

    class ConfigurableObject
    {

        public function configure($config=array())
        {
            if(is_string($config))
            {
                if(!($args = func_get_args()))
                    throw new \InvalidArgumentException("Configure: If first param is string, second must be array!");
                $config = array($config=>func_get_args());
            }
            //TODO: Move to Utils?
            $getValue = function($value){
                if(!$value && $value !== FALSE) return null;
                return is_callable($value) ? call_user_func_array($value, func_get_args()) : $value;
            };

            //TODO: Move to Utils?
            $setValue = function($scope, $name, $value, $denyDynamicDefinitions = true, $solveCallableValues = true){
                //We might have passed a callback to lazy solve something.
                if($solveCallableValues && is_callable($value)) $value = call_user_func($value);
                //We might have a passed a reference to a setter:
                if(is_callable(array($scope, $name))) return call_user_func(array($scope, $name), $value);

                //If prop is not defined, can we create props dynamically?
                if(! property_exists($scope, $name) && $denyDynamicDefinitions) return;

                if(is_array($scope))       return $scope[$name] = $value;
                else if(is_object($scope)) return $scope->$name = $value;
            };

            foreach($config as $prop => $value)
            {
                $value = $getValue($value);
                $setValue($this, $prop, $value);
            }
        }

    }
}