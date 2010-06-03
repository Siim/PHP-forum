<?php

require_once 'Base.php';
require_once FORUM_ROOT . 'database/queries/BaseQuery.php';
require_once FORUM_ROOT . 'database/DAO/filtered/RegisterUserDAO.php';
/**
 * Install controller: setup database + admin user :)
 */
class Install extends Base{
  protected $file;
  protected $template;

  public function __construct(){
    $this->file = FORUM_ROOT . "/database/DB_Conf.php";
    $this->template = $this->file.".tpl";
    parent::__construct($_GET);

  }

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
    if(!file_exists($this->file)){
      $data =  file_get_contents($this->template);
      $patterns = array(
        "/#USERNAME#/",
        "/#PASSWORD#/",
        "/#HOST#/",
        "/#DBNAME#/"
      );

      $replacements = array(
        $_POST['dbusername'],
        $_POST['dbpassword'],
        $_POST['dbhost'],
        $_POST['dbname']
      );

     $res = preg_replace($patterns,$replacements,$data);
     file_put_contents($this->file,$res);

     try{
       //setup admin user :)
       $user = new RegisterUserDAO();
       $user->username = $_POST['username'];
       $user->password = $_POST['password'];
       $user->admin = 1;
       $user->active = 1;
       if($user->validate()){

         //create tables
         $sql = new BaseQuery();
         $sql->query($this->nodeSQL());
         $sql->query($this->postSQL());
         $sql->query($this->userSQL());
         
         //save admin user
         $user->save();
         $_SESSION['message'] = "Success! Installion is now complete!";
         $this->redirect("");
       }else{
         unlink($this->file);
         $_SESSION['post'] = $_POST;
         $this->redirect($_SERVER['HTTP_REFERER'],false);
         exit();
       }
     }catch(PDOException $e){
       unlink($this->file);
       $_SESSION['post'] = $_POST;
       $this->redirect($_SERVER['HTTP_REFERER'],false);
       $_SESSION['error']['database'] = "Cannot connect to the database!";
       exit();
     }

    }
  }

  /**
   * MySQL tables :)
   */
  private function nodeSQL(){
    return ("
      CREATE TABLE node(
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(100) NOT NULL,
        `description`  varchar(100) NOT NULL,
        `parent` int(11) NOT NULL,
        `date` datetime NOT NULL,
        `username` int(10) NOT NULL,
        `lastpost` datetime NOT NULL,
        `count` int(10) NOT NULL,
        `csum`  varchar(32) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1
    ");
  }

  private function postSQL(){
    return ("
      CREATE TABLE `post` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `userid` int(10) DEFAULT NULL,
        `title` varchar(100) DEFAULT NULL,
        `content` text,
        `date` datetime DEFAULT NULL,
        `ip` varchar(100) DEFAULT NULL,
        `parent` int(10) DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1
    ");
  }

  private function userSQL(){
    return ("
      CREATE TABLE `user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` tinytext NOT NULL,
        `password` tinytext NOT NULL,
        `email` tinytext NOT NULL,
        `posts` int(11) NOT NULL,
        `registered` datetime NOT NULL,
        `last` datetime NOT NULL,
        `admin` tinyint(1) NOT NULL,
        `active` tinytext NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1
    ");
  
  }

}
