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
      'uri' => urldecode($_GET[3])
    ));

    if(isset($_GET[4])&&$_GET[4]>0){
      $page = (int) $_GET[4];
    }else{
      $page = 1;
    }

    $fref = $this->db->createDBRef('forum',$forum);
    $data = $this->db->topic->find(array(
      'forum' => $fref
    ))->skip(($page-1)*10)->limit(10);
    $count = $data->count(false);

    $lastpage = floor($count/10) + (($count%10>0)?1:0);
    $this->getView()->assign('topics',$data);
    $this->getView()->assign('uri_len',3); //for pagination
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

    $post = $_POST;
    $post['uri'] = str_replace(' ','.',$post['title']);
    
    // check if the title exists
    // if it exists, then dont save the duplicate :)
    // yes it's arguable
    if(!$table->findOne(array('uri' => $post['uri']))){
      $table->save(array_allow($post,array(
        'title','description','uri'
      )));
    }

    $this->redirect();
  }

  public function deleteforum(){
  }
  
}
