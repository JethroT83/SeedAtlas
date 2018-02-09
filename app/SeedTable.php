<?php

class SeedTable{


	/*
	*	seeds the table
	*	@param string
	*/
	public $table;

	/*
	*	User inputs
	*	@param array
	*/
	public $params;

	/*
	*	sets number of records seeded
	*	If false, defers to the params
	*	@param int
	*/
	public $records = false;

	/*
	*	If false, nullable fields will not be seeded when there is no params
	*	@boonlean
	*/
	public $seedNullable = false;

	/*
	*	The attributes of each column
	*	@param array
	*/
	private $columnData;

	/*
	*	stores all the seed data
	*	@param array
	*/
	private $seedData;

	public function __construct(string $table){
		
		$this->table = $table;
	}


	public function params($params = array()){

		$this->params = $params;

		return $this;
	}


	public function records(int $records){

		$this->records = $records;

		return $this;
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

		if($this->records === false && count($this->records) == 0){
			die("You must set number of records to be seeded or params");
		}else{
			return $this->records === false ? count($this->params) : $this->records;
		}
	}

	private function seedMaker(){

		$autoId = $this->getLastAutoIncrement();

		$totalRows = $this->getTotalRecords();

		$seedData = [];
		for($row=0;$row<$totalRows;$row++){
			$rowData = [];
			foreach($this->columnData as $i => $column){

				$columnName = $column['COLUMN_NAME'];

				switch(true){

					case isset($this->params[$row][$columnName]):					
						$rowData[$columnName] = $this->params[$row][$columnName];
						break;

					case ($column['EXTRA'] == "auto_increment"):
						$rowData[$columnName] = $autoId + $row;
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

		$this->seedData = $seedData;
	}


	public function queryBuilder(){


		$q = "INSERT INTO `{$this->table}` ";
		foreach($this->seedData as $i => $row){
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

		$this->seedMaker();

		$q = $this->queryBuilder();

		Seeder::post($q);

		return $this;
	}

}