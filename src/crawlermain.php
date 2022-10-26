<?php
include_once './crawlerbase.php';
class CrawlerMain extends CrawlerBase
{
    public function run(publishresult &$fileresult,$echoon,$session,$usedb, $root,$nomefileini)
    {
        $workersnumber=0;
        $workerprocess =array();
        if (!$usedb)
        {
            $urllist=new UrlListSimpleArray();// array();
        }
        else
        {
            $hostdb='hostdb';
            $userdb='userdb';
            $password='password';
            $database='database';
            if (file_exists("./$nomefileini"))
            {
                $p= new params("./$nomefileini");
                $hostdb=$p->hostdb;
                $userdb=$p->userdb;
                $password=$p->password;
                $database=$p->database;
                $workersnumber=$p->localworkernumbers;
            }
           $urllist = new UrlListSql($hostdb, $userdb, $password, $database,$session,$this->isMainclient);
            if(!$urllist->openconnection())
            {echo "no db connection<br>";
                $usedb=false;
                $urllist=new UrlListSimpleArray();
            }
            else
            {
                echo " $workersnumber worker<br>";
            }
            
        }
       
        
        
       $depth=$this->_depth;
       if ($this->isMainclient)
       {
         
               

                $this->crawl_page($this->_url, 0,$fileresult,$urllist,$echoon,$usedb);
                //qui fai partire con shell i workers secondari
                if ($usedb)
                {
                  for ($i=0;$i<$workersnumber;$i++)
                    {
                        //$descriptorspec = [STDIN, STDOUT, STDOUT];
                        $descriptorspec = array(
                            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                            2 => array("file", "./error-output.txt", "a") // stderr is a file to write to
                        );
                        
                        $cmd = "php ./startworkerlocal.php  '$session' '$this->_url'  '$root' $depth ".($i+1)." > /dev/null 2>&1 &";
                        $proc = proc_open($cmd, $descriptorspec, $pipes);
                        array_push($workerprocess,$proc);
                    }           
                }
                    
                
       } 
      
        $depthfromrow=0;
        $allseen=false;
        do
        {   
            if ($workersnumber<1 || !$this->isMainclient)
            {
                 
                foreach($urllist->getPageToProcess($depthfromrow) as &$hr)            
                {
                    if ($hr!=null)
                    {
                        $this->crawl_page($hr, $depthfromrow + 1,$fileresult,$urllist,$echoon,$usedb);
                    }
                    else
                    {
                        $allseen=true;
    
                    }
                    
                }
            }
            else
            {
               
                if ($this->isMainclient)
                    {
                     $this->_printResult($this->_url, $depth, 0, 0,$echoon,$urllist); 
                      sleep(2);
                    }
            }
            

        }while(!$urllist->allpagesseen() && !$allseen );
       
       
        if ($usedb  )
        {
            if ($this->isMainclient)
            {
                $result=$urllist->getresult();
                foreach ($result as &$r)
                {
                    $row=  explode(";",$r);
                    //$row['url'].';'.$row['codehttp'].';'.$row['external'].';'.$row['broken'].';'.$row['depth']
                    // $fileresult->writerowhml( $url,$httpcode,$isexternal,$isbroken,$time);
                    $fileresult->writerowhml( $row[0],$row[1],$row[2],$row[3],$row[4]);
                    $fileresult-> writerowcsv( $row[0],$row[1],$row[2],$row[3],$row[4]);  
                }
                for ($i=0;$i<$workersnumber;$i++)
                {            
                    proc_close($workerprocess[$i]);
                }
            }
          
            
            
            
        $urllist-> closeconnection();
         
        }
    }
}