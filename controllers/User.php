<?php

class User extends Controller{
  
  public function index(){
  
  }
  
  public function saveuser(){
  }
  
  public function deleteuser(){
  }

  public function login(){
  }

  public function logout(){
  }

  public function register(){
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
  }

  public function editprofile(){
    $this->setTitle("Change your password");
    $this->setFile("editprofile.haml");
    $this->render(); 
  }

  public function saveprofile(){
    if(isset($_SESSION['user'])){
    }
  }

  protected function mailPass($user, $from, $pass){
    mail($user->email,'New password', $pass, "From: $from\n");
  }

  protected function getActivationCode($user){
    return strtoupper(substr($user->password,0,10));
  }
  
}

