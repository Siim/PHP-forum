<?php

  require_once 'BaseDAO.php';
  
  class ForumDAO extends BaseDAO{
    protected $id=0;
    protected $title='';
    protected $description='';
    protected $date;
    protected $username='';
    protected $parent='';
    protected $count=0;
    protected $lastpost;
    protected $tableName = 'node';
    
    public function __construct(){
      $this->date=date('Y-m-d H:i:s');
      parent::__construct();
    }

    public function fetchAll($parent,$page=0,$limit=10,$order='lastpost DESC'){
      return $this->getQuery()->fetchAll($parent,$page,$limit,$order);
    }

    public function getLastPost(){
      return $this->getQuery()->getLastPost($this);
    }
  }

?>
