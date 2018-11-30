<?php 

if( !isset($_COOKIE['rpihome_auth']) ){

    http_response_code(403);
    return "<h1>Unauthorized</h1>";
    

}