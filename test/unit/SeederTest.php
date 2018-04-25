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


    /*public function testSeeder(){

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


    public function testJoin(){

        Seeder::setConnection(self::$creds['host'], self::$creds['user'],self::$creds['pass'],self::$creds['database']);

        $postTable = Seeder::Table("post")->to("user_id")->records(4);

        $data  = Seeder::Table("user")
                    ->on([
                        "id"=>$postTable
                    ])
                    ->records(1)
                    ->seed()
                    ->getSeedData();
    
       $q = "SELECT * 
                FROM user u
                INNER JOIN post p ON u.id = p.user_id";

        $r = Seeder::get($q);

        $this->assertEquals(4, count($r),"Should be four post records to one post user.");

    }*/

    public function testJoinOnJoin(){

        Seeder::setConnection(self::$creds['host'],self::$creds['user'],self::$creds['pass'],self::$creds['database']);

            $image = Seeder::Table("image");
            $post = Seeder::Table("post");
            $user = Seeder::Table("user");

            $data = $user->on([
                                "id"=> $post->to("user_id")
                                            ->on(["id"=> $image->to("post_id")
                                                                    ->records(4)
                                                 ])->records(4)
                            ])
                        ->records(2)
                        ->seed()
                        ->getSeedData();


           $q = "SELECT * 
                    FROM user u
                    INNER JOIN post p ON u.id = p.user_id
                    INNER JOIN image i ON p.id = i.post_id";

            $r = Seeder::get($q);

            $this->assertEquals(32, count($r),"Should be four post records to one post user.");

    }
}