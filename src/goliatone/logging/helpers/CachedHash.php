<?php namespace goliatone\flatg\logging\helpers {

    class CachedHash
    {

        protected $cache;


        public function __construct()
        {
            $this->reset();
        }

        /**
         *
         */
        public function reset()
        {
            $this->cache = array();
        }

        /**
         * @param string $id
         * @param mixed  $getter
         */
        public function get($id, $getter)
        {
            if($this->has($id)) return $this->cache[$id];

//            $this->set($id, $getter);
            call_user_func_array(array($this, 'set'), func_get_args());

            return $this->cache[$id];
        }

        /**
         * @param $id
         * @param $value
         * @return $this
         */
        public function set($id, $value)
        {

            //We want to lazy process arguments, only if callable getter.
            $_get = function($arguments) use($value){
                array_shift($arguments); //get rid of $id
                array_shift($arguments); //get rid of $getter
                return call_user_func_array($value, $arguments);
            };

            $this->cache[$id] = is_callable($value) ? $_get(func_get_args()) : $value;
            return $this;
        }

        /**
         * @param $id
         * @return bool
         */
        public function has($id)
        {
            return array_key_exists($id, $this->cache);
        }

        /**
         * @param $id
         * @return $this
         */
        public function remove($id)
        {
            if(! $this->has($id)) return $this;
            unset($this->cache[$id]);
            return $this;
        }
    }
}