<?php

class SeedTable{


	/**
	*	seeds the table
	*	@param string
	*/
	public $table;

	/**
	*	User inputs
	*	@param array
	*/
	public $params;

	/**
	*	User inputs
	*	@param array
	*/
	public $param;

	/**
	*	sets number of records seeded
	*	If false, defers to the params
	*	@param int
	*/
	public $records = false;

	/**
	*	If false, nullable fields will not be seeded when there is no params
	*	@param boolean
	*/
	public $seedNullable = false;

	/**
	*	The attributes of each column
	*	@param array
	*/
	private $columnData;

	/**
	*	stores all the seed data
	*	@param array
	*/
	private $seedData;

	/**
	*	the join on column
	*	@param string
	*/
	private $on;

	public function __construct(string $table){
		
		$this->table = $table;
	}

	/**
	*	param will seed the value across all rows
	*	this is a single dimension array
	*	@param array
	*/
	public function rule(array $rule){

		$this->rule = $rule;

		return $this;
	}

	/**
	*	on joins to a field
	*
	*	@param array
	*/
	public function on(array $on){

		$this->on = $on;

		return $this;
	}
	/**
	*	params will seed on an individual row
	*	This is therefore a 2d array
	*	@param array
	*/
	public function params($params = array()){

		$this->params = $params;

		return $this;
	}


	public function records(int $records){

		$this->records = $records;

		return $this;
	}

	public function to(string $column){

		$this->to = $column;

		return $this;
	}

	private function joinValue($value){

		$this->joinValue = $value;

	}

	/**
	*	0 - do not seed nullable columns
	*	1 - seed all nullable columns
	*	2 - randomly seed nullable columns
	*	@return void
	*/
	public function seedNullable(int $b){
		$this->seedNullable = $b;
	}


	public function getSeedData(){
		return $this->seedData;
	}


	private function getLastAutoIncrement(){

		foreach($this->columnData as $i => $row){
			if($row['EXTRA'] == "auto_increment"){

				$columnName = $row['COLUMN_NAME'];
				$table = $this->table;

				$sql = "SELECT max($columnName) as `maxId` FROM `$table`";
				$data = Seeder::get($sql);

				if(count($data) == 0){
					return 0;
				}else{
					return $data[0]['maxId'];
				}
				
			}
		}

		return false;
	}

	private function getTotalRecords(){

		if($this->records === false && $this->records == 0){
			die("You must set number of records to be seeded or params");
		}else{
			return $this->records === false ? count($this->params) : $this->records;
		}
	}

	/*private function toHelper($child, $column){
							
		switch(true){
			case ($column['EXTRA'] == "auto_increment"):

				$id  	= $autoId + $row +1;
				$data  	= $child->rule([$child->on=>$id])
								->seed()
								->getSeedData();
				$ret = $id;
				
			default:
				$data = $child->seed()->getSeedData();
				$ret  = $data[0][$child->on];
		}	

		return $ret;
	}*/


	private function seedMaker(){

		$autoId = $this->getLastAutoIncrement();

		$totalRows = $this->getTotalRecords();
		$seedData = [];
		for($row=0;$row<$totalRows;$row++){
			$rowData = [];
			foreach($this->columnData as $i => $column){

				$columnName = $column['COLUMN_NAME'];

				switch(true){

					case (isset($this->on[$columnName])):

						$child = $this->on[$columnName];

#file_put_contents("child_".$columnName.$r.".txt", json_encode($this,JSON_PRETTY_PRINT));
#echo "\n".__LINE__."--WTF mate??";
						//$rowData[$columnName] = $this->toHelper($child,$column);
						if ($column['EXTRA'] == "auto_increment"){

							$id  			= $autoId + $row +1;
							$childData  	= $child->rule([$child->to=>$id])
												->seed()
												->getSeedData();

							$rowData[$columnName] = $id;
#file_put_contents("child_".$columnName.$r.".txt", json_encode($childData,JSON_PRETTY_PRINT));									
						}else{
$r = rand(1,1000);
							//$childData = [0=>[$child->on]];
							$childData = $child->seed()->getSeedData();
#file_put_contents("child_".$columnName.$r.".txt", json_encode($childData,JSON_PRETTY_PRINT));	
							#$rowData[$columnName]  = $childData[0][$child->on];

							$index = $row * $i;

							//if(isset($this->seedData[$this->table][$index][$child->to])){
							//	$rowData[$columnName]  = $this->seedData[$child->table][$index][$child->to];
							//}else{

								//option to enforce all relationships ??? 

								//$rowData[$columnName]  = $this->seedData[$child->table][$index][$child->to];
								$rowData[$columnName] = "";
							//}
						}

						//$data[$columnName] = $childData;
						//$rowData[$columnName] = $childData;

						break;

					//Child -- Join values are inherited from the eldest sibling
					/*case (isset($this->to) && $columnName == $this->to):
						
						$index = $row * $i;

						#if(count($seedData)>0){
							if(isset($this->seedData[$this->table][$index][$child->to])){
								$rowData[$columnName]  = $this->seedData[$this->table][$index][$child->to];
							}else{

								//option to enforce all relationships ??? 

								$rowData[$columnName]  = $this->seedData[$this->table][$index][$child->to];
							}
						#}else{
						#	$rowData[$columnName] = Randomizer::randomize($column);
						#}

						break;*/


					case isset($this->rule[$columnName]):
						$rowData[$columnName] = $this->rule[$columnName];
						break;


					case isset($this->params[$row][$columnName]):

						$rowData[$columnName] = $this->params[$row][$columnName];
						break;


					case ($column['EXTRA'] == "auto_increment"):

						$rowData[$columnName] = $autoId + $row +1;
						break;






					case ($column['COLUMN_KEY'] == "PRI" || $column['COLUMN_KEY'] == "UNI"):
						// everthing must be unique
						$rowData[$columnName] = Randomizer::randomizeUnique($seedData,$column);
						break;

					case (		($column['IS_NULLABLE'] == "YES" && $this->seedNullable === true) 
							||	($column['IS_NULLABLE'] == "NO")):
							
						$rowData[$columnName] = Randomizer::randomize($column);
						break;
					default:
						$rowData[$columnName] = null;
				}
			}

			array_push($seedData,$rowData);
		}

		$this->seedData[$this->table] = $seedData;
	}


	public function queryBuilder(){

#echo "\n".__LINE__."<--table-->".$this->table."<--columnData-->".json_encode($this->columnData,JSON_PRETTY_PRINT);
#echo "\n".__LINE__."--seedData-->".json_encode($this->seedData['user'],JSON_PRETTY_PRINT);
		$q = "INSERT INTO `{$this->table}` ";
		foreach($this->seedData[$this->table] as $i => $row){
			foreach($this->columnData as $j => $column){


				$columnName = $column['COLUMN_NAME'];

				// Columns
				if($i == 0){
					if($j == 0){	$qColumnNames= "`$columnName`";
					}else{			$qColumnNames.= ", `$columnName`";	}
				}

				//Column Values
				if($j == 0){	$qValues = "'".addslashes($row[$columnName])."'";
				}else{			$qValues.= " ,'".addslashes($row[$columnName])."'";}					
			}

			if($i == 0 ){
				$q.= "\n($qColumnNames) ";
				$q.= "\nVALUES ($qValues) ";
			}else{
				$q.= "\n,($qValues) ";
			}

		}

		return $q;
	}

	public function seed(){

		$this->columnData = Seeder::getColumnInfo($this->table);
#file_put_contents($this->table.".txt", json_encode($this->columnData,JSON_PRETTY_PRINT));
		$this->seedMaker();

		$q = $this->queryBuilder();

		Seeder::post($q);

		return $this;
	}

}