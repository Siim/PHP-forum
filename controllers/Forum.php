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
  
  public function viewtopic(){
    $this->setFile('viewtopic.haml');
    $this->render();
  }
  
  public function newforum(){
    $this->setFile('addforum.haml');
    $this->render();
  }
  
  
  public function editforum(){
  }

  public function edittopic(){
  
  }

  public function saveforum(){
    $table = $this->db->forum;
    $table->save($_POST);
    $this->redirect();
  }

  public function deleteforum(){
  }
  
  public function newtopic(){
    $this->setFile('newtopic.haml');
    $this->render();
  }
  
  public function savetopic(){
  }

  public function deletetopic(){
  }

  
  public function savepost(){

  }

  public function deletepost(){
  }
}
