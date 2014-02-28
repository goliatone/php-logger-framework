<?php namespace goliatone\flatg\logging\helpers {

    class Utils
    {

        /**
         * DateTime format ISO8601 compatible
         * with JS construct.
         */
        const ISO8601 = 'Y-m-d\T H:i:sP';

        /**
         * @param $object
         * @param bool   $fullyQualified
         * @param string $glue
         * @return mixed
         */
        static public function qualifiedClassName($object, $fullyQualified=TRUE, $glue='.')
        {
            if(is_string($object)) return $object;

            $name      = get_class($object);
            $className = str_replace("\\", $glue, $name);

            if($fullyQualified) return $className;

            if (preg_match('@\\\\([\w]+)$@', $name, $matches))
                $className = $matches[1];

            return $className;
        }

        /**
         * @param string $default
         * @return string
         */
        static public function getServerAddress($default='localhost')
        {
            return (isset ($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : $default;
        }

        /**
         * @param $bytes
         * @param int $precision
         * @return string
         */
        static public function fileSizeToString($bytes, $precision=2)
        {
            $units = array('B','KB','MB','GB','TB','PB', 'EB','ZB','YB');
            for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
            return sprintf("%.{$precision}f", $bytes).' '.@$units[$i];
        }

        /**
         * @param $value
         * @return bool|int
         */
        static public function stringToFileSize($value)
        {
            $str   = strtoupper(trim($value));
            $units = array('B','KB','MB','GB','TB','PB', 'EB','ZB','YB');
            if(! preg_match('/^([0-9.]+)\s?(KB|MB|GB)?$/', $str, $matches)) return FALSE;
            return (int) @$matches[1] * pow(1024, array_search(@$matches[2], $units));
        }

        /**
         * TODO: it should take a method to stringify objects.
         * TODO: Use $cStart = { and $cEnd = } so we can match at will
         * @param  string $message String template
         * @param  array  $context Context providing vars
         * @param  bool   $consume If true, missing matches will be
         *                         removed, else they are left.
         * @return string
         */
        public static function stringTemplate($message, array $context = array(), $consume = FALSE)
        {
            $getMatchReplace = function($match, $context) use($consume){
                $alt = $consume ? "" : "{".$match."}";
                $out = array_key_exists($match, $context) ? $context[$match] : $alt;
                return is_callable($out) ? call_user_func_array($out, func_get_args()) : $out;
            };

            $replace = array();
            preg_match_all('/\{([^}]+)}/', $message, $matches);

            foreach($matches[1] as $match) {
                $match = trim($match);
                $replace["{".$match."}"] = $getMatchReplace($match, $context);
            }
            //mb_strstr
            return strtr($message, $replace);
        }
    }
}