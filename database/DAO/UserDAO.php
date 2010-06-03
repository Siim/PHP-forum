<?php

require_once 'BaseDAO.php';

class UserDAO extends BaseDAO{
  protected $id=0;
  protected $username='';
  protected $password;
  protected $email='';
  protected $posts=0;
  protected $registered;
  protected $last;
  protected $admin=0;
  protected $active;

  //table name
  protected $tableName = 'user';
  
  public function __construct(){
    $this->registered=date('Y-m-d H:i:s');
    parent::__construct();
  }

  public function fetchItemByCode(){
    return $this->getQuery()->fetchItemByCode($this);
  }

  public function fetchItemByEmail(){
    return $this->getQuery()->fetchItemByEmail($this);
  }
}
