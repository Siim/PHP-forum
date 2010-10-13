<?php
class Topic extends Controller{
  public function index(){
  
  }

  
  public function newforum(){
    $this->setFile('addforum.haml');
    $this->render();
  }

  public function edittopic(){
  
  }
  
  public function newtopic(){
    $this->setFile('newtopic.haml');
    $this->render();
  }
  
  public function savetopic(){
  }

  public function deletetopic(){

  }
  
  public function savepost(){

  }

  public function deletepost(){
  }
  
}
