<?php
class Topic extends Controller{
  public function index(){

    $v = $this->getView();
    $topic = $this->db->topic->findOne(array(
      'uri' => urldecode($_GET[2])
    ));

    if(isset($_GET[3])&&$_GET[3]>0){
      $page=(int)$_GET[3];
      $last = lastpage($topic['count']);
      if($page>$last)$page=$last;
    }else{
      $page = 1;
    }

    $topicref = $this->db->createDBRef('topic',$topic);
    $data = $this->db->post->find(array(
      'topic' => $topicref
    ))->skip(($page-1)*10)->limit(10);

    $count = $data->count(false);
    $topic = new MongoData($topic,$this->db);
    $forum = $topic->forum;
    $v->assign('uri_len',2); //for pagination
    $v->assign('page',$page);
    $v->assign('lastpage',lastpage($count));
    $v->assign('count',$count);
    $v->assign('topic',$topic);
    $v->assign('forum',$forum);
    $v->assign('posts',$data);
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

    //check topic's name... 
    $cnt = $this->db->topic->find(array(
      'title' => $_POST['title']
    ))->count(true);

    $req = $_POST;
    $req['forum'] = $forumref;
    $req['count'] = 1;
    $req['uri'] = str_replace(' ','.',$_POST['title']);
    if($cnt>0)$req['uri'] .= ".$cnt";

    $data = array_allow($req,array(
      'title','count','uri','forum'
    ));   
    
    $this->db->topic->save($data);
    $tref = $this->db->createDBRef('topic',$data);
    //save post
    $post = array(
       'topic'  => $tref
      ,'author' => 'poweruser'
      ,'content' => $_POST['content']
    );

    $this->db->post->save($post);
    $this->redirect('forum/viewforum/'.$forum_uri);
  }

  public function deletetopic(){

  }
  
  public function savepost(){
    $topic_uri = urldecode($_POST['topic_uri']);
    $this->db->topic->update(
      array('uri' => $topic_uri),
      array('$inc'  => array('count' => 1))
    );
    $topic = $this->db->topic->findOne(array('uri' => $topic_uri));
    $topicref = $this->db->createDBRef('topic',$topic);
    $post = array(
       'topic'  => $topicref
      ,'author' => 'poweruser'
      ,'content' => $_POST['content']
    );

    $this->db->post->save($post);
    $count = $topic['count'];
    //TODO: add lastpage function to helpers
    $lastpage = floor($count/10) + (($count%10>0)?1:0);
    $this->redirect('topic/'.$topic_uri . '/' . $lastpage .'#last');
  }

  public function deletepost(){
  }
  
}
