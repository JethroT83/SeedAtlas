<?php
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;
use test\SetupService;
use Phinx\Wrapper\TextWrapper;


class SeederTest extends TestCase
{
    private static $creds = ["host"=>null,"user"=>null,"password"=>null,"database"=>null];
    private static $T;

    public function setUp()
    {
        
        self::$creds = SetupService::getCredentials();

        /*$configArray    = Yaml::parseFile(__DIR__."/../../phinx.yml");
        $config         = new Config($configArray);
        $P              = new Manager($config,new StringInput('migrate'), new NullOutput());
        #$P->setEnvironments(['testing']);
        $P->migrate('testing');
        echo "\n\n---output-->".$P->printStatus('testing')."--\n\n";*/

        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput(' '), new NullOutput());

        self::$T = new TextWrapper($app);
        self::$T->getMigrate("testing");
    }

    public function tearDown(){
    	self::$T->getRollback("testing");
    }


    public function testSeeder(){

    	Seeder::setConnection(self::$creds['host'], self::$creds['user'],self::$creds['pass'],self::$creds['database']);

		$data =  Seeder::Table("user")
					->params([
						["first_name"=>"John Doe"]
					])->records(2)
					->seed()
					->getSeedData();

		$this->assertEquals(2, count($data), "There should be two records seeded.");
		$this->assertEquals("John Doe", $data[0]['first_name'],"The first record first name should match the params.");

    }
}