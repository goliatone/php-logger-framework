<?php namespace goliatone\flatg\config\drivers {

    use Symfony\Component\Yaml\Yaml;

    class YamlConfigDriver extends AbstractConfigDriver
    {


        public function load($content)
        {
            return Yaml::parse($content);
        }


        public function format($content)
        {
            return Yaml::dump($content);
        }

    }
}