<?php
class params
{
    
    public $isServer;
    public $ipserver;
    public $port;
    public $serverlist;

    public $hostdb;
    public $userdb;
    public $password;
    public $database;
    public $localworkernumbers;
    public $depth;
    public $usedb;
    private function getparam($fileparam)
    {
        $this->port=36004;//defalut local port
        if (file_exists($fileparam)) {
            $file = new SplFileObject($fileparam);
            // Loop until we reach the end of the file.
            while (!$file->eof()) {
                // Echo one line from the file.
                $line= $file->fgets();
                $pos=strpos($line,'=');
                $name= substr($line,0,$pos);                
                $value= substr($line,$pos+1) ;
                switch ($name)
                {
                    case "usedb":
                        $this->usedb=boolval(intval($value));
                        break;
                    case "isServer":
                        $this->isServer=boolval(intval($value));
                        // echo "isServer $value";
                        break;
                    case"ipserver":
                        $this->ipserver =$value;
                        // echo "ipserver $value";
                        break;
                    case"port":
                        $this->port=intval($value);
                        // echo "port $value";
                        break;   
                    case"depth":
                            $this->depth=intval($value);
                            // echo "port $value";
                        break;                      
                    case"serverlist":
                            $listaserver=explode(",", $value);
                            $k= count($listaserver);
                            $this->serverlist=array(count($listaserver));
                            for (  $i=0;$i<count ($listaserver);$i++)
                            {
                                $addrport=explode(":", $listaserver[$i]);

                                $this->serverlist[$i]=$addrport;
                            }
                            echo "serverlist $value";
                            break;
                            case "hostdb": 
                                 $this->hostdb= trim($value) ;
                                 
                                  break; 
                            case "userdb":
                                $this->userdb=trim($value); 
                            break; 
                            case "password":
                                $this->password=trim($value);
                                 break; 
                            case "database": 
                                $this->database=trim($value);
                                 break; 
                     case  "localworkernumbers":
                        $this->localworkernumbers=intval($value);
                        break;
                }

            }            
            // Unset the file to call __destruct(), closing the file handle.
            $file = null;
            return true;
        }
        else
        {
            return false;
        }
    }
    public function __construct($_fileparam )
    { 
        
        $this->isServer=true;
        $this->ipserver='127.0.0.1';
        $this->port=36001;
        $this->hostdb="hostdb";
        $this->userdb="userdb";
        $this->password="password";
        $this->database="database";
        $this->localworkernumbers=0;
         $context='';
       if (! $this->getparam($_fileparam))
       {
        echo "file paramters dispatcher.ini not found!";
       }
    }
   
}
?>