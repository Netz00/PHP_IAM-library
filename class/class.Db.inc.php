<?php 

//https://www.binpress.com/using-php-with-mysql/

abstract class Db {
 
    protected static $connection;
    /**
     * Connect to the database
     *
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
    private function connect() {    
        // Try and connect to the database
        if(!isset(self::$connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = parse_ini_file('./config.ini');
            self::$connection = new mysqli($config['host'],$config['username'],$config['password'],$config['dbname']);
        }
 
        // If connection was not successful, handle the error
        if(self::$connection === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
            return false;
        }
        return self::$connection;
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
        return $connection -> real_escape_string($value);
    }


    function runBaseQuery($query) {

        $connection = $this -> connect();
        $result = mysqli_query($connection,$query);
        
        if($result === true) {
            return true;
        }
        
        return false;
    }


    function runQuery($query, $param_type, $param_value_array) {

        $connection = $this -> connect();

        $stmt = $connection->prepare($query);
        $this->bindQueryParams($stmt, $param_type, $param_value_array);
        
        if(!$stmt->execute())
        return false;
        
        $result = $stmt->get_result();

        $stmt->close();

        $resultset = array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }

        if(!empty($resultset)) {
            return $resultset;
        }
        return false;
    }


    function bindQueryParams($stmt, $param_type, $param_value_array) {
        $param_value_reference[] = & $param_type;
        for($i=0; $i<count($param_value_array); $i++) {
            $param_value_reference[] = & $param_value_array[$i];
        }
        call_user_func_array(array(
            $stmt,
            'bind_param'
        ), $param_value_reference);
    }

    function insert($query, $param_type, $param_value_array) {
        $connection = $this -> connect();
        $stmt = $connection->prepare($query);
        $this->bindQueryParams($stmt, $param_type, $param_value_array);
        $result = $stmt->execute();
        if($result === false) 
            return false;
        
        return true;
    }

    function update($query, $param_type, $param_value_array) {
        $connection = $this -> connect();
        $stmt = $connection->prepare($query);
        $this->bindQueryParams($stmt, $param_type, $param_value_array);
        $result = $stmt->execute();
        if($result === false) 
            return false;
        
        return true;
    }


}