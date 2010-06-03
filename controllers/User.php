<?php

require_once 'Base.php';
require_once FORUM_ROOT . 'database/DAO/UserDAO.php';
require_once FORUM_ROOT . 'database/DAO/filtered/RegisterUserDAO.php';
require_once FORUM_ROOT . 'database/DAO/filtered/ProfileUserDAO.php';
require_once FORUM_ROOT . 'database/queries/UserQuery.php';
class User extends Base{
  function __construct(){
    parent::__construct($_GET);
  }
  
  public function index(){
  
  }
  
  public function saveuser(){
    $u = new RegisterUserDAO();
    $u->username = $_POST['username'];
    $u->password = $_POST['password'];
    $u->email = $_POST['email'];
    unset($_SESSION['post']);
    
    //if submitted data is OK
    if($u->validate()){
      $u->active = $this->getActivationCode($u);
      $u->save();
      $_SESSION['message'] = "
        Congrats! You have successfully registered. 
        Please check your e-mail to complete the registration.";
      $this->sendEmail($u,EMAIL_FROM);
      $this->redirect("");
    }else{
      //no need to re-enter username, email etc...
      $_SESSION['post'] = $_POST;
      $this->redirect($_SERVER['HTTP_REFERER'],false);
    }
  }
  
  public function deleteuser(){
  }

  public function login(){
    $q = new UserQuery();
    $res = $q->identify($_POST['username'],md5($_POST['password']));

    if($res&&$res['active']==1){
      $_SESSION['user'] = $res;
    }else{
      
      $message = $this->getView()->setFile('wrongpass.haml')->fetch();
      $_SESSION['message'] = $message;

    }


    $this->redirect($_SERVER['HTTP_REFERER'],false);
  }

  public function logout(){
    session_destroy();
    session_start();
    $_SESSION['message'] = "You have successfully logged out!";
    $this->redirect($_SERVER['HTTP_REFERER'],false);
  }

  public function register(){
   if(isset($_SESSION['post']))
     $this->getView()->assign('post',$_SESSION['post']);

   $this->setTitle('User registration');
   $this->setFile('register.haml');
   $this->render();
  }

  protected function sendEmail($user, $from){
    
    $host = $_SERVER['SERVER_NAME'];
    $code = $user->active;
    $address = "http://" . $host . FORUM_PATH . "?c=user&a=activate&code=$code";

    $this->getView()->assign('address',$address);
    $content = $this->getView()->setFile('mail.haml')->fetch();
    mail($user->email,'User registration', $content, "Content-type: text/html; charset=UTF-8\r\nFrom: $from\r\n");
  
  }

  public function activate(){

    if(isset($_GET['code']) && strlen($_GET['code'])==10){
      $user = new UserDAO();
      $code = $_GET['code'];
      $user->active = $code;
      $user->fetchItemByCode();

      if($user->id!=0){
        $user->active = 1;
        $user->save();
        $_SESSION['message'] = "Congrats! Your account is now active.";
      }else{
        $_SESSION['message'] = "Activation failed! The code doesn't match!";
      }

    }else{
      $_SESSION['message'] = "Activation failed! Try again!";
    
    }
    $this->redirect("");
  }

  public function resetpass(){
    $this->setTitle("Send new password");
    $this->setFile('resetpass.haml');
    $this->render();
  }

  public function sendnewpass(){
    $user = new UserDAO();
    $user->email = $_POST['email'];
    $user->fetchItemByEmail();
    if($user->id==0){
      $_SESSION['message'] = "User not found!";
    }else{
      $pass = $user->password;
      $len = strlen($pass);
      $newpass = substr($pass,$len-5,$len);
      $user->password = md5($newpass);
      $user->save();
      $this->mailPass($user,EMAIL_FROM,$newpass);
      $_SESSION['message'] = "The new password was sent to " . $user->email;

    }
    $this->redirect("");
  }

  public function editprofile(){
    $this->setTitle("Change your password");
    $this->setFile("editprofile.haml");
    $this->render(); 
  }

  public function saveprofile(){
    if(isset($_SESSION['user'])){
      $user = new ProfileUserDAO();
      $user->id = $this->user['id'];
      $user->fetchItem();

      if(md5($_POST['oldpassword'])===$user->password){
        $user->password = md5($_POST['password']);
        if($user->validate()){
          $_SESSION['message'] = "Password changed!";
          $user->save();
          $this->redirect("");
        }else{
          $this->redirect($_SERVER['HTTP_REFERER'],false);
        }
      }else{
        $_SESSION['message'] = "Old password doesn't match! Try again!";
        $this->redirect($_SERVER['HTTP_REFERER'],false);
      }
    }
  
  }

  protected function mailPass($user, $from, $pass){
    mail($user->email,'New password', $pass, "From: $from\n");
  }

  protected function getActivationCode($user){
    return strtoupper(substr($user->password,0,10));
  }
  
}

