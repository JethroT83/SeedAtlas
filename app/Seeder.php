<?php

class Seeder{

	/**
	*	connection is linked to a static binding
	*	@static
	*/
	public static $connection;

	/**
	*	sets table name
	*	works just like mysql database.table
	*	@static table
	*/
	public static $table;


	public static function setConnection($host,$user, $password, $db){
		
		self::$connection = mysqli_connect($host,$user,$password,$db);
		
		// Check connection
		if (mysqli_connect_errno()){
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
	}

	public static function get($sql){
		$r = self::$connection->query($sql);
		return  $r->fetch_all(MYSQLI_ASSOC);
	}

	public static function getColumnInfo($table){

		$sql = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, 
						NUMERIC_SCALE, IS_NULLABLE, COLUMN_KEY, EXTRA
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_NAME = '$table'";

		$r = self::$connection->query($sql);
		$result =  $r->fetch_all(MYSQLI_ASSOC);

		if(count($result) == 0){
			die($table ." doesn't exist in the database.\n");
		}else{
			return $result;
		}
	}

	public static function post($query){
		return self::$connection->query($query);
	}


	public static function Table(string $table){

		$seedTable =  new SeedTable($table);

		return $seedTable;
	}


}