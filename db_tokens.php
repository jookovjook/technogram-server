<?php
/**
 * DB operations functions
 */

class DB_Tokens
{

	private $con;

    // constructor
    function __construct(){
        include_once './db_connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->con = $this->db->connect();

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
        $result = mysqli_query($this->con,"SELECT 1 FROM tokens WHERE `token` = '$token'");
        if ($result && mysqli_num_rows($result) > 0){
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
            $result =mysqli_query($this->con,"SELECT expires FROM tokens WHERE `token` = '$token'");
            $row = mysqli_fetch_array($result);
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
        mysqli_query($this->con,"UPDATE tokens SET expires = $time WHERE token = '$token'");
    }

    public function addToken($user_id){
        $token = $this->generateToken();
        $expires = 0;
        mysqli_query($this->con,"INSERT INTO tokens (user_id, token, expires)
                    VALUES ('$user_id', '$token', '$expires') ");
        $this->setDefaultExpiration($token);
        return $token;
    }

    public function expireToken($token){
        $time = 0;
        mysqli_query($this->con,"UPDATE tokens SET expires = $time WHERE token = '$token'");
    }

    public function ifUserExists($username){
        $result =mysqli_query($this->con,"SELECT 1 FROM users WHERE username = '$username'");
        if ($result && mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ifUserExistsById($user_id){
        $result =mysqli_query($this->con,"SELECT 1 FROM users WHERE user_id = ".$user_id);
        if ($result && mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ifEmailExists($email){
        $result =mysqli_query($this->con,"SELECT 1 FROM users WHERE email = '$email'");
        if ($result && mysqli_num_rows($result) > 0){
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
                $result = mysqli_query($this->con,"SELECT user_id FROM users WHERE username = '$username'");
                $row = mysqli_fetch_array($result);
                return $this->addToken($row['user_id']);
            }
        }
        return false;
    }

    public function ifPassword($username, $password){
        if($this->ifUserExists($username)){
            $result = mysqli_query($this->con,"SELECT password FROM users WHERE username ='$username'");
            $row = mysqli_fetch_array($result);
            if($row['password'] == $password){
                return true;
            }
        }
        return false;
    }

    public function ifPasswordById($user_id, $password){
        if($this->ifUserExistsById($user_id)){
            $result = mysqli_query($this->con,"SELECT password FROM users WHERE user_id = $user_id");
            $row = mysqli_fetch_array($result);
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

    public function getUserIdByToken($token){
        $user_id = -1;
        if($this->ifTokenNotExpired($token)) {
            $result = mysqli_query($this->con,"SELECT user_id FROM tokens WHERE token = '$token'");
            $row = mysqli_fetch_array($result);
            $user_id = $row['user_id'];
        }
        return $user_id;
    }

    public function getUserIdByUsername($username){
        $user_id = -1;
        if($this->ifUserExists($username)) {
            $result = mysqli_query($this->con,"SELECT user_id FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($result);
            $user_id = $row['user_id'];
        }
        return $user_id;
    }

    public function getUserIdByEmail($email){
        $user_id = -1;
        if($this->ifEmailExists($email)) {
            $result = mysqli_query($this->con,"SELECT user_id FROM users WHERE email = '$email'");
            $row = mysqli_fetch_array($result);
            $user_id = $row['user_id'];
        }
        return $user_id;
    }

    public function getUserAvatar($user_id){
        $result = mysqli_query($this->con,"SELECT avatar_large FROM users WHERE user_id = $user_id");
        $row = mysqli_fetch_array($result);
        $avatar_id = $row['avatar_large'];
        $result = mysqli_query($this->con,"SELECT img_link FROM images WHERE image_id = $avatar_id");
        $row = mysqli_fetch_array($result);
        return $row['img_link'];
    }

    public function getUserEmail($user_id){
        $result = mysqli_query($this->con,"SELECT email FROM users WHERE user_id = $user_id");
        $row = mysqli_fetch_array($result);
        return $row['email'];
    }

    public function getUserInfo($user_id){
        $result = mysqli_query($this->con,"SELECT name, surname, about FROM user_info WHERE user_id = $user_id");
        return mysqli_fetch_array($result);
    }

}