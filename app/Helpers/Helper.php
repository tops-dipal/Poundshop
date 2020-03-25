<?php

use GuzzleHttp\Client;
use Carbon\Carbon;

// use DB;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!function_exists('makeNulltoBlank')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function makeNulltoBlank($data) {
        array_walk_recursive($data, function (&$item) {
            $item = (is_null($item)) ? strval($item) : $item;
        });
        return $data;
    }

}


//RestFul API base URL
if (!function_exists('apiBaseUrl')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function apiBaseUrl() {
        return url('/') . '/api/';
    }

}

//Calling RestFul API URL
if (!function_exists('apiRequest')) {

    /**
     * Calling RestFul API URL
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function apiRequest($apiRoute, $data = array(), $method = "get") {
        if (!empty($apiRoute)) {
            $config['headers']['Authorization'] = 'Bearer ' . session('apiToken');
            $client                             = new Client($config);
            $result                             = $client->$method(apiBaseUrl() . $apiRoute, $data);
            $response                           = json_decode($result->getBody());
        }
        else {
            $response['msg'] = 'apiRoute cannot be empty.';
        }

        return $response;
    }

}

// Used to format array
if (!function_exists('helper_array_column')) {

    /**
     * format array
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function helper_array_column($input, $array_index_key = NULL, $array_value = NULL) {
        $result = array();

        if (count($input) > 0) {
            foreach ($input as $key => $value) {

                if (is_array($value)) {
                    @$result[is_null($array_index_key) ? $key : (string) (is_callable($array_index_key) ? $array_index_key($value) : $value[$array_index_key])] = is_null($array_value) ? $value : (is_callable($array_value) ? $array_value($value, $key) : $value[$array_value]);
                }
                else if (is_object($value)) {
                    $result[is_null($array_index_key) ? $key : (string) $value->$array_index_key] = is_null($array_value) ? $value : $value->$array_value;
                }
                else {
                    $result[is_null($array_index_key) ? $key : (string) (is_callable($array_index_key) ? $array_index_key($value, $key) : $key)] = is_null($array_value) ? $value : (string) (is_callable($array_value) ? $array_value($value, $key) : $value);
                }
            }
        }

        return $result;
    }

}

// Used to format array
if (!function_exists('helper_array_column_multiple_key')) {

    /**
     * Format array in multi-dimensional array
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function helper_array_column_multiple_key($input, $array_index_key = NULL, $add_extra_key = FALSE, $array_value = NULL) {
        $result = array();

        $add_extra_key_string = "";

        if ($add_extra_key) {
            $add_extra_key_string = "[]";
        }

        if (@count($input) > 0) {
            $key_string = (implode("", array_map(function($value) {
                                return '[(string)$value["' . $value . '"]]';
                            }, $array_index_key)) . $add_extra_key_string);


            foreach ($input as $key => $value) {
                if (is_array($value) && $key_string) {
                    $execution = '$result' . $key_string . ' = is_null($array_value) ? $value : $value[$array_value];';
                    eval($execution);
                }
            }
        }
        return $result;
    }

}

// create N level node array
if (!function_exists('buildTree')) {

    /**
     * create N level node array
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function buildTree($elements, $parentId = 0) {

        $branch = array();

        foreach ($elements as $element) {

            if ($element->parent_id == $parentId) {

                $children = buildTree($elements, $element['id']);

                if ($children) {

                    $element->children = $children;
                }

                $branch[] = $element;
            }
        }

        return $branch;
    }

}

// returns system formated date
if (!function_exists('system_date')) {

    /**
     * returns system formated date
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function system_date($date = "") {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $date = str_replace('/', '-', $date);

        return date('d-M-Y', strtotime($date));
    }

}

// returns db formated date
if (!function_exists('db_date')) {

    /**
     * returns db formated date
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function db_date($date = "") {
        if (!empty($date)) {
            $date = str_replace('/', '-', $date);

            return date('Y-m-d', strtotime($date));
        }
    }

}


if (!function_exists('pr')) {

    /**
     * description
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function pr($data) {
        echo '<pre>';
        print_r($data);
        exit;
    }

}

if (!function_exists('pr1')) {

    /**
     * description
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function pr1($data) {
        echo '<pre>';
        print_r($data);
    }

}

if (!function_exists('object_to_array')) {

    /**
     * object to array conversation
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function object_to_array($data) {
        if (!empty($data)) {
            return json_decode(json_encode($data), true);
        }
        else {
            return array();
        }
    }

}

if (!function_exists('get_sku')) {

    /**
     * Returns system generated sku
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function get_sku() {
        $new_id = 1;

        $last_record = DB::table('products')->selectRaw('MAX(id) as last_id')->pluck('last_id')->toArray();

        if (!empty($last_record)) {
            $new_id = $last_record[0] + 1;
        }

        $milisec = microtime(true);

        $milisec = substr($milisec, strpos($milisec, ".") + 2);

        $unique_str = Auth::user()->id . $new_id . $milisec . rand(0, 9);

        usleep(100);

        return $unique_str;
    }

}

if (!function_exists('last_query_start')) {

    /**
     * Returns system generated sku
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function last_query_start() {
        DB::enableQueryLog();
    }

}

if (!function_exists('last_query_end')) {

    /**
     * Returns system generated sku
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function last_query_end() {
        $query = DB::getQueryLog();
        dd($query);
    }

}


if (!function_exists('arraySearchKey')) {
    /**
     * return key value of array
     * @author : Hitesh Tank
     * @param
     * @return
     */
//    function arraySearchKey($array,$key,$value)
//    {
//        if(is$arrayKey = (array_search($value, array_column($array,$key)))){
//            dd($array[$arrayKey]);
//        }else{
//            dd('test');
//        }
//    }
}


if (!function_exists('ucCase')) {

    /**
     * return form of either uc case or normal text
     * @author : Hitesh Tank
     * @param
     * @return
     */
    function ucCase($text, $isucCase = false) {
        $from = [' from', ' From', ' Has', ' has', ' been', ' Been', ' of', ' to', ' with', ' by', ' is', ' an', ' Of', ' To', ' With', ' By', ' Is', ' An', ' on', ' On'];
        $to   = [' from', ' from', ' has', ' has', ' been', ' been', ' of', ' to', ' with', ' by', ' is', ' an', ' of', ' to', ' with', ' by', ' is', 'an', ' on', ' on'];
        if (!$isucCase) {
            $textString = ucwords($text);
            //return strstr($replaceWords, $textString);
            return str_replace($from, $to, $textString);
            //return str_ireplace(array_keys($replaceWords), array_values($replaceWords), $textString);
        }
        else {
            return $text;
        }
    }

}

// returns system formated date
if (!function_exists('system_date_time')) {

    /**
     * returns system formated date
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function system_date_time($date = "") {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $date = str_replace('/', '-', $date);

        return date('d-M-Y h:iA', strtotime($date));
    }

}

if (!function_exists('priceFormate')) {

    /**
     * returns price formate
     * @author : Hitesh Tank
     * @param
     * @return
     */
    function priceFormate($value) {
        return number_format($value, 2, ".", "");
    }

}


// returns system formated date
if (!function_exists('booking_date_time')) {

    /**
     * returns system formated date
     * @author : Shubham Dayma
     * @param
     * @return
     */
    function booking_date_time($date = "") {

        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $date = str_replace('/', '-', $date);

        //return date('l d-M-Y', strtotime($date));
        $weekStr  = date('l', strtotime($date));
        $weekDate = date('d-M-Y', strtotime($date));
        return $weekStr . '<br> ' . $weekDate;
    }

}


if (!function_exists('booking_prev_day_date')) {

    /**
     * returns system formated date
     * @author : Mohit Trivedi
     * @param
     * @return
     */
    function booking_prev_day_date($date = "") {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $date = str_replace('/', '-', $date);

        return date('Y-m-d', strtotime($date . " - 1 day"));
    }

}

if (!function_exists('booking_next_day_date')) {

    /**
     * returns system formated date
     * @author : Mohit Trivedi
     * @param
     * @return
     */
    function booking_next_day_date($date = "") {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $date = str_replace('/', '-', $date);

        return date('Y-m-d', strtotime($date . " + 1 day"));
    }

    function get_week_num($date = "") {
        if (empty($date)) {
            $ddate = date('W');
        }
        else {
            if ($date == date('Y-m-d')) {
                $ddate = date("W", strtotime('sunday this week', strtotime($date)));
            }
            else {
                $ddate = date("W", strtotime('sunday next week', strtotime($date)));
            }
        }
        /* $date = new DateTime($ddate);
          $week = $date->format("W"); */
        return $ddate;
    }

    /**
     * @author Hitesh Tank
     * @param DateTime $date
     * @return type
     */

    /** Get Week num based on date
      author Kinjal
     * */
    function getWeekNum($date = "") {
        if (empty($date)) {
            $ddate = date('Y-m-d H:i:s');
        }
        else {
            $ddate = date('Y-m-d', strtotime("+1 days", strtotime($date)));
        }
        $date = new DateTime($ddate);
        $week = $date->format("W");
        return $week;
    }

    /**
     * @author Hitesh Tank
     * @param DateTime $date
     * @return type
     */
    function getStartAndEndDate($week, $year) {
        $dateTime             = new DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date']   = $dateTime->format('Y-m-d');
        return $result;
    }

    /*     * get previous week date based on specific date
      author Kinjal
     * */

    function booking_prev_week_date($date = "") {

        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        return date("Y-m-d", strtotime('sunday previous week', strtotime($date)));
    }

    /*     * get next week date based on specific date
      author Kinjal
     * */

    function booking_next_week_date($date = "") {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        return date("Y-m-d", strtotime('saturday', strtotime($date)));
    }

    /*     * get start and end date of week
      author Kinjal
     * */

    function x_week_range($date) {
        $ts                   = strtotime($date);
        $start                = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        $result['start_date'] = date('Y-m-d', $start);
        $result['end_date']   = date('Y-m-d', strtotime('next saturday', $start));
        return $result;
    }

    /**
     * @author Hitesh Tank
     * @param type $date
     * @return type
     * Previous Date
     */
    function bookingPreviousWeekDate($date = "") {
        if (empty($date)) {
            $date = Carbon::now();
        }
        else {
            $date = Carbon::parse($date);
        }
        return $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }

    /**
     * @author Hitesh Tank
     * @param type $date
     * @return type
     * Next Date
     */
    function bookingNextWeekDate($date = "") {
        if (empty($date)) {
            $date = Carbon::now();
        }
        else {
            $date = Carbon::parse($date);
        }
        return $date->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
    }

    /**
     * @author Hitesh Tank
     * @param type $date
     * @return type
     */
    function weekDateRange($date) {
        $result               = [];
        $date                 = Carbon::parse($date);
        $result['start_date'] = $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $result['end_date']   = $date->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        return $result;
    }

    function dateFormateShowDate($dateTime = '') {
        if (empty($dateTime)) {
            $date = date('d-M-Y H:i');
        }
        else {
            $date = date('d-M-Y H:i', $dateTime);
        }
        return $date;
    }

    function getRequestAgent($user_agent) {
        $bname    = 'Unknown';
        $platform = 'Unknown';
        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            $bname = 'Internet Explorer';
            $ub    = "W";
        }
        elseif (preg_match('/Firefox/i', $user_agent)) {
            $bname = 'Mozilla Firefox';
            $ub    = "W";
        }
        elseif (preg_match('/Chrome/i', $user_agent)) {
            $bname = 'Google Chrome';
            $ub    = "W";
        }
        elseif (preg_match('/Safari/i', $user_agent)) {
            $bname = 'Apple Safari';
            $ub    = "W";
        }
        elseif (preg_match('/Opera/i', $user_agent)) {
            $bname = 'Opera';
            $ub    = "W";
        }
        elseif (preg_match('/Netscape/i', $user_agent)) {
            $bname = 'Netscape';
            $ub    = "W";
        }
        elseif (preg_match('/PostmanRuntime/i', $user_agent)) {
            $bname = 'PostMan';
            $ub    = "M";
        }
        else {
            $ub = "M";
        }
        return $ub;
    }

}


