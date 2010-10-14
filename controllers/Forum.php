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
  
  public function newforum(){
    $this->setFile('addforum.haml');
    $this->render();
  }

  public function viewforum(){
    $forum = $this->db->forum->findOne(array(
      'title' => str_replace('_',' ',$_GET[3])
    ));

    $this->getView()->assign('forum',new Data($forum));
    $this->setFile('viewforum.haml');
    $this->render();
  }
  
  
  public function editforum(){
  }


  public function saveforum(){
    $table = $this->db->forum;

    //allow only these fields to be saved

    $table->save(array_allow($_POST,array(
      'title','description'
    )));

    $this->redirect();
  }

  public function deleteforum(){
  }
  
}
