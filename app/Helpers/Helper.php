<?php

use GuzzleHttp\Client;

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
        return Auth::user()->id . time() . rand(100, 999);
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
        if (!$isucCase) {
            return ucwords($text);
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

        return date('d-M-Y H:i:s', strtotime($date));
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


