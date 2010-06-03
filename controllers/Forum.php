<?php

require_once 'Base.php';
require_once FORUM_ROOT . 'database/DAO/ForumDAO.php';
require_once FORUM_ROOT . 'database/DAO/PostDAO.php';
require_once FORUM_ROOT . 'database/DAO/TopicDAO.php';
require_once FORUM_ROOT . 'database/DAO/filtered/SaveTopicDAO.php';
require_once FORUM_ROOT . 'lib/bbcode/BBCode.php';
require_once FORUM_ROOT . 'database/DAO/UserDAO.php';


/**
 * Boring stuff here :)
 */
class Forum extends Base{
  protected $user;
  function __construct(){
    parent::__construct($_GET);
  }
  
  public function index(){

    $d = new ForumDAO();
    $res = $d->fetchAll(0,0,50,'title ASC');
    $this->getView()->assign('teemad',$res);
    $this->setTitle('Forum');
    $this->setFile('list.haml');
    $this->render();
  }
  
  public function viewforum(){
    //query for parent + parent title
    $d = new ForumDAO();
    $d->id = (int) $_GET['f'];
    $d->fetchItem();

    $v = $this->getView();
    $v->assign('count',$d->count);

    //divide results into pages...
    $page = 0;
    if(isset($_GET['page'])){
      $page = (int) $_GET['page'];
    }

    $this->getView()->assign('page',$page);
    $perpage = 10;
    $v->assign('teemad',$d->fetchAll($d->id,$page,$perpage));

    $v->assign('forum',$d);
    $v->assign('user',$_SESSION['user']);
    $this->setTitle($d->title);
    $this->setFile('viewforum.haml');
    $this->render();
  }
  
  public function viewtopic(){
    $bbcode = new BBCode();
    $d = new ForumDAO();
    $parent = new ForumDAO();
    $post = new PostDAO();
    $d->id = (int) $_GET['t'];
    $d->fetchItem();
    $parent->id=$d->parent;
    $parent->fetchItem();
    $v = $this->getView();
    $page = 0;

    if(isset($_GET['page'])){
      $page = (int) $_GET['page'];
    }
    $v->assign('page',$page);
    $v->assign('count',$d->count);

    $perpage = 10;
    
    $v->assign('bbcode',$bbcode);
    $v->assign('posts',$post->fetchAll($d->id,$page,$perpage));
    $v->assign('topic',$d);
    $v->assign('parent',$parent);
    $v->assign('id',$d->id);
    $this->setTitle($d->title);
    $this->setFile('viewtopic.haml');
    $this->render();
  }
  
  public function newforum(){
    if($this->user['admin']){
      $forum = new ForumDAO();
      $this->setTitle('Add new subforum');
      $this->setFile('addforum.haml');
      $this->render();
    }
  }
  
  
  public function editforum(){
    if($this->user['admin']){
      $this->getView()->assign('f',$_GET['f']);
      $forum = new ForumDAO();
      $forum->id = $_GET['f'];
      $forum->fetchItem();
      $this->getView()->assign('forum', $forum);
      $this->setTitle('Edit subforum');
      $this->setFile('addforum.haml');
      $this->render();
    }
  }
  public function edittopic(){
    if($this->user['admin']){
      $this->getView()->assign('f',$_GET['f']);
      $forum = new ForumDAO();
      $forum->id = $_GET['f'];
      $forum->fetchItem();
      $this->getView()->assign('forum', $forum);
      $this->getView()->assign('edit',true);
      $this->setTitle('Edit topic');
      $this->setFile('addforum.haml');
      $this->render();
    }
  }

  public function saveforum(){
    $d = new ForumDAO();
    if(isset($_POST['f'])){
      $d->id = $_POST['f'];
      $d->fetchItem();
    }

    $d->title = $_POST['title'];
    $d->description = $_POST['description'];
    $d->save();
    if($_POST['topic']==1){
      $this->redirect('?a=viewforum&f='.$d->parent);
    }else{
      $this->redirect('');
    }
  }

  public function deleteforum(){
    if($this->user['admin']){
      $f = new ForumDAO();
      $f->id = $_GET['f'];
      $f->fetchItem();
      $f->delete();
      $this->redirect('');
    }
  }
  
  public function newtopic(){
    if(isset($_SESSION['post']))
      $this->getView()->assign('post',$_SESSION['post']);
    $this->setTitle('New topic');
    $parent = (int) $_GET['f'];
    $this->getView()->assign('parent',$parent);
    $this->setFile('newtopic.haml');
    $this->render();
  }
  
  public function savetopic(){
    $t = new SaveTopicDAO();
    $parent = new ForumDAO();
    $u = new UserDAO();
    $u->id = $_SESSION['user']['id'];
    $u->fetchItem();
    $p = $_POST;
    $t->title = $p['title'];
    $t->count = 1;
    $t->userid = $this->user['id'];
    $t->content = $p['content'];
    $t->ip = $_SERVER['REMOTE_ADDR'];
    $t->parent = $p['f'];


    //update parent count...
    $parent->id = $t->parent;
    $parent->fetchItem();
    $parent->count +=1;
    $parent->date = $t->date;
    unset($_SESSION['post']);


    if($t->validate()){
      $parent->save();
      $id = $t->save();
      $_SESSION['user']['posts']++;
      $u->posts++;
      $u->save();
      $this->redirect("?a=viewtopic&t=$id");
      
    }else{
      $_SESSION['post'] = $_POST;
      $this->redirect($_SERVER['HTTP_REFERER'],false);
    }
  }

  public function deletetopic(){
    $f = new ForumDAO();
    $parent = new ForumDAO();
    $f->id = $_GET['f'];
    $f->fetchItem();
    $parent->id = $f->parent;
    $parent->fetchItem();
    $parent->count--;
    $f->delete();
    $parent->lastpost = $f->getLastPost();
    print_r($parent->lastpost);
    $parent->save();
    $this->redirect('?a=viewforum&f='.$parent->id);
  
  }

  
  public function savepost(){

    $parent = new ForumDAO();
    $t = new TopicDAO();
    $u = new UserDAO();
    $p = $_POST;

    $t->id = $p['id'];
    $t->fetchItem();
    $parent->id = $t->parent;
    $parent->fetchItem();

    $t->title = $p['title'];
    $t->date = date('Y-m-d H:i:s');
    $t->count +=1;
    $user = $this->user;
    $t->userid = $user['id'];
    $u->id = $user['id'];
    $u->fetchItem();
    $t->content = $p['content'];
    $t->ip = $_SERVER['REMOTE_ADDR'];
    $parent->lastpost = $t->date;

    if($t->validate()){
      $t->save();
      $parent->save();
      $u->posts++;
      $_SESSION['user']['posts']++;
      $u->save();
    }else{
      $_SESSION['message'] = "Cannot save empty message!";
    }
    $this->redirect($_SERVER['HTTP_REFERER']."#last",false);
    exit();
  
  }

  public function deletepost(){
    if($_SESSION['user']['admin']==1){
      $post = new PostDAO();
      $post->id = $_GET['pid'];
      $post->fetchItem();
      $date = $post->delete();

      //make sure that dates get updated correctly
      $forum = new ForumDAO();
      $topic = new TopicDAO();
      $topic->id = $post->parent;
      $topic->fetchItem();
      $forum->id = $topic->parent;
      $forum->fetchItem();
      $forum->lastpost = $date;

      $forum->save();
      $this->redirect($_SERVER['HTTP_REFERER'],false);
    }
  }
}
