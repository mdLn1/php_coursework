<?php class Connection {

    private $host = "mysql.cms.gre.ac.uk";
    private $database = "mdb_cp3526m";
    private $username = "cp3526m";
    private $password = "Peer21";

    private $link;
    private $result;
    public $sql;

    function __construct(){
            $this->link = new mysqli($this->host,$this->username,$this->password,$this->database);
        // Check connection
if ($this->link->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
            return $this->link; 
    }

    function query($sql){
            if (!empty($sql)){
                    $this->sql = $sql;
                    $this->result = $this->link->query($sql);
                    return $this->result;
            }else{
                    return false;
            }
    }

    function fetch($result=""){
            if (empty($result)){ $result = $this->result; }
            return dbx_fetch_row($result);
    }

    function __destruct(){
            dbx_close($this->link);
    }
}
?>