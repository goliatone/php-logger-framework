<?php namespace goliatone\flatg\logging\core {

    interface ILogMessageFormatTransformer
    {
        /**
         * @param $resource
         * @param null $provider
         * @return mixed
         */
        public function transform($resource, $provider = NULL);
    }
}