<?php

require_once 'BaseQuery.php';

class ForumQuery extends BaseQuery{

  public function __construct(){    
    parent::__construct();
    
  }
  
  
  public function fetchAll($parent,$page,$limit,$order){
    $limit = (int) $limit;
    $page = (int) $page;
    $q = $this->prepare("SELECT * FROM node WHERE parent=? ORDER BY $order LIMIT $page ,$limit");
    $q->execute(array($parent));
    return $q->fetchAll(PDO::FETCH_ASSOC);
    
  }

  /**
   * Get last post of current forum
   */
  public function getLastPost($model){
    $q = $this->prepare("SELECT lastpost FROM node where parent=? ORDER BY lastpost DESC LIMIT 1");
    $q->execute(array($model->parent));
    $last = $q->fetch(PDO::FETCH_OBJ);
    return $last->lastpost;
  }

  protected function update($model){
    $tn = $model->tableName;
    $query = $this->prepare(
      "UPDATE $tn SET title=?,description=?,date=?,username=?,count=?,lastpost=? WHERE id=?"
    );
    echo $query->execute(array(
      $model->title,
      $model->description,
      $model->date,
      $model->username,
      $model->count,
      $model->lastpost,
      $model->id
    ));
  
  }
  
  protected function insert($model){

    $query = $this->prepare(
      "INSERT INTO node (title,description,date,username,parent) VALUES (?,?,?,?,?)"
    );
    $query->execute(array(
      $model->title,
      $model->description,
      $model->date,
      $model->username,
      $model->parent
    ));
  }

}

?>
