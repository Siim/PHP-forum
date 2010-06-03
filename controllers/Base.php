<?php

require_once FORUM_ROOT . '/lib/phphaml/includes/haml/HamlParser.class.php';

/**
 * Base controller class. Each controller should extend this class...
 */

class Base{
  protected $view;
  protected $content; 
  protected $user;
    
  public function __construct($params = array()){
    $this->view = new HamlParser(VIEW_DIR,VIEW_CACHE_DIR);
    $this->content = '';

    $this->setMessage('message');
    $this->setMessage('error');
    $this->user = $_SESSION['user'];
    $this->view->assign('user',$this->user);
    if(isset($params['a'])){
      $action = $params['a'];
      
      if($this->isAction($action)){
        $this->$action();
      }else{
        $this->error404();
      }
    }else{
      $this->index();
    }
  }

  /**
   * index method is default action (get param a=index)
   */
  public function index(){
  
  }
  
  public function error404(){
    header("HTTP/1.0 404 Not Found");
    $this->setFile(ERROR_404_PAGE);
    $this->render();
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
  
  protected function isAction($method){
    $methods = get_class_methods($this);
    return (array_search($method, $methods));
  }
  
  /**
   * Assign $title var for template 
   */
  protected function setTitle($title='Foooo'){
    $this->getView()->assign('title',$title);
  }
  
  protected function redirect($addr='',$baseurl=true){
    
    if($baseurl)$h=url($addr);
    else $h=$addr;
    header("Location: $h");
  }

  /**
   * Assign message to view and delete from session
   */
  protected function setMessage($type){
  
    if(isset($_SESSION[$type])){
      $message = $_SESSION[$type];
      unset($_SESSION[$type]);
    }
    $this->getView()->assign($type,$message);
  }
  
}

