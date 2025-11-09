<?php

use App\Classes\emtSign;
use App\Classes\emtURL;
use Carbon\Carbon;

if (!function_exists('length')) {
	function length($element)
	{
		$count = 0;
		if (is_array($element) || is_object(($element))) {
			$count = count($element);
		} elseif (is_string($element)) {
			$count = strlen($element);
		} else {
			$count = 0;
		}
		return ($count);
	}
}

if (!function_exists('emt_signedRoute')) {
    function emt_signedRoute($name, $parameters = [], $absolute = true)
    {
        return emtURL::signedRoute($name, $parameters, $absolute);
    }
}

if (!function_exists('emt_signedURL')) {
    function emt_signedURL($path)
    {
        return emtURL::signedURL($path);
    }
}
if (!function_exists('emt_sign')) {
    function emt_sign($value)
    {
        return emtSign::sign($value);
    }
}

if (!function_exists('emt_stringSVClean')) {
	function emt_stringSVClean(string $vcString, string $vcSeparator = ',')
	{
		$aSearch = [" ", ",", ".", "\n\r", "\r\n", "\n", "\r"];
		$vcString = str_replace($aSearch, $vcSeparator, $vcString);
		$vcString = preg_replace("/\\" . $vcSeparator . "+/", $vcSeparator, $vcString);
		$vcString = trim($vcString, $vcSeparator);

		return $vcString;
	}
}

if (!function_exists('getBase64File')) {
	function getBase64File(string $vcFilePath)
	{
		if (!file_exists($vcFilePath)) {
			return null;
		}

		$fSource = fopen($vcFilePath, 'r');
		$content = stream_get_contents($fSource);
		fclose($fSource);

		return base64_encode($content);
	}
}

if (!function_exists('array_recursive_search_key_map')) {
	function array_recursive_search_key_map($needle, $haystack)
	{
		$needle = str_replace('\\', '', $needle);
		foreach ($haystack as $first_level_key => $value) {
			if ($needle === $value) {
				return array($first_level_key);
			} elseif (is_array($value)) {
				$callback = array_recursive_search_key_map($needle, $value);
				if ($callback) {
					//return array_merge(array($first_level_key), $callback);
					return $first_level_key;
				}
			}
		}
		return false;
	}
}

if (!function_exists('emt_file_size')) {
	function emt_file_size($iSize)
	{
		$aSizes = array('bytes', 'KB', 'MB', 'GB', 'TB');
		// Calcular vcSize
		$iEscala = intval(log($iSize, 2));
		$iEscala = ($iEscala < 1) ? 0 : $iEscala;
		$iOrdenB10 = intdiv($iEscala, 10);
		$iOrdenB10 = ($iOrdenB10 < 1) ? 0 : $iOrdenB10;
		return number_format($iSize / pow(1024, $iOrdenB10), 1, ',', '.') . ' ' . $aSizes[$iOrdenB10];
	}
}

if (!function_exists('emt_variable_get')) {
	function emt_variable_get($path, $array) {
		$path = explode('.', $path); //if needed
		$temp = $array;

		foreach($path as $key) {
			if (is_array($temp)) {
				$temp = $temp[$key];
			} else {
				$temp = $temp->{$key};
			}
		}
		return $temp;
	}
}

if (!function_exists('emt_contains')) {
    function emt_contains($string, Array $search, $caseInsensitive = true) {
        $matches = [];
        $exp = '/'
            . implode('|', array_map('preg_quote', $search))
            . ($caseInsensitive ? '/i' : '/');
        $result = preg_match($exp, $string, $matches) ? true : false;
        return $matches;
    }
}

if (!function_exists('string2decimal')) {
    function string2decimal($value) {
        $number = str_replace(',','.',str_replace('.','',$value));
        $number = str_replace('%', '', $number);
		$number = str_replace('€', '', $number);
        $number = trim($number);
        return (is_numeric($number) ? $number*1:false);
    }
}
if (!function_exists('decimal2string')) {
    function decimal2string($value) {
        return number_format($value,2,',','.');
    }
}

if (!function_exists('url_segments')) {
    function url_segments($uri) {
        $pos = strpos($uri, '?');
        if ($pos > 0) {
            $uri = substr($uri, 0, $pos);
        }
        $segments = explode('/', str_replace(''.url('').'', '', $uri));

        return array_values(array_filter($segments, function ($value) {
            return $value !== '';
        }));
    }
}

if (!function_exists('clean_email')) {
    function clean_email($value) {
        $value = str_replace(' ','', $value);
        return str_replace(';',',', $value);
    }
}
