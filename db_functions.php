<?php
/**
 * DB operations functions
 */

class DB_Functions
{

    private $db;
    private $con;
    private $get_pubs ="SELECT publications.publication_id, publications.user_id, users.username,
  title, text, V.views, S.likes, SD.x2likes, comments, images.img_link, datetime, I.img_link AS small_avatar
FROM publications
  JOIN (SELECT publications.publication_id, COUNT(publications_comments.publication_id) AS comments FROM publications
    LEFT JOIN publications_comments ON publications_comments.publication_id = publications.publication_id GROUP BY 1) AS T
    ON (T.publication_id = publications.publication_id)
  JOIN users ON(publications.user_id = users.user_id)
  JOIN images ON(image_id = image)
  JOIN images AS I ON(users.avatar_small = I.image_id)
  JOIN (SELECT publication_id, COUNT(*) AS views FROM publications_views GROUP BY publication_id) AS V
    ON (V.publication_id = publications.publication_id)
  JOIN (SELECT pub_id, COUNT(*) AS likes FROM pubs_likes GROUP BY pub_id) AS S
    ON (S.pub_id = publications.publication_id)
  JOIN (SELECT pub_id, COUNT(*) AS x2likes FROM pubs_x2_likes GROUP BY pub_id) AS SD
    ON (SD.pub_id = publications.publication_id)";
    private $order = " ORDER BY datetime DESC, publication_id DESC";
    private $strict = " LIMIT 10";
    private $id_strict = " WHERE publications.publication_id < ";
    private $id_strict_user = " AND publications.publication_id < ";
    private $target_path = "uploads/";
    private $images_directory = "image_resources/";

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

    public function getUser($username){
        require_once 'config.php';
        $result = mysqli_query($this->con, "SELECT user_id, password FROM users WHERE username = '$username'");
        return $result;
    }

    public function getUserInfo($user_id)
    {
        require_once 'config.php';
        return mysqli_query($this->con,"SELECT users.username, user_info.name, user_info.surname, img_link FROM user_info
                  JOIN users ON(users.user_id = user_info.user_id)
                  JOIN images ON(users.avatar_small = image_id)
                  WHERE (users.user_id = user_info.user_id) AND (user_info.user_id = '$user_id')");
    }

    public function getUsernameAndAvaId($user_id)
    {
        return mysqli_query($this->con,"SELECT username, avatar_small FROM users WHERE user_id = $user_id");
    }

    public function getNameSurnameAbout($user_id)
    {
        return mysqli_query($this->con,"SELECT name, surname, about FROM user_info WHERE user_id = $user_id");
    }

    public function getUserPubsIds($user_id){
        return $answer = mysqli_query($this->con,"SELECT publication_id FROM publications WHERE user_id = '$user_id'");
    }

    public function getStats($user_id){
        $answer = $this->getUserPubsIds($user_id);
        $views = 0;
        $likes = 0;
        $x2likes = 0;
        $subs = 0;
        while($r = mysqli_fetch_assoc($answer) ){
           $pub_id =  $r['publication_id'];
           $response = mysqli_query($this->con,"SELECT COUNT(*) FROM publications_views WHERE publication_id = $pub_id GROUP BY publication_id");
           if(mysqli_num_rows($response) == 1) $views += (mysqli_fetch_row($response))[0];
           $response = mysqli_query($this->con,"SELECT COUNT(*) FROM pubs_likes WHERE pub_id = $pub_id GROUP BY pub_id");
           if(mysqli_num_rows($response) == 1) $likes += (mysqli_fetch_row($response))[0];
           $response = mysqli_query($this->con,"SELECT COUNT(*) FROM pubs_x2_likes WHERE pub_id = $pub_id GROUP BY pub_id");
           if(mysqli_num_rows($response) == 1) $x2likes += (mysqli_fetch_row($response))[0];
        }
        $answer = array();
        $answer['views'] = $views;
        $answer['likes'] = $likes;
        $answer['x2likes'] = $x2likes;
        $answer['subs'] = $subs;
        return $answer;

    }



    public function getOwnInfo($user_id){
        require_once 'config.php';
        return mysqli_query($this->con,"SELECT users.email, users.username, user_info.name, user_info.surname, img_link, user_info.about FROM user_info
                  JOIN users ON(users.user_id = user_info.user_id)
                  JOIN images ON(users.avatar_small = image_id)
                  WHERE (users.user_id = user_info.user_id) AND (user_info.user_id = '$user_id')");
    }

    public function getUserAddInfo($user_id){
        return mysqli_query($this->con,"SELECT users.username, user_info.name, user_info.surname, user_info.about, img_link FROM user_info
                  JOIN users ON(users.user_id = user_info.user_id)
                  JOIN images ON(users.avatar_small = image_id)
                  WHERE (users.user_id = user_info.user_id) AND (user_info.user_id = '$user_id')");
    }

    public function getUserPublications($user_id)
    {
        require_once 'config.php';
        return mysqli_query($this->con,$this->get_pubs." WHERE publications.user_id = ".$user_id.$this->order.$this->strict);
    }

    public function getUserPubsId($user_id, $last_id){
        return mysqli_query($this->con,$this->get_pubs." WHERE publications.user_id = ".$user_id.$this->id_strict_user.$last_id.$this->order.$this->strict);
    }

    public function getAllPublications()
    {
        require_once 'config.php';
        return mysqli_query($this->con,$this->get_pubs.$this->order.$this->strict);
    }

    public function getAllPubsId($pub_id){
        return mysqli_query($this->con,$this->get_pubs.$this->id_strict.$pub_id.$this->order.$this->strict);
    }

    public function getDateOfPub($pub_id){
        $result = mysqli_query($this->con,"SELECT datetime FROM publications WHERE publication_id = $pub_id");
        $row = mysqli_fetch_array($result);
        $datetime = $row['datetime'];
        return $datetime;
    }

    public function getPublication($publication_id)
    {
        require_once 'config.php';
        return mysqli_query($this->con,$this->get_pubs." WHERE publications.publication_id = ".$publication_id);
    }

    public function getComments($publication_id){
        require_once 'config.php';
        return mysqli_query($this->con,"SELECT users.username, images.img_link, publications_comments.* FROM users
JOIN publications_comments ON (users.user_id = publications_comments.user_id)
JOIN images ON(avatar_small = image_id) AND publication_id = ".$publication_id." ORDER BY comment_id ASC");
    }

    public function postComment($publication_id, $user_id, $comment){
        require_once 'config.php';
        if(mysqli_query($this->con,"INSERT INTO publications_comments (publication_id, user_id, comment) VALUES (".$publication_id.", ".$user_id.", '".$comment."')")) {
            $id = mysqli_insert_id($this->con);
            return mysqli_query($this->con,"SELECT users.username, images.img_link, publications_comments.* FROM users
          JOIN publications_comments ON(users.user_id = publications_comments.user_id)
          JOIN images ON(avatar_small = image_id) AND comment_id = " . $id);
        }else{
            return false;
        }
        //return mysql_query("INSERT INTO comments (publication_id, user_id, comment) VALUES (1, 1, 'hello'");
    }

    public function getPublicationImages($publication_id){
        require_once 'config.php';
        return mysqli_query($this->con,"SELECT images.img_link FROM publication_images JOIN images
              ON (images.image_id = publication_images.image_id) WHERE publication_id = ".$publication_id);
    }

    public function insertTempImage($image_link){
        require_once 'config.php';
        $id = "-1";
        $result = mysqli_query($this->con,"INSERT INTO temp_images (image_link) VALUES ('".$image_link."')");
        if($result) $id = mysqli_insert_id($this->con);
        return $id;
    }

    public function getTempImageLink($_id){
        require_once 'config.php';
        return mysqli_query($this->con,"SELECT image_link FROM temp_images WHERE temp_image_id = ".$_id);
    }

    public function transferImage($temp_image_id, $filename){
        require_once 'config.php';
        $response = array();
        $respone['error']=true;
        if(rename($this->target_path.$filename, $this->images_directory.$filename)){
            if(mysqli_query($this->con,"INSERT INTO images (img_link) VALUES ('".$filename."')")){
                $response['image_id'] = mysqli_insert_id($this->con);
                $response['error'] = false;
                mysqli_query($this->con,"DELETE FROM temp_images WHERE temp_image_id = ".$temp_image_id);
            }else{
                $response['message'] = 'Error pasting image into DB';
            }
        }else{
            $response['message'] = 'Error transfering image into new directory';
        }
        return $response;
    }

    public function makePublication($user_id, $title, $description, $image){
        require_once 'config.php';
        $response = array();
        $response['error'] = true;
        $response['message'] = "error creacting new publication";
        if(mysqli_query($this->con,"INSERT INTO publications (user_id, title, text, image, datetime)
          VALUES (".$user_id.", '".$title."','".$description."',".$image.", NOW() )")){
            $response['error'] = false;
            $response['message'] = "success";
            $insert_id =  mysqli_insert_id($this->con);
            $response['publication_id'] = $insert_id;
            //if($this->addPublicationView($insert_id, -1));
            //$this->addPublicationX2Like($insert_id, -1);
            //$this->addPublicationLike($insert_id, -1);
        }
        return $response;
    }

    public function getUserIdByToken($token){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT user_id FROM tokens WHERE token = '".$token."'");
        $row = mysqli_fetch_array($result);
        $id = $row['user_id'];
        if(is_null($id)) $id = -1;
        return $id;
    }

    public function ifUserOwnsPublication($user_id, $publication_id){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT user_id, publication_id FROM publications
                    WHERE user_id = ".$user_id." AND publication_id = ".$publication_id);
        $row = mysqli_fetch_array($result);
        $user_id = $row['user_id'];
        if(is_null($user_id)){
            return false;
        }else{
            return true;
        }
    }

    public function addImageToPublication($publication_id, $image_id){
        require_once 'config.php';
        $response = array();
        $response['error'] = true;
        $response['message'] = "Error pasting image into DB";
        if(mysqli_query($this->con,"INSERT INTO publication_images (publication_id, image_id)
                          VALUES (".$publication_id.", ".$image_id.")")){
            $response['error'] = false;
        }
        return $response;
    }

    public function getUserSmallAvatar($user_id){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT img_link FROM users JOIN images
                      ON (images.image_id = users.avatar_small) AND user_id = ".$user_id);
        return $result;
    }

    public function addPublicationView($publication_id, $user_id){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT view_id FROM publications_views
              WHERE publication_id = '$publication_id' AND user_id = '$user_id'");
        if(mysqli_fetch_row($result)!== false){
            return false;
        }else{
            mysqli_query($this->con,"INSERT INTO publications_views (publication_id, user_id) VALUES (".$publication_id.", ".$user_id.")");
            return true;
        }
    }

    public function addPublicationLike($pub_id, $user_id){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT like_id FROM pubs_likes
              WHERE pub_id = '$pub_id' AND user_id = '$user_id'");
        if(mysqli_fetch_row($result)!== false){
            return false;
        }else{
            mysqli_query($this->con,"INSERT INTO pubs_likes (pub_id, user_id) VALUES (".$pub_id.", ".$user_id.")");
            return true;
        }
    }

    public function addPublicationX2Like($pub_id, $user_id){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT like_id FROM pubs_x2_likes
              WHERE pub_id = '$pub_id' AND user_id = '$user_id'");
        if(mysqli_fetch_row($result)!== false){
            return false;
        }else{
            mysqli_query($this->con,"INSERT INTO pubs_x2_likes (pub_id, user_id) VALUES (".$pub_id.", ".$user_id.")");
            return true;
        }
    }

    public function addAdvToSoft($publication_id, $license, $stage){
        require_once 'config.php';
        $result['error'] = true;
        if(mysqli_query($this->con,"INSERT INTO pub_soft_adv (publication_id, license, stage)
              VALUES ('$publication_id', '$license', '$stage') ")){
            $result['error'] = false;
        }
        return $result;
    }

    public function addLinkToSoft($publication_id, $link, $type){
        require_once 'config.php';
        $result['error'] = true;
        if(mysqli_query($this->con,"INSERT INTO pub_soft_links (publication_id, link, link_type)
                  VALUES  ('$publication_id', '$link', '$type')")){
            $result['error'] = false;
        }
        return $result;
    }

    public function getSoftAdv($publication_id){
        require_once 'config.php';
        $response['exists'] = false;
        $result = mysqli_query($this->con,"SELECT * FROM pub_soft_adv WHERE publication_id = ".$publication_id);
        $row = mysqli_fetch_array($result);
        if($row != false){
            $response['exists'] = true;
            $response['license'] = $row['license'];
            $response['stage'] = $row['stage'];
        }
        return $response;
    }

    public function register($username, $password, $email){
        require_once 'config.php';
        $result['error'] = true;
        $result['ins_usr'] = false;
        if(mysqli_query($this->con,"INSERT INTO users (username, password, email)
              VALUES ('$username', '$password', '$email') ")){
            $result['ins_usr'] = true;
            $id = mysqli_insert_id($this->con);
            if(mysqli_query($this->con,"INSERT INTO user_info (user_id) VALUES ('$id')")){
                $result['error'] = false;
                $result['user_id'] = $id;
            }
        }
        return $result;
    }

    public function updateUserInfo($user_id, $name, $surname, $about){
        mysqli_query($this->con,"UPDATE user_info SET name='$name', surname='$surname', $about='$about' WHERE user_id=".$user_id);
    }

    public function updateName($user_id, $name){
        mysqli_query($this->con,"UPDATE user_info SET name='$name' WHERE user_id=".$user_id);
    }

    public function updateSurname($user_id, $surname){
        mysqli_query($this->con,"UPDATE user_info SET surname = '$surname' WHERE user_id=".$user_id);
    }

    public function updateAbout($user_id, $about){
        mysqli_query($this->con,"UPDATE user_info SET about = '$about'  WHERE user_id=".$user_id);
    }

    public function updateUsername($user_id, $username){
        if(mysqli_query($this->con,"UPDATE users SET username='$username' WHERE user_id = ".$user_id)){
            return true;
        }
        return false;
    }

    public function updatePassword($user_id, $curr_pass, $password){
        include_once 'db_tokens.php';
        $dbt = new DB_Tokens();
        if($dbt->ifPasswordById($user_id, $curr_pass)){
            mysqli_query($this->con,"UPDATE users SET password = '$password'  WHERE user_id = ".$user_id);
            return true;
        }else{
            return false;
        }
    }

    public function updateEmail($user_id, $email){
        if(mysqli_query($this->con,"UPDATE users SET email = '$email' WHERE user_id = ".$user_id)){
            return true;
        }
        return false;
    }

    public function getImageByLink($img_link){
        require_once 'config.php';
        $result = mysqli_query($this->con,"SELECT image_id FROM images WHERE img_link = '$img_link'");
        $row = mysqli_fetch_array($result);
        $image_id = $row['image_id'];
        if(is_null($image_id)) $image_id = -1;
        return $image_id;
    }

    public function setProfileImage($user_id, $image_id){
        mysqli_query($this->con,"UPDATE users SET avatar_small = '$image_id', avatar_large = '$image_id' WHERE user_id=".$user_id);
    }

    public function ifPubExists($pub_id){
        $result =mysqli_query($this->con,"SELECT 1 FROM publications WHERE publication_id = ".$pub_id);
        if ($result && mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
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

    public function deleteLike($pub_id, $user_id){
        if(mysqli_query($this->con,"DELETE FROM pubs_likes WHERE pub_id = $pub_id AND user_id = $user_id"));
    }

    public function deleteX2Like($pub_id, $user_id){
        if(mysqli_query($this->con,"DELETE FROM pubs_x2_likes WHERE pub_id = $pub_id AND user_id = $user_id"));
    }

    //OPTIMIZATION

    public function getAllPubsOptimized(){
        return mysqli_query($this->con,"SELECT * FROM publications ORDER BY publication_id DESC
            LIMIT 10");
    }

    public function getAllPubsAfterIdOptimized($pub_id){
        return mysqli_query($this->con,"SELECT * FROM publications ORDER BY datetime DESC, publication_id DESC
            LIMIT 10 WHERE publications.publication_id < ".$pub_id);
    }

    public function countComments($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) as comments FROM publications_comments
              WHERE publication_id = $pub_id GROUP BY publication_id");
    }

    public function getUserForPud($user_id){
        return mysqli_query($this->con,"SELECT username, avatar_small FROM users WHERE user_id = $user_id");
    }

    public function getImageById($image_id){
        return mysqli_query($this->con,"SELECT img_link FROM images WHERE image_id = $image_id");
    }

    public function countViews($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) as views FROM publications_views WHERE
            publication_id = $pub_id GROUP BY publication_id");
    }

    public function countLikes($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) as likes FROM pubs_likes WHERE
            pub_id = $pub_id GROUP BY pub_id");
    }

    public function countX2Likes($pub_id){
        return mysqli_query($this->con,"SELECT COUNT(*) as x2likes FROM pubs_x2_likes WHERE
            pub_id = $pub_id GROUP BY pub_id");
    }

    //FUNCTIONS FOR STRESS TESTS

    public function registerTest($username, $password, $email, $name, $surname, $about, $img_link){
        require_once 'config.php';
        $result['error'] = true;
        $result['ins_usr'] = false;
        mysqli_query($this->con,"INSERT INTO images (img_link) VALUES ('$img_link')");
        $image_id = mysqli_insert_id($this->con);
        if(mysqli_query($this->con,"INSERT INTO users (username, password, email, avatar_small, avatar_large)
              VALUES ('$username', '$password', '$email', $image_id, $image_id) ")){
            $result['ins_usr'] = true;
            $id = mysqli_insert_id($this->con);
            if(mysqli_query($this->con,"INSERT INTO user_info (user_id, name, surname, about) VALUES ('$id', '$name', '$surname', '$about')")){
                $result['error'] = false;
                $result['user_id'] = $id;
            }
        }
        return $result;
    }

    public function makePublicationTest($user_id, $title, $description, $img_link){
        //require_once 'config.php';
        $response = array();
        $response['error'] = true;
        $response['message'] = "error creacting new publication";
        mysqli_query($this->con,"INSERT INTO images (img_link) VALUES ('$img_link')");
        $image = mysqli_insert_id($this->con);
        if(mysqli_query($this->con,"INSERT INTO publications (user_id, title, text, image, datetime)
          VALUES (".$user_id.", '".$title."','".$description."',".$image.", NOW() )")){
            $response['error'] = false;
            $response['message'] = "success";
            $insert_id =  mysqli_insert_id($this->con);
            $response['pub_id'] = $insert_id;
            //if($this->addPublicationView($insert_id, -1));
            //$this->addPublicationX2Like($insert_id, -1);
            //$this->addPublicationLike($insert_id, -1);
        }
        return $response;
    }

    public function addImageToPublicationTest($publication_id, $image_link){
        //require_once 'config.php';
        $response = array();
        $response['error'] = true;
        $response['message'] = "Error pasting image into DB";
        mysqli_query($this->con,"INSERT INTO images (img_link) VALUES ('$image_link')");
        $image_id = mysqli_insert_id($this->con);
        if(mysqli_query($this->con,"INSERT INTO publication_images (publication_id, image_id)
                          VALUES (".$publication_id.", ".$image_id.")")){
            $response['error'] = false;
        }
        return $response;
    }
}