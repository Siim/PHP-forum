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
    //save post
    $post = array(
       'autohor' => 'unknown'
      ,'content' => $_POST['content']
    );
    $this->db->post->save($post);

    $_POST['post'] = $this->db->createDBRef('post',$post);
    $topic = array_allow($_POST,array(
      'title','post'
    ));

    //save topic
    $this->db->topic->save($topic);
    //$this->redirect();

    //update forum
    $forum = $this->db->forum->update(
      array('title' => str_replace('_',' ',$_POST['f'])),
      array('$push' => array('topic' => $this->db->createDBRef('topic',$topic)))
    );
  }

  public function deletetopic(){

  }
  
  public function savepost(){

  }

  public function deletepost(){
  }
  
}
