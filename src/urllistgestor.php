<?php
abstract class UrlListGestor
{
    
    // Force Extending class to define this method
    abstract public function push($href,$seen,$depth,$httpcode,$idworker);
    abstract public  function processedPageCount();
    abstract public  function foundedPageCount();
    abstract public  function getPageToProcess(&$depth);
     abstract public  function setseen($url,$isexternal, $isbroken,$depth,$httpcode,$idworker);
    abstract public  function IsSeen($url);
    abstract public  function getseenCount();
    abstract public  function allpagesseen();
    abstract public  function pushList(array $hreflist ,$seen ,$depth);
    abstract public  function getresult();

    // Common method
    
}
?>