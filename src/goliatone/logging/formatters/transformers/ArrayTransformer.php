<?php namespace goliatone\flatg\logging\formatters\transformers {


    class ArrayTransformer extends BaseTransformer
    {

        public function transform($resource, $provider = NULL)
        {

            $sizeOf = sizeof($resource);
            $keys   = array_keys($resource);
            $output = '';

            for ($i=0; $i < $sizeOf; $i++) $output .= $provider->transform($resource[$keys[$i]], $provider);

            return $output;
        }
    }
}