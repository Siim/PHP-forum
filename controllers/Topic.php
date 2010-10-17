<?php
class Topic extends Controller{
  public function index(){
    $v = $this->getView();
    $topic = $this->db->topic->findOne(array(
      'uri' => $_GET[2]
    ));
    $data = new MongoData($topic,$this->db);
    $v->assign('posts',$data->post);
    $this->setFile('viewtopic.haml');
    $this->render();
    
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
      'uri' => $_POST['forum_uri']
    ));
    $forumref = $this->db->createDBRef('forum',$forum);

    //save post
    $post = array(
       'author' => 'poweruser'
      ,'content' => $_POST['content']
    );

    $this->db->post->save($post);
    $postref = $this->db->createDBRef('post',$post);
    $_POST['post'] = array($postref);
    $_POST['forum'] = $forumref;
    $_POST['count'] = 1;
    $_POST['uri'] = str_replace(' ','-',$_POST['title']);

    $data = array_allow($_POST,array(
      'title','post','forum','count','uri'
    ));

    //check topic's name... if found, post to existing topic
    $topic = $this->db->topic->findOne(array(
      'title' => $data['title']
    ));

    if($topic){
      //push post to existing topic
      $this->db->topic->update(
        array(
          '_id' => $topic['_id']
        ),
        array(
          '$push' => array('post'  => $postref)
         ,'$inc'  => array('count' => 1)
        ));
    }else{
      $this->db->topic->save($data);
    }

    $this->redirect('forum/viewforum/'.$_POST['forum_uri']);
  }

  public function deletetopic(){

  }
  
  public function savepost(){

  }

  public function deletepost(){
  }
  
}
