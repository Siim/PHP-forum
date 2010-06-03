<?php

require_once FORUM_ROOT . '/database/DAO/UserDAO.php';

class ProfileUserDAO extends UserDAO{

  public function __construct(){
    parent::__construct();
    $this->queryName = 'UserQuery';
  }

  protected function passwordFilter($password){
    if(strlen($password)<5){
      $_SESSION['error']['pass'] = "Parool peab olema vähemal 5 tähemärki pikk!";
      $this->valid = false;
    }
    return $password;
  }
}
