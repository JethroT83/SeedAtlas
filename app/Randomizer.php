<?php

namespace app;


class Randomizer{


	public static function randomize($column){

		$dataType 		= $column['DATA_TYPE'];
		$length 		= $column['CHARACTER_MAXIMUM_LENGTH'];
		$numPrec 		= $column['NUMERIC_PRECISION'];
		$numScale 		= $column['NUMERIC_SCALE'];

		$numberTypes = ["tinyint","smallint","mediumint","int","bigint","float","double","decimal"];
		$textTypes = ["binary","char","varchar","longtext","text"];
		$dateTypes = ["date","datetime","timestamp","time","year"];
		//$blobTypes = ["tinyblob","blob","mediumblob","longblob"];

		switch(true){

			case in_array($dataType,$numberTypes):
				return rand(0,pow(10,$numPrec-1)) + rand(0,pow(10,$numScale))/pow(10,$numScale);

			case in_array($dataType,$textTypes):
				return self::randomizeString($length);

			case in_array($dataType,$dateTypes):
				return self::randomDate($dataType);

			//case in_array($dataType,$blobTypes):
				// not necessary for now
		}
	}


	private static function checkUnique($seedData,$columnName,$randData){

		foreach($seedData as $i => $info){
			if(isset($info[$columnName]) && $randData == $info[$columnName]){
				return false;
			}
		}

		return true;
	}


	public static function randomizeUnique($seedData, $column){

		$columnName = $column['COLUMN_NAME'];

		$randData = self::randomize($column);
		if(self::checkUnique($seedData,$columnName,$randData) === false){
			return self::randomizeUnique($seedData, $column);
		}else{
			return $randData;
		}
	}


	private static function randomizeString($length){

		$chars = "abcde fghij klmno pqrst uvwxyz ABCDE FGHIJ KLMNO PQRST UVWXYZ 12345 67890";

		$x = trim(ceil($length/strlen($chars)));

		$str = "";
		for($a=1;$a<=$x;$a++){$str.=$chars;}

		$randLength = rand(1,$length);

		return substr(str_shuffle($str),0,$randLength);

	}

	private static function randomizeDate(string $dataType){

		$now = strtotime(date("Y-m-d H:i:s"));
		$randTime = rand(0,$now);

		switch($dataType){

			case "date":
				return date("YYYY-mm-dd",$randTime);

			case ("datetime" || "timestamp"):
				return date("Y-m-d H:i:s", $randTime);

			case "time":
				return date("H:i:s", $randTime);

			case "year":
				return date("Y", $randTime);
		}
	}
}
