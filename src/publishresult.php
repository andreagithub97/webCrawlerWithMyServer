<?php
class publishresult
{
     
    //protected $_urllist=array();
    // protected $_session;
    protected $_starttime;
    protected $filehandle;
    public $csv;
    protected $filehandlecsv;
    public function __construct($filepath,$csvtoo   )
    {
        $this->filehandle= fopen($filepath.".html","w");
        $this->csv=$csvtoo;
        $this->ctablehead($this->filehandle);
        if ($this->csv)
        {
            $this->filehandlecsv= fopen($filepath.".csv","w");
            $this->csvhead($this->filehandlecsv);
        }
        //$this->_session=$session;
       // $this->_urllist=$urllist;
        //$this->_starttime=$objDateTime ;
    }
    public function writerowcsv( $url,$code,$external_link,$broken_link,$time)
    {
        $comma=";";//",";
        $ext= ($external_link==1)?1:0;
        $broken = ($broken_link==1)?1:0;
        fwrite($this->filehandlecsv,"$url$comma$code$comma$ext$comma$broken$comma$time\n");
    }
    public function close()
    {
        fclose($this->filehandle);
        if ($this->csv)
        {
            fclose($this->filehandlecsv);
        }
    }
    public function writerowhml( $url,$code,$external_link,$broken_link,$time)
    {
        $ext= ($external_link==1)?1:0;
        $broken = ($broken_link==1)?1:0;
        fwrite( $this->filehandle,"  <tr>\n");
        fwrite( $this->filehandle, "    <td> $url</td>\n");
        fwrite( $this->filehandle, "    <td> $code</td>\n");
        fwrite( $this->filehandle, "    <td> $ext</td>\n");
        fwrite( $this->filehandle, "    <td> $broken</td>\n");
        fwrite( $this->filehandle, "    <td> $time</td>\n");
        fwrite( $this->filehandle, "  </tr>\n");
    }
    private function rowhml(&$fileresult,$url,$code,$isext,$isbroken,$time)
    {
        fwrite( $fileresult,"  <tr>\n");
        fwrite( $fileresult, "    <td> $url</td>\n");
        fwrite( $fileresult, "    <td> $code</td>\n");
        fwrite( $fileresult, "    <td> $isext</td>\n");
        fwrite( $fileresult, "    <td> $isbroken</td>\n");
        fwrite( $fileresult, "    <td> $time</td>\n");
        fwrite( $fileresult, "  </tr>\n");
    }
    function ctablehead(&$fileresult)
    {
        fwrite( $fileresult,"<table>\n");	
        fwrite( $fileresult,"  <tr>\n");
        fwrite( $fileresult,"    <th>url        </th>\n");
        fwrite( $fileresult,"    <th>code</th>\n");
        fwrite( $fileresult,"    <th>isext</th>\n");
        fwrite( $fileresult,"    <th>isbroken</th>\n");
        fwrite( $fileresult,"    <th>time</th>\n");
        fwrite( $fileresult,"  </tr>\n");
  
    }
    function csvhead(&$fileresult)
    {
        $comma=";";//",";
        fwrite( $fileresult,"url$comma"."code$comma"."external_link$comma"."broken_link$comma"."time\n");
    }
     function ctable(&$fileresult,$data)
    {
        fwrite( $fileresult,"<table>\n");	
        fwrite( $fileresult,"  <tr>\n");
        fwrite( $fileresult,"    <th>url        </th>\n");
        fwrite( $fileresult,"    <th>code</th>\n");
        fwrite( $fileresult,"    <th>isext</th>\n");
        fwrite( $fileresult,"    <th>isbroken</th>\n");
        fwrite( $fileresult,"    <th>time</th>\n");
        fwrite( $fileresult,"  </tr>\n");
     foreach (  $data  as &$u)
     { 
          //rowh(array_column($u, 'url'),array_column($u, 'code'),array_column($u, 'isext'),array_column($u, 'isbroken'),array_column($u, 'time'));
          $this->rowhml($fileresult,$u['url'],$u['code'],$u['isext'],$u['isbroken'],$u['time']);			
     }
     echo "</table>                                ";
    }
    private function rowcsv($fileresult,$url,$code,$external_link,$broken_link,$time)
    {
        $ext= ($external_link==1)?1:0;
        $broken = ($broken_link==1)?1:0;
        fwrite( $fileresult,"$url,$code,$ext, $broken ,$time\n");
    }
    public function savetotablehtml($filepath)
    {
        $fileresult= fopen($filepath,"w");
        $this->ctable($fileresult,$this->_urllist);
        fclose($fileresult);
    }

    public function tocsv($filepath)
    {
        $comma=";";//",";
        $fileresult= fopen($filepath,"w");
        $objDateTime = $this->_starttime;
        fwrite( $fileresult,"url,code,external_link,broken_link,time\n");
        $objDateTime = $this->_starttime;
        foreach (  $this->_urllist  as &$u)
		{ 
			 $this->rowcsv($fileresult,$u['url'],$u['code'],$u['isext'],$u['isbroken'],$u['time']);			
		}
        fclose($fileresult);
    }

    public function save($filepath)
    {
    $fileresult= fopen($filepath,"w");
    $objDateTime = $this->_starttime;
    fwrite($fileresult,$objDateTime->format('c') ."\n");
    fwrite( $fileresult,print_r($this->_urllist, true));
    $objDateTime = new DateTime('NOW');
    fwrite($fileresult,$objDateTime->format('c') ."\n");
    fclose($fileresult); 
    }
}