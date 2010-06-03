<?php

require_once 'BaseQuery.php';

class TopicQuery extends BaseQuery{

  public function __construct(){    
    parent::__construct();
    
  }
  
  public function fetchAll($parent,$model){
    $q = $this->prepare('
      SELECT
        post.id AS id,
        post.title AS title,
        post.content AS content,
        post.date AS date 
      FROM post
      INNER JOIN node ON post.parent = node.id 
      WHERE node.id=?  
    ');

    $q->execute(array($parent));
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }
  
  //TODO: only update statement...
  protected function update($model){
    $tn = $model->tableName;

    //upate topic's stats
    $query = $this->prepare(
      "UPDATE node SET count=?,lastpost=? WHERE id=?"
    );
    $query->execute(array(
      $model->count,
      $model->date,
      $model->id
    ));

    //update forum's stats
    $query = $this->prepare("UPDATE node SET lastpost=? WHERE id=?");
    $query->execute(array($model->date,$model->parent));
    
    $query = $this->prepare('INSERT INTO post (content,date,ip,parent,userid) VALUES (?,?,?,?,?)');
    $query->execute(array(
      $model->content,
      $model->date,
      $model->ip,
      $model->id,
      $model->userid
    ));
  }
  
  protected function insert($model){
    //make unique identifier for this topic to avoid setting the post to the wrong topic
    $csum = md5($model->userid.$model->title.$model->count.$model->parent.$model->lastpost);
    $query = $this->prepare(
      "INSERT INTO node (username,title,count,parent,lastpost,csum) VALUES (?,?,?,?,?,?)"
    );

    $query->execute(array(
      $model->userid,
      $model->title,
      $model->count,
      $model->parent,
      $model->lastpost,
      $csum
    ));


    //update forum's stats
    $query = $this->prepare("UPDATE node SET lastpost=? WHERE id=?");
    $query->execute(array($model->lastpost,$model->parent));
    
    //last node id
    $query = $this->prepare("SELECT id FROM node WHERE csum=?");
    $query->execute(array($csum));
    $res = $query->fetch();
    $id = $res['id'];
    
    $query = $this->prepare('INSERT INTO post (content,date,ip,parent,userid) VALUES (?,?,?,?,?)');
    $query->execute(array(
      $model->content,
      $model->date,
      $model->ip,
      $id,
      $model->userid
    ));
    return $id;
  }
}

?>
