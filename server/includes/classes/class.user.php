<?php

/**
 * User class
 */
class User {

    public $id;

    public $email;

    public $username;

    public $name;

    public $role;

    /**
     * Retrives logged in user
     * 
     * @return  Returns a copy if itself
     */
    public function __construct()
    {   
        $auth_string = $_COOKIE["rpihome_auth"];


        if(!$auth_string){
            return new Error(_("User not logged in!"), 401);
        }

        $database = new Database;

        $result = $database->query("SELECT id, expire FROM authentications WHERE auth_string = ? LIMIT 1", array($auth_string));

        if($result->num_rows != 1){
            return new Error(_("Invalid authentication!"), 401); 
        }

        $auth_array = $result->fetch_assoc();

        if($auth_array["expire"] <= time()){
            return new Error(_("Session has expired!"), 401);
        }

        $result = $database->query("SELECT id, email, username, name, role FROM users WHERE id = ? LIMIT 1", [$auth_array['id']]);

        if($result->num_rows != 1) {
            return new Error(_("User not found!"), 401);
        }

        $user = $result->fetch_assoc();

        $this->id = $user['id'];
        $this->email = $user['email'];
        $this->username = $user['username'];
        $this->name = $user['name'];
        $this->role = $user['role'];
        
        global $user;

        $user = $this;

    }

    /**
     * Checks if user has entered correct crendetials to a valid user
     */
    public function login($credentials){

        $database = new Databse();

        $sql = "SELECT id, password_hash, password_salt FROM users WHERE ";

        if($this->is_email($credentials['login']))
            $sql .= "email";
        else
            $sql .= "username";

        $sql .= " = ? LIMIT 1";

        $result = $database->query($sql, [$credentials['login']]);

        if($result->num_rows != 1){
            return new Error(_("User not found!"), 401);
        }

        $password_array = $result->fetch_assoc();

        $password_hash = $password_array['password_hash'];
        $password_salt = $password_array['password_salt'];

        
        $user_hash = password_hash($credentials['password'] . $password_salt, PASSWORD_BCRYPT);
        

        if(!hash_equals($password_hash, $user_hash)){
            return new Error(_("Login and password doesn't match!"), 401);
        } 

        $result = $database->query("SELECT id, email, username, name, role FROM users WHERE id = ? LIMIT 1", [$password_array['id']]);

        $user = $result->fetch_assoc();

        $this->id = $user['id'];
        $this->email = $user['email'];
        $this->username = $user['username'];
        $this->name = $user['name'];
        $this->role = $user['role'];

        $auth = $this->new_auth_string();

        if(is_error($auth)){
            return $auth;
        }

        global $user;

        $user = $this;

        return true;
    }

    /**
     * Create a new user with the following credentials
     */
    public function sign_up($credentials){

        if($credentials['password'] != $credentials['password2']){
            return new Error(_("Passwords doesn't match!"));
        }

        if(!$this->is_email($credentials['email'])){
            return new Error(_("Invalid email!"));
        }
        
        if(!$this->validate_password($credentials['password'])){
            return new Error(_("Password needs to contain one uppercase letter, one lowercase letter, one number and one special character!"));
        }

        $database = new Database();

        $result = $database->query("SELECT id FROM user WHERE email = ? LIMIT 1", [$credentials['email']]);

        if( $result->num_rows > 0 ){
            return new Error(_("A user account with this email already exsist!"));
        }
        
        $password_salt = openssl_random_pseudo_bytes(16);

        $password_hash = password_hash($credentials['password'] . $password_salt, PASSWORD_BCRYPT);

        $result = $database->update("INSERT INTO users (email, username, name, role, password_hash, password_salt, date) 
            VALUES(?, ?, ?, ?, ?, ?, ?)", [
                $credentials['email'],
                $credentials['username'],
                $credentials['name'],
                "user",
                $password_hash,
                $password_salt,
                time()
            ]);

        if($result !== true){
            return new Error(_("Something went wrong! Please try again later."));
        }

        $auth = $this->new_auth_string();

        if(is_error($auth)){
            return $auth;
        }

        global $user;

        $user = $this;

        return true;
    }

    /**
     * Creates a new authentication for this account
     */
    private function new_auth_string(){

        $database = new Database();

        $http_user_agent = $_SERVER['HTTP_USER_AGENT'];

        $auth_string = $http_user_agent . $this->id . $this->email . time();
        
        $hashed_auth_string = hash("sha256", $auth_string, false);

        $expire = time() + 3600 * 24 * 365;

        $result = $database->update("INSERT INTO authentications (user_id, auth_string, http_user_agent, timestamp, location, ip_adress, expire) 
            VALUES (?, ?, ?, ?, ?, ?)", [
                $this->id,
                $auth_string,
                $http_user_agent,
                time(),
                "Sweden",
                $_SERVER['REMOTE_ADDR'],
                $expire
            ]);

        if($result === true){

            setcookie('rpihome_auth', $hashed_auth_string, $expire, "/");

            return true;

        } else {
            return new Error(_("Something went wrong during the authentication process!"), 400);
        }
    }


    /**
     * Checks if input is a valid email adress
     * 
     * @return boolean
     */
    private function is_email($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           return false;
        }
        return true;
    }

    /**
     * Validates users password
     * 
     * @return boolean
     */
    private function validate_password($password){
        return (bool) preg_match("^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$", $password);
    }
}