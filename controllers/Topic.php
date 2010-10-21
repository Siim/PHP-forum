<?php
class Topic extends Controller{
  public function index(){

    $v = $this->getView();
    $topic = $this->db->topic->findOne(array(
      'uri' => urldecode($_GET[2])
    ));

    $page = 1;
    if(isset($_GET[3]))$page = currentpage($_GET[3],lastpage($topic['count']));

    //get all posts for current topic
    $topicref = $this->db->createDBRef('topic',$topic);
    $data = $this->db->post->find(array(
      'topic' => $topicref
    ))->skip(($page-1)*10)->limit(10);

    $count = $data->count(false);
    $topic = new MongoData($topic,$this->db);

    //assign template vars
    //how long is path? (keep url short in pagination)
    $this->uri_len = 2; 
    $this->page = $page;
    $this->lastpage = lastpage($count);
    $this->count = $count;
    $this->topic = $topic;
    $this->forum = $topic->forum;
    $this->posts = $data;
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
    $req['lastpost'] = new MongoDate();
    $req['uri'] = str_replace(' ','.',$_POST['title']);
    if($cnt>0)$req['uri'] .= ".$cnt";

    $data = array_allow($req,array(
      'title','count','uri','forum','lastpost'
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
    $this->db->forum->update(
      array('uri' => $forum_uri),
      array('$inc'  => array('count' => 1)),
      array('$set' => array('lastpost' => new MongoDate()))
    );
    $this->redirect('forum/viewforum/'.$forum_uri);
  }

  public function deletetopic(){

  }
  
  public function savepost(){
    $topic_uri = urldecode($_POST['topic_uri']);
    $this->db->topic->update(
      array('uri' => $topic_uri),
      array('$inc'  => array('count' => 1)),
      array('$set' => array('lastpost' => new MongoDate()))
    );
    $topic = $this->db->topic->findOne(array('uri' => $topic_uri));

    $this->db->forum->update(
      array('_id' => $topic['forum']['$id']).
      array('$set' => array('lastpost', new MongoData()))
    );

    $topicref = $this->db->createDBRef('topic',$topic);
    $post = array(
       'topic'  => $topicref
      ,'author' => 'poweruser'
      ,'content' => $_POST['content']
    );

    $this->db->post->save($post);
    $count = $topic['count'];
    $lastpage = lastpage($count);
    $this->redirect('topic/'.$topic_uri . '/' . $lastpage .'#last');
  }

  public function deletepost(){
  }
  
}
