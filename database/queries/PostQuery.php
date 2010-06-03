<?php

require_once 'BaseQuery.php';

class PostQuery extends BaseQuery{

  public function __construct(){    
    parent::__construct();
    
  }
  
  public function fetchAll($parent,$page,$limit){
    $limit = (int) $limit;
    $page = (int) $page;

    $q = $this->prepare("
      SELECT 
        post.id AS id,
        post.title AS title,
        post.content AS content,
        post.date AS date,
        post.ip AS ip,
        user.username,
        user.posts,
        user.admin 
      FROM post
      INNER JOIN node ON post.parent = node.id 
      LEFT JOIN user ON post.userid = user.id
      WHERE node.id=?
      LIMIT $page,$limit
    ");

    $q->execute(array($parent));
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }
  
  protected function update($model){
    $tn = $model->tableName;
    $query = $this->prepare(
      "UPDATE $tn SET title=?,description=?,date=?,author=?,grp=? WHERE id=?"
    );
    $query->execute(array(
      $model->title,
      $model->description,
      $model->date,
      $model->author,
      $model->group,
      $model->id
    ));
  }
  
  protected function insert($model){
    $query = $this->prepare(
      "INSERT INTO node (title,description,date,author,grp,parent) VALUES (?,?,?,?,?,?)"
    );
    
    $query->execute(array(
      $model->title,
      $model->description,
      $model->date,
      $model->author,
      $model->grp,
      $model->parent
    ));
  }

  public function delete($model){  
    $q = $this->prepare("DELETE FROM post WHERE id=?");
    $q->execute(array($model->id));
    $q = $this->prepare("SELECT date FROM post WHERE parent=? ORDER BY date DESC LIMIT 1");
    $q->execute(array($model->parent));
    $res = $q->fetch(PDO::FETCH_OBJ);

    //update parent's information
    $q = $this->prepare("UPDATE node SET count=count-1, lastpost=? WHERE id=?");
    $q->execute(array($res->date,$model->parent));

    //find newest post in forum...
    return $this->getLastPost($model);

  }

  /**
   * Get last post of forum
   * 
   */
  public function getLastPost($model){
    //model is post model... heh
    $q = $this->prepare("
      SELECT lastpost FROM node where parent=
      (
        SELECT parent FROM node WHERE id=? LIMIT 1
      ) 
      ORDER BY lastpost DESC LIMIT 1
    ");
    
    $q->execute(array($model->parent));
    
    $last = $q->fetch(PDO::FETCH_OBJ);
    return $last->lastpost;
  }
}

?>
