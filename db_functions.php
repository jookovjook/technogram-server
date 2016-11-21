<?php
/**
 * DB operations functions
 */

class DB_Functions
{

    private $db;
    private $get_pubs ="SELECT publications.publication_id, publications.user_id, users.username,
  title, text, V.views, S.stars, comments, images.img_link, datetime, I.img_link AS small_avatar
FROM publications
  JOIN (SELECT publications.publication_id, COUNT(publications_comments.publication_id) AS comments FROM publications
    LEFT JOIN publications_comments ON publications_comments.publication_id = publications.publication_id GROUP BY 1) AS T
    ON (T.publication_id = publications.publication_id)
  JOIN users ON(publications.user_id = users.user_id)
  JOIN images ON(image_id = image)
  JOIN images AS I ON(users.avatar_small = I.image_id)
  JOIN (SELECT publication_id, COUNT(*) AS views FROM publications_views GROUP BY publication_id) AS V
    ON (V.publication_id = publications.publication_id)
  JOIN (SELECT publication_id, COUNT(*) AS stars FROM publications_stars GROUP BY publication_id) AS S
    ON (S.publication_id = publications.publication_id)";
    private $target_path = "uploads/";
    private $images_directory = "image_resources/";

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

    public function getUser($username){
        require_once 'config.php';
        $result = mysql_query("SELECT user_id, password FROM users WHERE username = '$username'");
        return $result;
    }

    public function getUserInfo($user_id)
    {
        require_once 'config.php';
        return mysql_query("SELECT users.username, user_info.name, user_info.surname, img_link FROM user_info
                  JOIN users ON(users.user_id = user_info.user_id)
                  JOIN images ON(users.avatar_small = image_id)
                  WHERE (users.user_id = user_info.user_id) AND (user_info.user_id = '$user_id')");
    }

    public function getUserPublications($user_id)
    {
        require_once 'config.php';
        return mysql_query($this->get_pubs." WHERE publications.user_id = ".$user_id);
    }

    public function getAllPublications()
    {
        require_once 'config.php';
        return mysql_query($this->get_pubs);
    }

    public function getPublication($publication_id)
    {
        require_once 'config.php';
        return mysql_query($this->get_pubs." WHERE publications.publication_id = ".$publication_id);
    }

    public function getComments($publication_id){
        require_once 'config.php';
        return mysql_query("SELECT users.username, images.img_link, publications_comments.* FROM users
JOIN publications_comments ON(users.user_id = publications_comments.user_id)
JOIN images ON(avatar_small = image_id) AND publication_id = ".$publication_id);
    }

    public function postComment($publication_id, $user_id, $comment){
        require_once 'config.php';
        if(mysql_query("INSERT INTO publications_comments (publication_id, user_id, comment) VALUES (".$publication_id.", ".$user_id.", '".$comment."')")) {
            $id = mysql_insert_id();
            return mysql_query("SELECT users.username, images.img_link, publications_comments.* FROM users
          JOIN publications_comments ON(users.user_id = publications_comments.user_id)
          JOIN images ON(avatar_small = image_id) AND comment_id = " . $id);
        }else{
            return false;
        }
        //return mysql_query("INSERT INTO comments (publication_id, user_id, comment) VALUES (1, 1, 'hello'");
    }

    public function getPublicationImages($publication_id){
        require_once 'config.php';
        return mysql_query("SELECT images.img_link FROM publication_images JOIN images
              ON (images.image_id = publication_images.image_id) WHERE publication_id = ".$publication_id);
    }

    public function insertTempImage($image_link){
        require_once 'config.php';
        $id = "-1";
        $result = mysql_query("INSERT INTO temp_images (image_link) VALUES ('".$image_link."')");
        if($result) $id = mysql_insert_id();
        return $id;
    }

    public function getTempImageLink($_id){
        require_once 'config.php';
        return mysql_query("SELECT image_link FROM temp_images WHERE temp_image_id = ".$_id);
    }

    public function transferImage($temp_image_id, $filename){
        require_once 'config.php';
        $response = array();
        $respone['error']=true;
        if(rename($this->target_path.$filename, $this->images_directory.$filename)){
            if(mysql_query("INSERT INTO images (img_link) VALUES ('".$filename."')")){
                $response['image_id'] = mysql_insert_id();
                $response['error'] = false;
                mysql_query("DELETE FROM temp_images WHERE temp_image_id = ".$temp_image_id);
            }else{
                $response['message'] = 'Error pasting image into DB';
            }
        }else{
            $response['message'] = 'Error transfering image into new directory';
        }
        return $response;
    }

    public function makePublication($user_id, $title, $description, $image, $branch){
        require_once 'config.php';
        $response = array();
        $response['error'] = true;
        $response['message'] = "error creacting new publication";
        if(mysql_query("INSERT INTO publications (user_id, title, text, image, datetime, branch)
          VALUES (".$user_id.", '".$title."','".$description."',".$image.", NOW(), ".$branch.")")){
            $response['error'] = false;
            $response['message'] = "success";
            $insert_id =  mysql_insert_id();
            $response['publication_id'] = $insert_id;
        }
        return $response;
    }

    public function getUserIdByToken($token){
        require_once 'config.php';
        $result = mysql_query("SELECT user_id FROM tokens WHERE token = '".$token."'");
        $row = mysql_fetch_array($result);
        $id = $row['user_id'];
        if(is_null($id)) $id = -1;
        return $id;
    }

    public function ifUserOwnsPublication($user_id, $publication_id){
        require_once 'config.php';
        $result = mysql_query("SELECT user_id, publication_id FROM publications
                    WHERE user_id = ".$user_id." AND publication_id = ".$publication_id);
        $row = mysql_fetch_array($result);
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
        if(mysql_query("INSERT INTO publication_images (publication_id, image_id)
                          VALUES (".$publication_id.", ".$image_id.")")){
            $response['error'] = false;
        }
        return $response;
    }

    public function getUserSmallAvatar($user_id){
        require_once 'config.php';
        $result = mysql_query("SELECT img_link FROM users JOIN images
                      ON (images.image_id = users.avatar_small) AND user_id = ".$user_id);
        return $result;
    }

    public function addPublicationView($publication_id, $user_id){
        require_once 'config.php';
        $result = mysql_query("SELECT view_id FROM publications_views
              WHERE publication_id = '$publication_id' AND user_id = '$user_id'");
        if(mysql_fetch_row($result)!== false){
            return false;
        }else{
            mysql_query("INSERT INTO publications_views (publication_id, user_id) VALUES (".$publication_id.", ".$user_id.")");
            return true;
        }
    }

    public function addPublicationStar($publication_id, $user_id){
        require_once 'config.php';
        $result = mysql_query("SELECT star_id FROM publications_stars
              WHERE publication_id = '$publication_id' AND user_id = '$user_id'");
        if(mysql_fetch_row($result)!== false){
            return false;
        }else{
            mysql_query("INSERT INTO publications_stars (publication_id, user_id) VALUES (".$publication_id.", ".$user_id.")");
            return true;
        }
    }

    public function addAdvToSoft($publication_id, $license, $stage){
        require_once 'config.php';
        $result['error'] = true;
        if(mysql_query("INSERT INTO pub_soft_adv (publication_id, license, stage)
              VALUES ('$publication_id', '$license', '$stage') ")){
            $result['error'] = false;
        }
        return $result;
    }

    public function addLinkToSoft($publication_id, $link, $type){
        require_once 'config.php';
        $result['error'] = true;
        if(mysql_query("INSERT INTO pub_soft_links (publication_id, link, link_type)
                  VALUES  ('$publication_id', '$link', '$type')")){
            $result['error'] = false;
        }
        return $result;
    }

    public function getSoftAdv($publication_id){
        require_once 'config.php';
        $response['exists'] = false;
        $result = mysql_query("SELECT * FROM pub_soft_adv WHERE publication_id = ".$publication_id);
        $row = mysql_fetch_array($result);
        if($row != false){
            $response['exists'] = true;
            $response['license'] = $row['license'];
            $response['stage'] = $row['stage'];
        }
        return $response;
    }

}

?>