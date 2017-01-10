<?php
/**
 * DB operations functions
 */

class DB_Tokens
{
    // constructor
    function __construct(){
        include_once './db_connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }

    // destructor
    function __destruct(){

    }

    public function setDefaultExpiration($token){
        $default_sec = 30*24*60*60; //30 days
        $this->extendToken($token, $default_sec);
    }

    public function generateToken(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 32;
        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) $token .= $characters[mt_rand(0, strlen($characters))];
        }while($this->ifTokenExists($token));

        return $token;
    }

    public function ifTokenExists($token){
        $result =mysql_query("SELECT 1 FROM tokens WHERE `token` = '$token'");
        if ($result && mysql_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ifTokenExpired($token){
        if($this->ifTokenExists($token)){
            date_default_timezone_set('Europe/Moscow');
            $date = new DateTime();
            $now = $date->getTimestamp();
            $result =mysql_query("SELECT expires FROM tokens WHERE `token` = '$token'");
            $row = mysql_fetch_array($result);
            if($row != false){
                $expires = $row['expires'];
                if($expires <= $now){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }else{
            return true;
        }
    }

    public function ifTokenNotExpired($token){
        return $this->reverse($this->ifTokenExpired($token));
    }

    public function extendToken($token, $sec){
        date_default_timezone_set('Europe/Moscow');
        $date = new DateTime();
        $now = $date->getTimestamp();
        $time = $now + $sec;
        mysql_query("UPDATE tokens SET expires = $time WHERE token = '$token'");
    }

    public function addToken($user_id){
        $token = $this->generateToken();
        $expires = 0;
        mysql_query("INSERT INTO tokens (user_id, token, expires)
                    VALUES ('$user_id', '$token', '$expires') ");
        $this->setDefaultExpiration($token);
        return $token;
    }

    public function expireToken($token){
        $time = 0;
        mysql_query("UPDATE tokens SET expires = $time WHERE token = '$token'");
    }

    public function ifUserExists($username){
        $result =mysql_query("SELECT 1 FROM users WHERE username = '$username'");
        if ($result && mysql_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ifUserNotExists($username){
        return $this->reverse($this->ifUserExists($username));
    }

    public function authentificate($username, $password){
        if($this->ifUserExists($username)){
            if($this->ifPassword($username, $password)){
                $result = mysql_query("SELECT user_id FROM users WHERE username = '$username'");
                $row = mysql_fetch_array($result);
                return $this->addToken($row['user_id']);
            }
        }
        return false;
    }

    public function ifPassword($username, $password){
        if($this->ifUserExists($username)){
            $result = mysql_query("SELECT password FROM users WHERE username ='$username'");
            $row = mysql_fetch_array($result);
            if($row['password'] == $password){
                return true;
            }
        }
        return false;
    }

    public function ifNotPassword($username, $password){
        return $this->reverse($this->ifPassword($username, $password));
    }

    public function reverse($value){
        if($value){
            return false;
        }
        return true;
    }

}

?>