<?php namespace goliatone\flatg\config\drivers {


    class XmlConfigDriver extends AbstractConfigDriver
    {
        /**
         * Imports a config file and makes
         * content available for a Config
         * instance.
         *
         * @param  string $file File path
         * @throws \Exception
         * @return array Configuration object
         */
        public function load($content)
        {
            try{
                $config = (array) new \SimpleXMLElement($content);

                //In order to make a recursive anonymous call we need
                //to pass by reference the closure...
                $parser = function(&$value) use(&$parser)
                {
                    $value = is_object($value) ? (array) $value : $value;

                    if(is_array($value))
                    {
                        array_walk($value, $parser);
                    }
                };

                array_walk($config, $parser);
            }
            catch(\Exception $e)
            {
                throw new \Exception("File {$this->path} could not be parsed as valid XML.",0,$e);
            }

            return $config;
        }

        /**
         * Formats and saves a configuration
         * object.
         * http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes/
         * @param array $data Configuration object
         * @return string
         */
        public function format($content)
        {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");

            // function call to convert array to xml
            $this->arrayToXml($content, $xml);

            //saving generated xml file
//            $xml->asXML('file path and name');

            return $xml->asXML();

        }

        protected function arrayToXml($data, &$xml) {
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    if(!is_numeric($key)){
                        $subnode = $xml->addChild("$key");
                        $this->arrayToXml($value, $subnode);
                    }
                    else{
                        $subnode = $xml->addChild("item$key");
                        $this->arrayToXml($value, $subnode);
                    }
                }
                else {
                    $xml->addChild("$key","$value");
                }
            }
        }

    }
}