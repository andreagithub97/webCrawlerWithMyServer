<?php
include_once './crawlermain.php';
include_once './crawlerworker.php';
include_once './publishresult.php'; 
class runcrawler
{
    function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function makedirtemp($root)
    {
        if (!file_exists("$root/tmp"))
        {
            mkdir("$root/tmp",0777,true);
        }
        $session =$this-> generateRandomString();   
        while (file_exists('$root/tmp/'.$session))
        {
            $session =$this->generateRandomString();
        }
        $tempdir="$root/tmp/$session";
        mkdir($tempdir,0777,true);
        return $session;
    }
  
    function runapp($startURL,$usedb,$isMainclient,$session, $root,$depth,$idworker,$nomefileini)
    {
        if ($depth<1)
        {
            $depth=1;
        }
        $filepath="$root/tmp/$session/$session";
        $pr=new publishresult($filepath,true);
        if($isMainclient)
        {
           echo "<script>";
           echo "fillsearchbar('$startURL');";
           echo "</script>"; 
        }
         $username = 'YOURUSER';
         $password = 'YOURPASS';
        if ($isMainclient)
        {
            $crawler = new CrawlerMain($startURL, $depth,$isMainclient,$idworker);
        }
        else
        {
            $crawler = new CrawlerWorker($startURL, $depth,$isMainclient,$idworker);
        } 
        $crawler->setHttpAuth($username, $password);
        // Exclude path with the following structure to be processed 
        $crawler->addFilterPath('customer/account/login/referer');
        if ($isMainclient)
        {
            $objDateTime = new DateTime('NOW');
            echo "$startURL<br>";
            echo "depth : $depth   session : $session  <br>";
            echo "start at".$objDateTime->format('c') ."<br>";
        }
        $serverfolder =(isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
        $crawler->run($pr,false,$session,$usedb, $root,$nomefileini);
        if ($isMainclient)
        {
            $pr->close();
            $objDateTime = new DateTime('NOW');
            echo "end at".$objDateTime->format('c') ."<br>";
            echo "<br>";
            //echo $_SERVER['HTTP_REFERER']; echo "<br>"; 
            $r=$serverfolder."/tmp/$session/$session";
            if(file_exists("$root/tmp/$session/$session.csv"))
            {
                echo "you can download result in csv format here:<a href='$r.csv'>cvs result</a>";// $pathresult
            }
            echo "<br>";
            if(file_exists( "$root/tmp/$session/$session.html"))
            {
                include "$root/tmp/$session/$session.html";             
            }
        }         
    }
}
?>