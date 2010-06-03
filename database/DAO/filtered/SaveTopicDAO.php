<?php

require_once FORUM_ROOT . '/database/DAO/ForumDAO.php';
/**
 * post topic filter!
 */
class SaveTopicDAO extends TopicDAO{

  public function __construct(){
    parent::__construct();
  }
  
  public function contentFilter($content){
    if(trim($content)===''){
      $_SESSION['error']['content'] = "Message is empty!";
      $this->valid = false;
    }
    return $content;
  }

  public function titleFilter($title){
    $len = strlen(trim($title));
    if($len<5){
      $_SESSION['error']['title'] = "The title is too short! (min 5 characters)";
      $this->valid = false;
    }

    if($len>100){
      $_SESSION['error']['title'] = "The title is too long (max 100 characters / current length: $len)!";
      $this->valid = false;
    }
    return strip_tags($title);
  
  }

  public function validate(){
    return $this->valid;
  }

}
