<?php

class Forum extends Controller{
  protected $user;
  
  public function index(){
    $res = $this->db->forum->find();
    $this->topics = $res;
    $this->setFile('list.haml');
    $this->render();
  }
  
  public function newforum(){
    $this->setFile('addforum.haml');
    $this->render();
  }

  public function viewforum(){
    //get forum by uri
    $forum = $this->db->forum->findOne(array(
      'uri' => urldecode($_GET[3])
    ));

    //page magic
    if(isset($_GET[4])&&$_GET[4]>0){
      $page = (int) $_GET[4];
    }else{
      $page = 1;
    }

    //get all topics for current forum
    $fref = $this->db->createDBRef('forum',$forum);
    $data = $this->db->topic->find(array(
      'forum' => $fref
    ))->skip(($page-1)*10)->limit(10);
    $count = $data->count(false);

    //assign template variables
    $this->topics = $data;
    //how long is path? (keep url short in pagination)
    $this->uri_len = 3;
    $this->page = $page;
    $this->count = $count;
    $this->lastpage = lastpage($count);
    $this->forum = new MongoData($forum);
    $this->setFile('viewforum.haml');
    $this->render();
  }
  
  
  public function editforum(){
  }


  public function saveforum(){
    $table = $this->db->forum;

    $post = $_POST;
    $post['uri'] = str_replace(' ','.',$post['title']);

    //initial topic count
    $post['count'] = 0;
    $post['lastpost'] = new MongoDate(strtotime("1970-01-01"));

    // check if the title exists
    // if it exists, then dont save the duplicate :)
    // yes it's arguable
    if(!$table->findOne(array('uri' => $post['uri']))){
      $table->save(array_allow($post,array(
        'title','description','uri','count','lastpost'
      )));
    }

    $this->redirect();
  }

  public function deleteforum(){
  }
  
}
