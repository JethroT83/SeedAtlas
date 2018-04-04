<?php


use Phinx\Migration\AbstractMigration;

class SeederTestMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user');
        $table
            ->addColumn('first_name', 'string', array('limit' => 100))
            ->addColumn('last_name', 'string', array('limit' => 100))
            ->addColumn('job_title', 'string', array('null' => true, 'limit' => 100))
            ->addColumn('salary', 'decimal', array('null' => true))
            ->addColumn('notes', 'text', array('null' => true))
            ->addColumn('updated', 'datetime', array('null' => true))
            ->create();

        $table = $this->table('post');
        $table
            ->addColumn('user_id', 'integer', array('limit' => 11))
            ->addColumn('post', 'text', ['limit' => 'TEXT_LONG'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }


    public function up(){
        $table = $this->table('user');
        $table
            ->addColumn('first_name', 'string', array('limit' => 100))
            ->addColumn('last_name', 'string', array('limit' => 100))
            ->addColumn('job_title', 'string', array('null' => true, 'limit' => 100))
            ->addColumn('salary', 'decimal', array('null' => true))
            ->addColumn('notes', 'text', array('null' => true))
            ->addColumn('updated', 'datetime', array('null' => true))
            ->create();

        $table = $this->table('post');
        $table
            ->addColumn('user_id', 'integer', array('limit' => 11))
            ->addColumn('post', 'text', ['limit' => 'TEXT_LONG'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
