<?php

  require_once 'BaseDAO.php';
  
  class TopicDAO extends BaseDAO{
    //node id
    protected $id=0;
    
    //user id
    protected $userid=0;
    
    //topic
    protected $title='';
    
    //post content
    protected $content='';
    
    //post date
    protected $date;
    
    //poster ip
    protected $ip='';
    
    //count 
    protected $count=0;
    
    //parent id
    protected $parent=0;
    
    protected $lastpost;
    
    //table name
    protected $tableName = 'node';
    
    public function __construct(){
      $this->date=date('Y-m-d H:i:s');
      $this->lastpost=date('Y-m-d H:i:s');
      parent::__construct();
    }

    public function validate(){
      if(trim($this->content)==='')return false;
      else return true;
    }

  }

?>
