<?php 
//  echo getcwd() . "\n";

 //chdir('./src/');
// echo getcwd() . "\n";
     include_once './mainapp.php' ;
     $nomefileini="dbconnection.ini";
      $session=strval($argv[1]);
      $url=strval($argv[2]);
      $root =strval($argv[3]);
      $depth=intval($argv[4]);
      $idworker=intval($argv[5]);
      $rc= new  runcrawler();
      $rc->runapp($url,true,false,$session,$root,$depth,$idworker,$nomefileini); //mainapp
?>