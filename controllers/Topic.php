<?php
class Topic extends Controller{
  public function index(){
  
  }

  public function edittopic(){
  
  }
  
  public function newtopic(){
    $this->setFile('newtopic.haml');
    $this->render();
  }
  
  public function savetopic(){
    //get forum
    $forum = $this->db->forum->findOne(array(
      'title' => str_replace('_',' ',$_POST['f'])
    ));


    //save post
    $post = array(
       'autohor' => 'unknown'
      ,'content' => $_POST['content']
    );
    $this->db->post->save($post);

    $_POST['post'] = array($this->db->createDBRef('post',$post));
    $_POST['forum'] = $this->db->createDBRef('forum',$forum);
    $_POST['count'] = 1;

    $topic = array_allow($_POST,array(
      'title','post','forum','count'
    ));

    //save topic
    $this->db->topic->save($topic);
    $this->redirect('forum/viewforum/'.$_POST['f']);


  }

  public function deletetopic(){

  }
  
  public function savepost(){

  }

  public function deletepost(){
  }
  
}
