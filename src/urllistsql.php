<?php
include_once './urllistgestor.php';
class UrlListSql extends UrlListGestor
{
      protected mysqli $conn;
      //protected $urllist;
      protected $session;
      protected $host;
      protected $user;
      protected $password;
      protected $database;
      protected $spooltablename;
      protected $isMainclient;
      public function pushList(array $hreflist ,$seen,$depth)
      {
        $sql = "INSERT IGNORE INTO  `$this->spooltablename`
          (`session`,
          `url`,
          `seen`,
          `depth`)
          VALUES";
           foreach($hreflist  as &$hr)  
           {
            $sql=$sql."('$this->session',
            '$hr',
            '$seen',
             '$depth'),";
           }
           $sql =trim($sql,",");
           $result = $this->conn->query($sql);
      }
      public function push($href ,$seen,$depth,$httpcode,$idworker)
      {
        // array_push ($this->urllist,trim($href,"/"));
        // $this->urllist = array_unique($this->urllist);
        // $href =trim($href,"/");
        // $sql = "select id from `crspool` where session='$this->session' and url='$href'";
        // $result = $this->conn->query($sql);
        // if ($result->num_rows == 0) 
        {//array_unique
          $sql = "INSERT IGNORE INTO  `$this->spooltablename`
          (`session`,
          `url`,
          `seen`,
          `depth`,
          `codehttp`)
          VALUES
          ('$this->session',
          '$href',
          '$seen',
          '$depth',
          '$httpcode')";
           $result = $this->conn->query($sql);
        } 
      }
      public  function processedPageCount()
      {
        $sql = "select count(*) from `$this->spooltablename` where session='$this->session' and seen=1 ";
      
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            return $row['count(*)'];
           // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
        }
         
        return 0;
      }
      function foundedPageCount()
      {
        $sql = "select count(*) from `$this->spooltablename` where session='$this->session'  ";
      
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            return $row['count(*)'];
           // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
        }
         
        return 0;
      }
      function allpagesseen()
      {
        return (count($this->getPageToProcess($depth))==0);
      }
      public  function getPageToProcess(&$depth)
      {
        $sql = "select url,depth from `$this->spooltablename` where session='$this->session' and seen=false LIMIT 1";
      
         $result = $this->conn->query($sql);
         $l=array();
         if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
            array_push($l,$row['url']);
            $depth= $row['depth'];
           // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
      } 
       
        return  $l;
      }
      public  function setseen($url ,$isexternal, $isbroken,$depth,$httpcode,$idworker )
      {
        $sql = "select * from `$this->spooltablename` where session='$this->session'  and url='$url' ";
      
        $result = $this->conn->query($sql);
       
        if ($result->num_rows > 0) {
          $sql = "update    `$this->spooltablename` set seen=true, idworker=".intval($idworker).", codehttp=".intval($httpcode).", broken=".intval($isbroken)." , external=".intval($isexternal)." where session='$this->session'  and url='$url'";
          $result = $this->conn->query($sql);
       }
       else
       {
        $this->push($url,true,$depth,$httpcode,$idworker);
       }
        
      
        
        
         
      }
      private function getseen()
      {
        $sql = "select url from `$this->spooltablename` where session='$this->session' and seen=true ";
      
         $result = $this->conn->query($sql);
         $l=array();
         if ($result->num_rows > 0) {
     
          // output data of each row
          while($row = $result->fetch_assoc()) {
            array_push($l,$row['url']);
           // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
        }
          return $l;
      }
      public function getresult()
      {
        $sql = "select url,codehttp,external,broken,depth  from `$this->spooltablename` where session='$this->session' and seen=true ";
      
         $result = $this->conn->query($sql);
         $l=array();
         if ($result->num_rows > 0) {
     
          // output data of each row
          while($row = $result->fetch_assoc()) {
            $e=$row['url'].';'.$row['codehttp'].';'.$row['external'].';'.$row['broken'].';'.$row['depth'];
            array_push($l,$e);
           // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
        }
          return $l;
      }
      public  function IsSeen($url)
      {
        $sql = "select url from `$this->spooltablename` where session='$this->session' and seen=true and url='$url'";
      
         $result = $this->conn->query($sql);
         $l=array();
         if ($result->num_rows > 0) {
          return true;
          // // output data of each row
          // while($row = $result->fetch_assoc()) {
          //   array_push($l,$row['url']);
          //  // echo $row['id'].' '.$row['session'].' '.$row['url'].' '.$row['seen']."<br>";
          }
          return false;
      }
      function getseenCount()
        {
          return count($this->getseen());
        }
         
       
    public function __construct($host,$user,$password,$database ,$session,$isMainclient)
    {
        
        

        $this->session=$session;
          $this->host=$host;
          $this->user=$user;
          $this->password=$password;
          $this->database=$database;
          $this->spooltablename="queque".$session;
          $this->isMainclient=$isMainclient;
      
    }
    public function createspooltable()
    {
      $sql="CREATE TABLE `$this->spooltablename` (
        `session` varchar(10) DEFAULT NULL,
        `url` varchar(255) NOT NULL,
        `codehttp` int(11) DEFAULT 0,
        `seen` tinyint(4) DEFAULT 0,
        `external` tinyint(4) DEFAULT 0,
        `broken` tinyint(4) DEFAULT 0,
        `depth` int(11) DEFAULT 0,
        `time` varchar(45) DEFAULT 0,
        `idworker` int(11) DEFAULT 0,
        PRIMARY KEY (`url`)
      ) ;"; //ENGINE=InnoDB DEFAULT CHARSET=utf8
      $result = $this->conn->query($sql);
      return $result;

    }
    public function openconnection( )
    {
      //echo "$this->host,$this->user,$this->password,$this->database <br>";
      $this->conn = new mysqli($this->host,$this->user,$this->password,$this->database);//remoto
      if ($this->conn->connect_error) {
        echo "<br> db connection failed <br>";
        //echo "$this->host,$this->user,$this->password,$this->database <br>";
      return false;
        //die("Connection failed: " . $this->conn->connect_error);
      }
         
       if (!$this->isMainclient)
       {
         return true;
       }

    return $this->createspooltable();
    }
    public function closeconnection( )
    {
      $this->conn->close(); 
    }
    
}
?>