
		<?php 
         include_once './mainapp.php' ;
		 include_once '../index.php' ;
		 $nomefileini="dbconnection.ini";
		 $depth=5;
		 $usadatabase=false;
		 if (file_exists("./$nomefileini"))
		 {
			 $p= new params("./$nomefileini");
			 $depth=$p->depth;
			 $usadatabase=$p->usedb;
		 }
		 
		$rc= new  runcrawler();
	    $root="..";
		
		$session =$rc->makedirtemp($root);					 
		$rc->runapp($_REQUEST["w3review"],$usadatabase,true,$session, $root,$depth,0,$nomefileini);

		
		 // fclose($fileresult);
		//phpinfo(); 
		// USAGE
		//php -S 0.0.0.0:8000	
		?>