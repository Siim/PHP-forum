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
      $_SESSION['error']['content'] = "Postituse sisu on tühi!";
      $this->valid = false;
    }
    return $content;
  }

  public function titleFilter($title){
    $len = strlen(trim($title));
    if($len<5){
      $_SESSION['error']['title'] = "Pealkiri liiga lühike (min 5 tähemärki)!";
      $this->valid = false;
    }

    if($len>100){
      $_SESSION['error']['title'] = "Pealkiri liiga pikk (max 100 tähemärki, teie sisestasite: $len tähemärki)!";
      $this->valid = false;
    }
    return strip_tags($title);
  
  }

  public function validate(){
    return $this->valid;
  }

}
