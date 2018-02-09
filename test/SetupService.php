<?php

namespace test;

class SetupService{

	private static function parseCredentials($string){
		$e = explode("\n",$string);

		$credentials = ["host"=>null,"user"=>null,"password"=>null,"database"=>null];
		foreach($e as $i => $line){
			if(strlen($line)>0){
				$ee = explode("=",$line);
				$key = $ee[0];
				$value = $ee[1];
				$credentials[$key] = $value;
			}
		}

		return $credentials;
	}


	public static function getCredentials($env = null){

		switch($env){

			default:
				return self::parseCredentials(file_get_contents(__DIR__."/.credentials"));		
		}
	}


	public static function wipeDatabase(){
		
		$c = App\Seeder::setConnection(self::$creds['host'], self::$creds['user'],self::$creds['pass'],self::$creds['database']);
		$q = "Show tables";

		$tables = $c->query($q);

		foreach($tables as $i => $table){
			$q = "Drop table $table";
			$tables = $c->query($q);
		}
	}


}