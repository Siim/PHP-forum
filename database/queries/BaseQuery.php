<?php


/**
 * Some common queries
 */
class BaseQuery extends PDO{

  public function __construct(){

    require_once FORUM_ROOT . '/database/DB_Conf.php';
		$conf = new DB_Conf();
    try{
		  parent::__construct($conf->getDSN(), $conf->getUsername(), $conf->getPassword());
    }catch(PDOException $e){
      throw $e;
    }
  }


  public function fetchItem(&$model){
    $q = $this->prepare('SELECT * FROM '. $model->tableName .' WHERE id=? LIMIT 1');
    $q->execute(array($model->id));
    $data = $q->fetch();
    
    foreach($data as $k => $v){
      $model->$k = $v;
    }
  }

  public function save($model){
    if($model->id==0 || $model->id===''){
      return $this->insert($model);
    }else{
      return $this->update($model);
    }
  }
  
  public function myquery($query,$items=array(),$fetch=false){
    $q= $this->prepare($query);
    $q->execute($items);
    if($fetch===true){
      return $q->fetch();
    }
  } 

  public function delete($model){  
    $q = $this->prepare("DELETE FROM " .$model->tableName ." WHERE id=?");
    $q->execute(array($model->id));

  }

}

?>
