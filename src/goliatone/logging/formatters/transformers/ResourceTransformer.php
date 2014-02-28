<?php namespace goliatone\flatg\logging\formatters\transformers {


    class ResourceTransformer extends BaseTransformer
    {

        public function transform($resource, $provider = NULL)
        {
            return $provider->transform(stream_get_meta_data($resource), $provider);
        }
    }
}