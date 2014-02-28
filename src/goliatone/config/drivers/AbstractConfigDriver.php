<?php namespace goliatone\flatg\config\drivers {


    /**
     * Class AbstractConfigDriver
     * @package goliatone\flatg\config\drivers
     */
    abstract class AbstractConfigDriver implements IConfigDriver
    {


        public $path;

        public function save($path, $data)
        {
            $output = $this->format($data);

            $file = new \SplFileObject($path, "w");
            if(!$file->fwrite($output))
            {
                //We were unable to save the file
                throw new \RuntimeException("Configuration file could not be saved to {$path}");
            }

            return $output;
        }

        public function import($path)
        {
            $this->path = $path;

            $content = file_get_contents($path);

            return $this->load($content);
        }

        /**
         * Imports a config file and makes
         * content available for a Config
         * instance.
         *
         * @param string $content
         * @return array Configuration object
         */
        abstract public function load($content);


        abstract public function format($content);

    }
}