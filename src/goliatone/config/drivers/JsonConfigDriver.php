<?php namespace goliatone\flatg\config\drivers {

    /**
     * Class JsonConfigDriver
     * @package goliatone\flatg\config\drivers
     */
    class JsonConfigDriver extends AbstractConfigDriver
    {

        public function load($content)
        {
            return json_decode($content, true);
        }

        public function format($content)
        {
            return json_encode($content);
        }

    }
}