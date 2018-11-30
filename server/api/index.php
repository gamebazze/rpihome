<?php 
require_once "./includes/include.php";

$action = $_GET['action'];

switch($action){
    case "login":

        if(isset($_POST['login']) && isset($_POST['password'])){
            $user = new User();
            $response = $user->login($_POST);

            if( !is_error($response) ){
                handle_response_message(true, null, 200);
            } else {
                handle_response_message(false, $response->get_message(),  $response->get_status_code());
            }

        } else {
            handle_response_message(false, _("Login and password cannot be empty!"), 400);
        }

        break;

    case "sign-up":

        if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password2']) && isset($_POST['password'])){
            $user = new User();
            $response = $user->login($_POST);

            if( !is_error($response) ){
                handle_response_message(true, null, 200);
            } else {
                handle_response_message(false, $response->get_message(),  $response->get_status_code());
            }

        } else {
            handle_response_message(false, _("Login and password cannot be empty!"), 400);
        }

        break;
        
}