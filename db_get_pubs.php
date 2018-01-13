<?php
/**
 * DB operations functions
 */

class DB_Get_Pubs
{

    private $db;
    private $con;

    // constructor
    function __construct(){
        include_once './db_connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->con = $this->db -> connect();
    }

    // destructor
    function __destruct(){

    }

//    public function reverse($value){
//        if($value){
//            return false;
//        }
//        return true;
//    }
//
//    public function ifTokenNotExpired($token){
//        return $this->reverse($this->ifTokenExpired($token));
//    }
//
//    public function ifTokenExpired($token){
//        if($this->ifTokenExists($token)){
//            date_default_timezone_set('Europe/Moscow');
//            $date = new DateTime();
//            $now = $date->getTimestamp();
//            $result =mysql_query("SELECT expires FROM tokens WHERE `token` = '$token'");
//            $row = mysql_fetch_array($result);
//            if($row != false){
//                $expires = $row['expires'];
//                if($expires <= $now){
//                    return true;
//                }else{
//                    return false;
//                }
//            }else{
//                return true;
//            }
//        }else{
//            return true;
//        }
//    }
//
//    public function ifTokenExists($token){
//        $result =mysql_query("SELECT 1 FROM tokens WHERE `token` = '$token'");
//        if ($result && mysql_num_rows($result) > 0){
//            return true;
//        }else{
//            return false;
//        }
//    }
//
//    public function getUserIdByToken($token){
//        $user_id = -1;
//        if($this->ifTokenNotExpired($token)) {
//            $result = mysql_query("SELECT user_id FROM tokens WHERE token = '$token'");
//            $row = mysql_fetch_array($result);
//            $user_id = $row['user_id'];
//        }
//        return $user_id;
//    }

    public function getUserIdByToken2($token){
        $user_id = -1;
        $result = mysqli_query($this->con,"SELECT user_id, expires FROM tokens WHERE token = '$token'");
        if ($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            date_default_timezone_set('Europe/Moscow');
            $date = new DateTime();
            $now = $date->getTimestamp();
            $expires = $row['expires'];
            if($expires >= $now){
                $user_id = $row['user_id'];
            }
        }
        return $user_id;
    }

    public function getAllPubs(){
        return mysqli_query($this->con,"SELECT * FROM publications ORDER BY publication_id DESC
            LIMIT 10");
    }

    public function getAllPubsAfterId($pub_id){
        return mysqli_query($this->con,"SELECT * FROM publications WHERE publications.publication_id < $pub_id ORDER BY publication_id DESC
            LIMIT 10");
    }

    public function getUserPubs($user_id)
    {
        return mysqli_query($this->con,"SELECT * FROM publications WHERE user_id = $user_id
        ORDER BY publication_id DESC LIMIT 10");
    }

    public function getUserPubsAfterId($user_id, $pub_id)
    {
        return mysqli_query($this->con,"SELECT * FROM publications WHERE user_id = $user_id AND
        publications.publication_id < $pub_id ORDER BY publication_id DESC LIMIT 10");
    }

    public function countComments($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) FROM publications_comments
              WHERE publication_id = $pub_id GROUP BY publication_id");
    }

    public function getUserForPud($user_id){
        return mysqli_query($this->con,"SELECT username, avatar_small FROM users WHERE user_id = $user_id");
    }

    public function getImageById($image_id){
        return mysqli_query($this->con,"SELECT img_link FROM images WHERE image_id = $image_id");
    }

    public function countViews($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) FROM publications_views WHERE
            publication_id = $pub_id GROUP BY publication_id");
    }

    public function countLikes($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) FROM pubs_likes WHERE
            pub_id = $pub_id GROUP BY pub_id");
    }

    public function countX2Likes($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) FROM pubs_x2_likes WHERE
            pub_id = $pub_id GROUP BY pub_id");
    }

    public function ifLikeExists($pub_id, $user_id){
        $result =mysqli_query($this->con,"SELECT 1 FROM pubs_likes WHERE pub_id = ".$pub_id." AND user_id = ".$user_id);
        if ($result && mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ifX2LikeExists($pub_id, $user_id){
        $result =mysqli_query($this->con,"SELECT 1 FROM pubs_x2_likes WHERE pub_id = ".$pub_id." AND user_id = ".$user_id);
        if ($result && mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

}