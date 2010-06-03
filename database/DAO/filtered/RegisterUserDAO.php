<?php

require_once FORUM_ROOT . '/database/DAO/UserDAO.php';

class RegisterUserDAO extends UserDAO{

  public function __construct(){
    parent::__construct();
    $this->queryName = 'UserQuery';
  }

  protected function usernameFilter($user){
    unset($_SESSION['errors']);

    if(strlen($user)<3){
      $_SESSION['error']['min'] = "Username must be at leat 3 characters long.";
      $this->valid = false;
    }

    if(strlen($user)>20){
      $_SESSION['error']['max'] = "Username too long. Maximum allowed length is 20 characters.";
      $this->valid = false;
    }

    if($this->query->exists('username',$user)){
      $_SESSION['error']['exists'] = "This username alraedy exists. Please select another.";
      $this->valid = false;
    }

    return $user;
  }

  protected function passwordFilter($password){
    if(strlen($password)<5){
      $_SESSION['error']['pass'] = "Password must be at least 5 characters long";
      $this->valid = false;
      return $password;
    }else{
      return md5($password);
    }
  }

  protected function emailFilter($email){
    $pattern = "/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/";
    if(preg_match($pattern,$email)){
      if($this->query->exists('email',$email)){
        $this->valid = false;
        $_SESSION['error']['mail'] = "Another user with this e-mail is already registered!";
      }else{
        $this->valid = true;
      }
    }else{
      $this->valid = false;
      $_SESSION['error']['email'] = "Invalid e-mail address";
    }
    return $email;
  }

}
