BASIC USAGE

<?php
	use SeedAtlas\App\Seeder;

	Seeder::setConnection($host,$user,$password,$database);

	$data = Seeder::Table("users")->records(2)->seed()->getSeedData();


?>


setConnection -- this library has to talk to the database.  This function sets a static binding that will call the database

Table -- sets the table that will get seeded

records -- the number of record seeded

seed -- seeds the records

getSeedData -- returns the seeded data


$data should return an array of all the values seeded in the database



ENTERING PARAMETERS
<?php
	use SeedAtlas\App\Seeder;

	Seeder::setConnection($host,$user,$password,$database);

	$data = App\Seeder::Table("ad_layout")
				->params([
					["user_id"=>1,
					"user_name"=>"John Doe"],
					["user_id"=>2,
					"user_name"=>"Jane Doe"],
				])->records(20)
				->seed()
				->getSeedData();

?>

params -- params is a 2 dimensional array.  The first dimension is a numeric index that represents each row.  The second dimension has an associative key will match a column name.  Simply match each column name and set it to the value you want.

records -- There will still be 20 records seeded, but the first two will reflec the information in the params.


ENTERING PARAMETERS WITH NO RECORDS
<?php
	use SeedAtlas\App\Seeder;

	Seeder::setConnection($host,$user,$password,$database);

	$data = App\Seeder::Table("ad_layout")
				->params([
					["user_id"=>1,
					"user_name"=>"John Doe"],
					["user_id"=>2,
					"user_name"=>"Jane Doe"],
				])->seed()
				->getSeedData();

?>

records and params -- when params are set and not records, there will only be as many records as set in the params.


ENTERING NO PARAMS AND NO RECORDS
<?php
	use SeedAtlas\App\Seeder;

	Seeder::setConnection($host,$user,$password,$database);

	$data = App\Seeder::Table("ad_layout")
				->seed()
				->getSeedData();

?>

This will cause the function to opt out.  You must have either records or params set.



NULLABLE FIELDS
<?php
	use SeedAtlas\App\Seeder;

	Seeder::setConnection($host,$user,$password,$database);

	$data = App\Seeder::Table("ad_layout")
				->params([
					["user_id"=>1,
					"user_name"=>"John Doe"],
					["user_id"=>2,
					"user_name"=>"Jane Doe"],
				])
				->seedNullable(true)
				->seed()
				->getSeedData();
?>

By default, this will not seed nullable fields.
Setting it to true will seed the nullable fields.