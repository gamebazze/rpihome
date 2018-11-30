<?php

/**
 * Checks if input is an Error class
 * 
 * @return boolean
 */
function is_error($object){
    return is_a($object, "Error", false);
}

/**
 * Handles the api response
 * 
 * @param array $data The data to respond with
 * @param int $http_status_code The http status code to respond with
 */
function handle_response_data(array $data, $http_status_code){

    http_response_code($http_status_code);

    echo json_encode($data);

    exit;
}

/**
 * Handles the api response
 * 
 * @param boolean $sucess
 * @param string $message The message to respond with
 * @param int $http_status_code The http status code to respond with
 */
function handle_response_message($success, $message, $http_status_code){

    http_response_code($http_status_code);

    echo json_encode(array("success" => $success, "message" => $message));

    exit;
}