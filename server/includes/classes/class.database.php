<?php
class database {
        // The database connection
        protected static $connection;

        /**
         * Connect to the database
         * 
         * @return bool false on failure / mysqli MySQLi object instance on success
         */
        public function connect() {    
            // Try and connect to the database
            if(!isset(self::$connection)) {
                // Load configuration as an array. Use the actual location of your configuration file
                $config = parse_ini_file( dirname(__FILE__) . '/config.ini'); 
                self::$connection = new mysqli('localhost',$config['username'],$config['password'],$config['dbname']);
            }

            // If connection was not successful, handle the error
            if(self::$connection === false) {
                // Handle error - notify administrator, log to a file, show an error screen, etc.
                return false;
            }
            return self::$connection;
        }

        /**
         * Query the database
         *
         * @param $query The query string
         * @param $params bind_param array
         * @return mixed The result of the mysqli::query() function
         */
        public function query(string $query, array $params = array()) {
            // Connect to the database
            $connection = $this -> connect();

            // Prepare statement
            $stmt = $connection -> prepare($query);

            if($stmt === false) {
                return false;
            }

            if(!empty($params)){
                
                /* Binding params to prepared statement*/
                $types = str_repeat('s', count($params));
                $stmt -> bind_param($types, ...$params);

            }
            /* Execute statement */
            $stmt->execute();
            
            /* Fetch result to array */
            $result = $stmt->get_result();

            return $result;
        }

        /**
         * Query the database
         *
         * @param $query The query string
         * @param $params bind_param array
         * @return mixed The result of the mysqli::query() function
         */
        public function update(string $query, array $params = array()) {
            // Connect to the database
            $connection = $this -> connect();

            // Prepare statement
            $stmt = $connection -> prepare($query);

            if($stmt === false) {
                return false;
            }

            if(!empty($params)){
                
                /* Binding params to prepared statement*/
                $types = str_repeat('s', count($params));
                $stmt -> bind_param($types, ...$params);

            }
            /* Execute statement */
            $result = $stmt->execute();

            return $result;
        }

        /**
         * Fetch rows from the database (SELECT query)
         *
         * @param $query The query string
         * @param $params bind_param array
         * @return bool False on failure / array Database rows on success
         */
        public function select(string $query, array $params = array()) {
            $rows = array();
            $result = $this -> query($query, $params);
            if($result === false) {
                return false;
            }
            while ($row = $result -> fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

        /**
         * Fetch the last error from the database
         * 
         * @return string Database error message
         */
        public function error() {
            $connection = $this -> connect();
            return $connection -> error;
        }

        /**
         * Quote and escape value for use in a database query
         *
         * @param string $value The value to be quoted and escaped
         * @return string The quoted and escaped string
         */
        public function quote($value) {
            $connection = $this -> connect();
            return "'" . $connection -> real_escape_string($value) . "'";
        }
    }