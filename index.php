
<?php 
//debug
// // //echo getcwd() . "\n";

// // chdir('./src/');
// // //echo getcwd() . "\n";
// // include_once './mainapp.php' ; 
// // $nomefileini="dbconnectiondebug.ini";
// // $depth=5;
// // $usadatabase=false;
// // 		 if (file_exists("./$nomefileini"))
// // 		 {
// // 			 $p= new params("./$nomefileini");
// // 			 $depth=$p->depth;
// // 			 $usadatabase=$p->usedb;
// // 		 }
		 
// // $rc= new  runcrawler();
// // $root="..";
// // $session =$rc->makedirtemp($root);	
// // //http://dittlermariaester.com/
// // //https://applieddigitalskills.withgoogle.com/c/en/curriculum.html
// // $rc->runapp('http://dittlermariaester.com',$usadatabase,true,$session,$root,$depth,0,$nomefileini); //mainapp 
// //phpinfo(); 
// // USAGE
// //php -S 0.0.0.0:8000	
?>


<html>
<head>
		<title>Online  Crawler</title>
	</head>
	<body style="text-align:center">
    <h1>Online  Crawler and Broken Link Checker</h1>

<form  action="./src/startcrawler.php">
  <p><label for="w3review">Enter your URL (e.g. www.example.com) :</label></p>
  <textarea  id="w3review" name="w3review" rows="1" cols="100">https://applieddigitalskills.withgoogle.com/s/en/home</textarea>
  <br>  
  <br> 
  <input  type="submit" value="Submit">
</form>
<script>
 
  function fillstatusbar(x) {
  
   document.getElementById("w3result").innerHTML=x;
}
function fillsearchbar(x) {
  
  document.getElementById("w3review").innerHTML=x;
}
</script>
<p  >Click the "Submit"</p>
<form  >
<textarea   id="w3result" name="w3result" rows="2" cols="100"></textarea>
</form>
	</body>
</html>