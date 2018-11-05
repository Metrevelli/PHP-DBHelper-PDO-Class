<?php
class dbHelp{
	private $connect;
	function __construct(){
		try {
			$this->connect = new PDO('mysql:host=localhost;dbname=dbName;charset=utf8', 'root', 'password');
			$this->connect->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die("Oops something went wrong!");
		}
	}
	function insert($table,$array){
		if(!empty($table) && !empty($array)){
			$keys = '';
			$keysForValues = '';
			$insertKeyValueArray = array();
			foreach($array as $key => $value){
	            $keys .= $key.",";
	            $keysForValues .= ':'.$key.",";
	            $insertKeyValueArray[":".$key] = $value;
        	}
            $keys = rtrim($keys, ',');
            $keysForValues = rtrim($keysForValues, ',');
			$stmt = $this->connect->prepare("INSERT INTO $table ($keys) VALUES ($keysForValues)");
			return $stmt->execute($insertKeyValueArray) ? $this->connect->lastInsertId('questionID') : FALSE;
		}
	}
	function select($what = "*",$table,$fields = []){
		if(!empty($table)){
			$selectKeyValueArray = array();
			$keysForWhere = '';
			foreach($fields as $key => $value){
				$selectKeyValueArray[":".$key] = $value;
			}
			$where = $this->getWhere($fields);
			$stmt = $this->connect->prepare("SELECT $what FROM $table $where");
			$stmt->execute($selectKeyValueArray);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		}
	}
	function update($table,$array,$fields = []){
		$keys = '';
		$keysForValues = '';
		$insertKeyValueArray = array();
		foreach($array as $key => $value){
            $keys .= $key . "=:".$key .',';
            $insertKeyValueArray[":".$key] = $value;
    	}
    	$keys = rtrim($keys, ', ');
		$where = $this->getWhere($fields,true);
		$stmt = $this->connect->prepare("UPDATE $table set $keys $where");
		$stmt->execute($insertKeyValueArray);
		$updatedRows = $stmt->rowCount();
		return $updatedRows;
	}
	function getWhere($array,$forUpdate = false){

        $where = !empty($array) ? ' WHERE' : '';
        foreach($array as $key => $value){
        	if($forUpdate){
				$where .= ' '.$key.' ='.$value.' AND';
        	}else{
            	$where .= ' '.$key.' =:'.$key.' AND';
        	}
        }
        $where = rtrim($where, 'AND');
        return $where;
    }
}
?>