<?php namespace goliatone\flatg\logging\formatters\transformers {

    use goliatone\flatg\logging\helpers\Utils;

    class ObjectTransformer extends BaseTransformer
    {

        /**
         * @param $object
         * @param TransformManager $provider
         * @return string|void
         */
        public function transform($resource, $provider = NULL)
        {
            $fullyQualifiedClassName = Utils::qualifiedClassName($resource, TRUE, '\\');
            $handler = $provider->getHandler($fullyQualifiedClassName);

            //If we dont have an specific transformer but have a toString method we spit it out
            if($handler->getType() === 'default' && method_exists($resource, "__toString")) return $resource;
            //At this point, we either handle
            return $handler->transform($resource, $provider);
        }
    }
}