<?php namespace goliatone\flatg\logging\formatters\transformers {


    use goliatone\flatg\logging\core\ILogMessageFormatTransformer;
    use goliatone\flatg\logging\helpers\Utils;

    class DateTimeTransformer extends BaseTransformer implements ILogMessageFormatTransformer
    {
        public $format = Utils::ISO8601;

        public function transform($resource, $provider = NULL)
        {
            return $resource->format($this->format);
        }
    }
}