<?php

  class DB_Conf{
    static public function getUsername(){
      return '#USERNAME#';
    }
    
    static public function getPassword(){
      return '#PASSWORD#';
    }
    
    static public function getDSN(){
      return 'mysql:host=#HOST#;dbname=#DBNAME#';
    }
    
  }

?>
