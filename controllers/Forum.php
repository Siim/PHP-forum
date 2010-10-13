<?php

class Forum extends Controller{
  protected $user;
  
  public function index(){
    $res = $this->db->forum->find();
    $this->getView()->assign('teemad',$res);
    $this->setTitle('Forum');
    $this->setFile('list.haml');
    $this->render();
  }
  
  public function viewforum(){
    $res = $this->db->forum->findOne(array(
      'title' => str_replace('_',' ',$_GET[3])
    ));
    $this->getView()->assign('forum',new Data($res));
    $this->setFile('viewforum.haml');
    $this->render();
  }
  
  
  public function editforum(){
  }


  public function saveforum(){
    $table = $this->db->forum;

    $table->save(array_filter($_POST, function($el){
      $ignore = array('Save','f');
      if(in_array(key($el,$ignore)))return false;
    }));

    $this->redirect();
  }

  public function deleteforum(){
  }
  
}
