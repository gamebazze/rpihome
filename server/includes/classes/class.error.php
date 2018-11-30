<?php


/**
 * Class for handling errors.
 */
class Error {

    private $error_msg;

    private $http_status_code;

    function __construct( $error_msg, $http_status_code = 200){
        
        $this->error_msg = $error_msg;

        $this->http_status_code = $http_status_code;

    }


    /**
     * Returns the error message.
     * 
     * @return string $error_msg
     */
    function get_message() {

        return $error_msg;

    }

    /**
     * Returns the http status code.
     * 
     * @return int $http_status_code
     */
    function get_status_code() {

        return $http_status_code;
    }
}