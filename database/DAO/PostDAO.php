<?php

  require_once 'BaseDAO.php';
  
  class PostDAO extends BaseDAO{
    protected $id=0;
    protected $userid=1;
    protected $title='';
    protected $content='';
    protected $date;
    protected $ip='';
    protected $parent=0;
    protected $tableName = 'post';
    
    public function __construct(){
      $this->date=date('Y-m-d H:i:s');
      parent::__construct();
    }

    public function fetchAll($parent,$page=0,$limit=10){
      return $this->getQuery()->fetchAll($parent,$page,$limit);
    }

  }

?>
