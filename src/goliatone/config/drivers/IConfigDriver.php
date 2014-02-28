<?php namespace goliatone\flatg\config\drivers {


    interface IConfigDriver
    {


        /**
         * Imports a config file and makes
         * content available for a Config
         * instance.
         *
         * @param  string $file File path
         * @return array Configuration object
         */
        public function import($path);

        /**
         * Formats and saves a configuration
         * object.
         *
         * @param $path
         * @param array $data Configuration object
         * @return string
         */
        public function save($path, $data);

        /**
         * Imports a config file and makes
         * content available for a Config
         * instance.
         *
         * @param  string $content
         * @return array Configuration object
         */
        public function load($content);

        /**
         * @param array $content
         * @return mixed
         */
        public function format($content);
    }
}