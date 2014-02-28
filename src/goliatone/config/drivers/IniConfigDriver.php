<?php namespace goliatone\flatg\config\drivers {


    class IniConfigDriver extends AbstractConfigDriver
    {

        public function load($content)
        {
            return parse_ini_file($content, true);
        }


        public function format($data)
        {
            //nothing here, move on.
        }

    }
}