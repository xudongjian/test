<?php

/* * ****** Array ******* */

/* Depreciated. see arrayIsEmpty(). */

function emptyArray($arr) {
    return (is_array($arr) === false || count($arr) === 0);
}

function arrayNotEmpty($arr) {
    return (is_array($arr) && (count($arr) > 0));
}

/**
 *  checks if is array, remove all empty elements.
 * if is not array, return the param.
 * @param type $arr
 * @return type array
 */
function arrayFilterEmptyValues($arr) {
    if (is_array($arr))
        return array_filter($arr);
    else
        return $arr;
}

function arrayExtractDistinctValue($models, $field) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field}) && isset($ret[$model->{$field}]) === false) {
                $ret[$model->{$field}] = $model->{$field};
            }
        }
    }
    return $ret;
}

function arrayExtractValue($models, $field) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field})) {
                $ret[] = $model->{$field};
            }
        }
    }
    return $ret;
}

function arrayExtractKeyValue($models, $field_key, $field_value) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field_key}) && isset($model->{$field_value})) {
                $ret[$model->{$field_key}] = $model->{$field_value};
            }
        }
    }

    return $ret;
}

/*
 * @param $arr array key value pair
 * @param $valueToLower bool if true then converts value to lower case.
 * @return an array containing lower case key and lower/normal case value.
 */

function arrayKeyToLower(array $arr, $valueToLower = false) {
    $ret = array();
    if ($valueToLower) {
        foreach ($arr as $key => $value) {
            $ret[strtolower($key)] = strtolower($value);
        }
    } else {
        foreach ($arr as $key => $value) {
            $ret [strtolower($key)] = $value;
        }
    }
    return $ret;
}

function arrayToCsv(array $arr, $delimiter = ', ') {
    $ret = implode($delimiter, $arr);
    return $ret;
}

/**
 *  Converts an array json object.
 * @param array $arr
 * @return json $arr
 */
function arrayToJson($arr, $unset = true) {
    if (is_array($arr) && count($arr) > 0) {
        $arr = CJSON::encode($arr);
    } else if ($unset) {
        $arr = null;
    }
    return $arr;
}

/**
 * Converts a json object to array.
 * @param json $json
 * @return array $arr
 */
function jsonToArray($json) {
    if (is_array($json) === false) {
        //  $this->{$attribute} = json_decode($this->{$attribute});
        $json = CJSON::decode($json);
    }
    return $json;
}

/* * ****** Number ******* */

function numRoundToNearestN($num, $n) {
    return (round($num / $n) * $n);
}

function numGetDecimal($num, $digits = 2) {
    $t = explode('.', $num);
    if (isset($t[1]))
        return str_pad($t[1], $digits, '0', STR_PAD_RIGHT);
    else
        return str_pad('', $digits, '0', STR_PAD_RIGHT);
}

function numIsEven($num) {
    return ($num % 2 == 0);
}

/* * ****** String ******* */

function strIsEmpty($value, $trim = false) {
    if ($trim) {
        $value = trim($value);
    }
    return ($value === null || $value == '');
}

function strStartsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function strEndsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function strContains($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
}

/**
 * @param type $length
 * @return type 32 chars.
 */
function strRandom($length = 32) {
    return(strtoupper(substr(str_shuffle(MD5(microtime())), 0, $length)));
}

/**
 *
 * @param type $length
 * @return type variable length.
 */
function strRandomLong($length = 10) {
    $randomstring = '';
    // Length of md5 hash.
    $len_per_loop = 32;
    if ($length > $len_per_loop) {
        $multiplier = floor($length / $len_per_loop);
        $remainder = $length % $len_per_loop;
        for ($i = 0; $i < $multiplier; $i++) {
            $randomstring .= substr(str_shuffle(md5(rand())), 0, $len_per_loop);
        }
        $randomstring .= substr(str_shuffle(md5(rand())), 0, $remainder);
    } else {
        $randomstring = substr(str_shuffle(md5(rand())), 0, $length);
    }
    return strtoupper($randomstring);
}

/* * ****** Encryption ******* */

function encrypt($value) {
    return hash('sha256', $value);
}

/* * ****** Datetime ******* */

function calYearsFromDatetime($datetimeFrom, $datetimeTo = 'today') {
    return date_diff(date_create($datetimeFrom), date_create($datetimeTo))->y;
}

/*
  function dateToDisplay($dateStr, $format='Y年m月d日') {
  $date = new DateTime($dateStr);
  return $date->format($format);
  }
 */
/*
  function datetimeToDisplay($date, $twoLines=false) {
  if (isset($date) && (strtotime($date) !== false)) {
  $date = new DateTime($date);
  }
  if ($twoLines) {
  return $date->format('Y年m月d日') . '<br />' . $date->format('H:i');
  } else {
  return ($date->format('Y年m月d日 H:i'));
  }
  }
 */

/**
 *
 * @param string $dateField '2014-06-20'.
 * @param string $timeField '12:20'.
 * @return string $str a string representing datetime.
 */
function combineDateTimeField($dateField, $timeField) {
    $date = new DateTime($dateField);
    $str = $date->format('Y-m-d') . ' ' . $timeField;
    return $str;
}

function isLeapYear($year) {
    if ($year % 400 == 0) {
        return true;
    } else if ($year % 100 == 0) {
        return false;
    } else if ($year % 4 == 0) {
        return true;
    } else
        return false;
}

/* * ** File system *** */

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function readFileToBytes($file) {
    $fp = fopen($file, 'r');
    $content = fread($fp, filesize($file));
    fclose($fp);
    return $content;
}

function createDirectory($dir) {
    if (is_dir($dir) === false) {
        if (mkdir($dir) === false) {
            throw new CException("Error saving data - failed to create directory");
        }
    }
}

// Deletes the directory with all sub-directories and files.
function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != DIRECTORY_SEPARATOR) {
            $dirPath .=DIRECTORY_SEPARATOR;
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDirectory($file);
            } else {
                deleteFile($file);
            }
        }
        rmdir($dirPath);
    }
}

function deleteFile($filename) {
    if (file_exists($filename)) {
        chmod($filename, 0777);
        return unlink($filename);
    }
    return true;
}

function getFileExtension($file) {
    $name = $file->name;
    $extension = substr($name, strrpos($name, "."));
    return $extension;
}

function createSignature($values, $secret)
{
//    sort($values, SORT_STRING);
//    $values = implode($values);
    ksort($values, SORT_STRING);
    $tmpStr = '';
    foreach($values as $k=>$v){
        if($v !== ''){
            $tmpStr .= $k.'='.$v.'&';
        }

    }
    $tmpStr = rtrim($tmpStr, '&');
    $tmpStr = md5($tmpStr.$secret);
    return $tmpStr;
}

function checkSignature($values, $secret, $signature)
{
    ksort($values, SORT_STRING);
    $tmpStr = '';
    foreach($values as $k=>$v){
        if($v !== ''){
            $tmpStr .= $k.'='.$v.'&';
        }

    }
    $tmpStr = rtrim($tmpStr, '&');
    $tmpStr = md5($tmpStr.$secret);
    if($tmpStr == $signature){
        return true;
    }else{
        return false;
    }
}