<?php
class Topic extends Controller{
  public function index(){

    $v = $this->getView();
    $topic = $this->db->topic->findOne(array(
      'uri' => urldecode($_GET[2])
    ));
    $data = new MongoData($topic,$this->db);
    $forum = $data->forum;
    $post = $data->post;
    $count = count($post);
    if(isset($_GET[3]))$page=(int)$_GET[3];
    else $page = 1;
    $post = array_slice($post,($page-1)*10,$page*10);
    $lastpage = floor($count/10) + (($count%10>0)?1:0);
    $v->assign('uri_len',2); //for pagination
    $v->assign('page',$page);
    $v->assign('lastpage',$lastpage);
    $v->assign('count',$count);
    $v->assign('topic',$data);
    $v->assign('forum',$forum);
    $v->assign('posts',$post);
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
    $forum_uri = urldecode($_POST['forum_uri']);
    $forum = $this->db->forum->findOne(array(
      'uri' => $forum_uri
    ));
    $forumref = $this->db->createDBRef('forum',$forum);

    //save post
    $post = array(
       'author' => 'poweruser'
      ,'content' => $_POST['content']
    );

    $this->db->post->save($post);
    $postref = $this->db->createDBRef('post',$post);



    //check topic's name... 
    $cnt = $this->db->topic->find(array(
      'title' => $_POST['title']
    ))->count(true);

    $req = $_POST;
    $req['post'] = array($postref);
    $req['forum'] = $forumref;
    $req['count'] = 1;
    $req['uri'] = str_replace(' ','.',$_POST['title']);
    if($cnt>0)$req['uri'] .= ":$cnt";

    $data = array_allow($req,array(
      'title','post','forum','count','uri'
    ));   
    
    $this->db->topic->save($data);
    $this->redirect('forum/viewforum/'.$forum_uri);
  }

  public function deletetopic(){

  }
  
  public function savepost(){
    $topic_uri = urldecode($_POST['topic_uri']);
    $post = array(
       'author' => 'poweruser'
      ,'content' => $_POST['content']
    );
    $this->db->post->save($post);
    $postref = $this->db->createDBRef('post',$post);

    $this->db->topic->update(
      array('uri' => $topic_uri),
      array(
        '$push' => array('post'  => $postref)
       ,'$inc'  => array('count' => 1)
      ));
    $topic = $this->db->topic->findOne(array('uri' => $topic_uri));
    $count = count($topic['post']);
    //TODO: add lastpage function to helpers
    $lastpage = floor($count/10) + (($count%10>0)?1:0);
    $this->redirect('topic/'.$topic_uri . '/' . $lastpage);
  }

  public function deletepost(){
  }
  
}
