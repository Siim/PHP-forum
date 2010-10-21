<?php

require_once ROOT . '/lib/phphaml/includes/haml/HamlParser.class.php';

/**
 * Base controller class. Each controller should extend this class...
 */

class Controller{
  private $view;
  private $content; 
  private $user;
  public $db;

  public function __construct($db){
    $this->view = new HamlParser(VIEW_DIR,VIEW_CACHE_DIR);
    $this->content = '';
    $this->db = $db;

    if(isset($_GET[2])){
      $action = $_GET[2];

      if($this->isAction($action)){
        $this->$action();
      }else{
        $this->index();
      }
    }else{
      $this->index();
    }
  }

  public function __set($param,$val){
    if(get_class($this)=='Controller'){
      $this->$param = $val;
    }else{
      $this->view->assign($param,$val);
    }
  }

  /**
   * index method is default action (get param a=index)
   */
  public function index(){}

  public function error404(){
    header("HTTP/1.0 404 Not Found");
    $this->setFile(ERROR_404_PAGE);
    $this->render();
  }

  public function getDB(){
    return $this->db;
  }

  /**
   * Redirect to specified url
   * TODO: move redirect to sys/helpers.php instead?
   */
  public function redirect($addr='',$baseurl=true){
    if($baseurl)$h=url($addr);
    else $h=$addr;
    header("Location: $h");
  }

  /**
   * Return HamlParser object
   */
  protected function getView(){
    return $this->view;
  }
  

  /**
   * Set template file  
   */
  protected function setFile($filename=''){
    if($filename!=='' && file_exists(VIEW_DIR . $filename))
      $this->content = $this->getView()->setFile($filename)->fetch();
  }
  
  /**
   * Render template with or without layout
   */
  protected function render($layout=true){
    $v = $this->getView();
    if($layout){
      $v->assign('content',$this->content);
      echo $v->setFile(LAYOUT_FILE)->fetch();    
    }else{
      echo $this->content;
    }
  }
  
  private function isAction($method){
    $methods = get_class_methods($this);
    return (array_search($method, $methods)!==false?true:false);
  }
  
}

