<?php

class Forum extends Controller{
  protected $user;
  
  public function index(){

    $d = new ForumDAO();
    $res = $d->fetchAll(0,0,50,'title ASC');
    $this->getView()->assign('teemad',$res);
    $this->setTitle('Forum');
    $this->setFile('list.haml');
    $this->render();
  }
  
  public function viewforum(){
    $this->setFile('viewforum.haml');
    $this->render();
  }
  
  public function viewtopic(){
    $this->setFile('viewtopic.haml');
    $this->render();
  }
  
  public function newforum(){
  }
  
  
  public function editforum(){
  }

  public function edittopic(){
  
  }

  public function saveforum(){
  
  }

  public function deleteforum(){
  }
  
  public function newtopic(){
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
