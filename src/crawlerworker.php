<?php
include_once './crawlerbase.php';
class CrawlerWorker extends CrawlerBase
{
    public function run(publishresult &$fileresult,$echoon,$session,$usedb, $root,$nomefileini)
    {
        $this->isMainclient=false;
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
            
        }
        $depth=$this->_depth;
        $depthfromrow=0;
        $allseen=false;
        do
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
            
        }while(!$urllist->allpagesseen() && !$allseen );
       
        if ($usedb  )
        {
        $urllist-> closeconnection();
        }
    }
}