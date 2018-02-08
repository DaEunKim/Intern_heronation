<?php

class ClassDB 
{
	public function ClassDB(){}

	public function getConn(){
	   		$dbid = "root";       
    		$dbpw = "0505";
    		$dbname = "prototype";
    		$dbhost = "localhost";
   		$conn =mysqli_connect($dbhost, $dbid, $dbpw, $dbname);
		mysqli_set_charset($conn,'utf8');
   		return $conn;
   	}
}

?>
