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

    if(isset($_GET[4])){
      $page = (int) $_GET[4];
    }else{
      $page = 1;
    }
    $topics = $this->db->topic->findOne();
    $fref = $this->db->createDBRef('forum',$forum);
    $data = $this->db->topic->find(array(
      'forum' => $fref
    ))->skip(($page-1)*10)->limit(10);
    $count = $data->count(false);

    $lastpage = floor($count/10) + (($count%10>0)?1:0);
    $this->getView()->assign('topics',$data);
    $this->getView()->assign('page',$page);
    $this->getView()->assign('lastpage',$lastpage);
    $this->getView()->assign('count',$count);
    $this->getView()->assign('forum',new MongoData($forum));
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
