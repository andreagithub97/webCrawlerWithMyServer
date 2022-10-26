<?php
include_once './urllistgestor.php';
class UrlListSimpleArray extends UrlListGestor
{
  protected $_seen = array();
    protected $urllist;
    public  function setseen($url,$isexternal, $isbroken,$depth,$httpcode,$idworker)
    {
      $url=trim($url,"/");
      if (!in_array($url,$this->urllist))
      {
        array_push ($this->urllist,$url);
      }
      $this->_seen[$url] = true;
    }
    function pushList(array $hreflist ,$seen,$depth)
    {
      $this->urllist =array_merge($this->urllist,array_unique($hreflist));
      $this->urllist = array_unique($this->urllist);
    }
      public function push($href,$seen,$depth,$httpcode,$idworker)
      {
        array_push ($this->urllist,trim($href,"/"));
        $this->urllist = array_unique($this->urllist);
        if ($seen){$this->setseen($href,false,false,$depth,$httpcode,$idworker);}
      }
      function allpagesseen()
      {
        if(  count($this->_seen)==count($this->urllist))
        {
          if (count(array_diff(array_keys($this->_seen),$this->urllist))==0)
          {
            return true;
          }
          return false;
        }
        return false;
         
      }
      public function getresult()
      {
        $r=array();
        return $r;
      }
      public  function processedPageCount()
      {
            return count($this->_seen);
      }
      function foundedPageCount()
      {
        return count($this->urllist);
         
      }
      public  function getPageToProcess(&$depth)
      {
        $result=array();
        foreach($this->urllist as &$h)
        {
          if (!isset($this->_seen[$h]))
          {
            array_push($result,$h);
              return $result;
          }
        }
        return $result;
      }
       
        public  function IsSeen($url)
        {
          return isset($this->_seen[$url]);
        }
        function getseenCount()
        {
          return count($this->_seen);
        }
    public function __construct( )
    {
        $this->_seen = array();
        $this->urllist = array();
    }
}
?>