<?php

class Install extends Controller{
  protected $file='conf/mongo.conf';
  protected $template;


  /**
   * Display installion form
   */
  public function index(){
    if(!file_exists($this->file)){
      if(isset($_SESSION['post'])){
        $this->getView()->assign('post',$_SESSION['post']);
        unset($_SESSION['post']);
      }
      $this->setTitle("Forum installation");
      $this->setFile("install.haml");
      $this->render();
    }
  }

  /**
   * Generate static php class that holds information about database
   * Create tables for database and admin user
   */
  public function save(){
  }

}
