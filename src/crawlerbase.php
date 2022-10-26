<?php
include_once './publishresult.php';
 
include_once './urllistgestor.php';
include_once './urllistsimplearray.php';
include_once './urllistsql.php';
  include_once './params.php';
abstract class CrawlerBase
{
    protected $_url;
    protected $_depth;
    protected $_host;
    protected $_useHttpAuth = false;
    protected $_user;
    protected $_pass;
    //protected $_seen = array();
    protected $_filter = array();
    protected $_rc ;
    protected $isMainclient;
    protected $idworker;

    abstract public function run(publishresult &$fileresult,$echoon,$session,$usedb, $root,$nomefileini);

    public function __construct($url, $depth ,$isMainclient,$idworker)
    {
        $this->_rc=1;
        $this->_url = $url;
        $this->_depth = $depth;
        $parse = parse_url($url);
        $this->_host = $parse['host'];
        $this->isMainclient =$isMainclient;
        $this->idworker=$idworker;
    }

    protected function _processAnchors($content, $url, $depth,publishresult &$fileresult,UrlListGestor &$urllist,$echoon)
    {
        $listahref=array();
        // do
        // {
        $dom = new DOMDocument('1.0');
        // set error level
        $internalErrors = libxml_use_internal_errors(true);
        @$dom->loadHTML($content);
        $anchors = $dom->getElementsByTagName('a');

        foreach ($anchors as $element) 
        {
            $href = $element->getAttribute('href');
            if (0 !== strpos($href, 'http'))
             {
                $path = '/' . ltrim($href, '/');
                if (extension_loaded('http')) 
                {
                    $href ='';// http_build_url($url, array('path' => $path));
                }
                 else 
                {
                    $parts = parse_url($url);
                    $href = $parts['scheme'] . '://';
                    if (isset($parts['user']) && isset($parts['pass'])) {
                        $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                    }
                    $href .= $parts['host'];
                    if (isset($parts['port'])) {
                        $href .= ':' . $parts['port'];
                    }
                    $href .= $path;
                }
                
               
            }
              //array_push ($urllist,trim($href,"/"));
             //$urllist->push(trim($href,"/"),false);
             array_push($listahref,trim($href,"/"));
        }
        $urllist->pushList($listahref,false,$depth);
        // Crawl only link that belongs to the start domain
        //$this->crawl_page($href, $depth - 1,$fileresult);
        //$urllist = array_unique($urllist);
        //foreach($urllist as &$hr)
        
        //}while(!$urllist->allpagesseen());
    }

    protected function _getContent($url)
    {
        $handle = curl_init($url);
        if ($this->_useHttpAuth) {
            curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($handle, CURLOPT_USERPWD, $this->_user . ":" . $this->_pass);
        }
        // follows 302 redirect, creates problem wiht authentication
   //curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE);
        // return the content
       curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);
        if(curl_errno($handle)){
            echo 'Curl error: ' . curl_error($handle);
        }
        // response total time
        $time = curl_getinfo($handle, CURLINFO_TOTAL_TIME);
        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);
        return array($response, $httpCode, $time);
    }

    protected function _printResult($url, $depth, $httpcode, $time,$echoon,UrlListGestor &$urllist)
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        $currentDepth = $this->_depth - $depth;
        //$count = count($this->_seen);
        $count =  $urllist->getseenCount($url) ;
        if ($echoon)
        { 
           
            echo "N::$count,CODE::$httpcode,TIME::$time,DEPTH::$currentDepth URL::$url <br>";
                 
        }
        else
        {
            
            //$processed =count($urllist);
           
            // if ($this->_rc>10 || $this->_rc==1) 
            // {
                $processed = $urllist->processedPageCount();
                $foundedPageCount= $urllist->foundedPageCount();
               // echo"<br>";
                
                //echo"processed  $processed page\n";
                echo "<script>";
		        echo "fillstatusbar('processed $processed pages of $foundedPageCount founded');";
		        echo "</script>";
            // }
             
           
            // $this->_rc+=1;
        }
       
       // fwrite( $fileresult,"N::$count,CODE::$httpcode,TIME::$time,DEPTH::$currentDepth URL::$url <br>");
       ob_start();
       flush();
    }

    // protected function isValid($url, $depth)
    // {
    //     if (strpos($url, $this->_host) === false
    //         || $depth === 0
    //         || isset($this->_seen[$url])
    //     ) {
    //         return false;
    //     }
    //     foreach ($this->_filter as $excludePath) {
    //         if (strpos($url, $excludePath) !== false) {
    //             return false;
    //         }
    //     }
    //     return true;
    // }
    protected function isValid0($url, $depth,UrlListGestor &$urllist )
    {
        //if (isset($this->_seen[$url]))
        if ($urllist->IsSeen($url))
        {
            //già visto
             return 4;
        }
        if (strpos($url, $this->_host) === false)
        {
                //esterno
            return 3;
        }
        if (  $depth >=  $this->_depth) {
            //limite profondità
            return 2;
        }
        foreach ($this->_filter as $excludePath) {
            if (strpos($url, $excludePath) !== false) {
                //escluso dal filtro
                return 1;
            }
        }
        return 0; // valido
    }
    public function crawl_page($url, $depth,publishresult &$fileresult,UrlListGestor &$urllist,$echoon, $usedb)
    {
         
        // if (!$this->isValid($url, $depth)) {
        //     return;
        // }
        $isexternal=false;
        $ismaxdepth=false;
        switch ($this->isValid0($url, $depth,$urllist)) 
        {
            case 4:
                //già visto
                return;
            case 3:
                $isexternal=true;
                break;
            case 2:
                $ismaxdepth=true;
                
                break;
            case 1:
                //escluso dal filtro
                return;
                break;
            case 0:
                //valid
                break;
        }
        // add to the seen URL
        //$this->_seen[$url] = true;
        //$urllist->_seen[$url] = true;
        
        // get Content and Return Code
        list($content, $httpcode, $time) = $this->_getContent($url);
        $isbroken=false;
        if ($httpcode==0)
        {
            $isbroken=true;
            
        }
        // print Result for current Page
        if ($this->isMainclient)
        {
           $this->_printResult($url, $depth, $httpcode, $time,$echoon,$urllist); 
        }
        
         
        
        //  $count =count($this->_seen);
        if (!$usedb)
        {
          $fileresult->writerowhml( $url,$httpcode,$isexternal,$isbroken,$time);
          $fileresult-> writerowcsv( $url,$httpcode,$isexternal,$isbroken,$time);   
        }
       
        //fwrite( $fileresult,'url'=>$url, 'code'=>$httpcode,'isext'=>$isexternal , 'isbroken'=>$isbroken, 'time'=>$time\n");
        // array_push( $urllist);
         
         $urllist->setseen( $url,$isexternal , $isbroken,$depth,$httpcode,$this->idworker);
        // process subPages
        if ($isexternal || $isbroken || $ismaxdepth)
        {
            return;
        }
        if ($content!=null)
        {
            $this->_processAnchors($content, $url, $depth,$fileresult,$urllist,$echoon); 
        }
        else
        {
            return;
        }
         
    }

    public function setHttpAuth($user, $pass)
    {
        $this->_useHttpAuth = true;
        $this->_user = $user;
        $this->_pass = $pass;
    }

    public function addFilterPath($path)
    {
        $this->_filter[] = $path;
    }

 
}

?>