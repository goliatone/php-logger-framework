<?php namespace goliatone\flatg\config\drivers {


    /**
     * Class ConfigDriver
     * @package goliatone\flatg\config\drivers
     */
    class ConfigDriver implements IConfigDriver
    {

        /**
         * @var array
         */
        protected $drivers = array();

        /**
         * @var AbstractConfigDriver
         */
        public $driver;

        /**
         * @var string
         */
        public $extension;

        /**
         *
         */
        public function __construct()
        {
            $this->registerDriver('xml',  'goliatone\\flatg\\config\\drivers\\XmlConfigDriver');
            $this->registerDriver('php',  'goliatone\\flatg\\config\\drivers\\PhpConfigDriver');
            $this->registerDriver('json', 'goliatone\\flatg\\config\\drivers\\JsonConfigDriver');
            $this->registerDriver('ini',  'goliatone\\flatg\\config\\drivers\\IniConfigDriver');
            $this->registerDriver('yml',  'goliatone\\flatg\\config\\drivers\\YamlConfigDriver');
            $this->registerDriver('yaml', 'goliatone\\flatg\\config\\drivers\\YamlConfigDriver');
        }

        /**
         * @param $extension
         * @param $driver
         */
        public function registerDriver($extension, $driver)
        {
            $this->drivers[$extension] = $driver;
        }


        /**
         * @param $filename
         * @return mixed
         * @throws \InvalidArgumentException
         */
        public function import($filename)
        {
            if(!file_exists($filename))
            {
                throw new \InvalidArgumentException("Configuration file {$filename} not loaded");
            }

            $driver = $this->driverFromPath($filename);
            return $driver->import($filename);
        }


        /**
         * @param $path
         * @param array $data
         * @return mixed
         */
        public function save($path, $data)
        {
            $driver = $this->driverFromPath($path);
            return $driver->save($path, $data);
        }


        //TODO: This is ugly, how do we mange?
        /**
         * @param string $content
         */
        public function load($content){}

        //TODO: This is ugly, how do we mange?
        /**
         * @param array $content
         */
        public function format($content){}

        /**
         * @param $filename
         * @return mixed
         * @throws \InvalidArgumentException
         */
        protected function driverFromPath($filename)
        {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if($this->extension === $ext) return $this->driver;

            $this->extension = $ext;

            if(!array_key_exists($ext, $this->drivers))
                throw new \InvalidArgumentException("Extension {$ext} has no driver assigned.");

            $DriverClass = $this->drivers[$ext];
            $this->driver = new $DriverClass();

            return $this->driver;
        }
    }
}