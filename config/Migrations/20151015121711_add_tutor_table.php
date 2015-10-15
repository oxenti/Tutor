<?php
use Migrations\AbstractMigration;

class AddTutorTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('tutors');
        $table
            ->addColumn('user_id', 'integer', [
                'limit' => 11
            ])
            ->addColumn('description', 'text', [
               'default' => null,
               'null' => false,
            ])
            ->addColumn('cpf', 'string', [
               'default' => null,
               'limit' => 11,
               'null' => false,
            ])
            ->addIndex(['cpf'], ['unique' => true])
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
            ])
            ->addColumn('created', 'datetime', [
               'default' => 'CURRENT_TIMESTAMP',
               'limit' => null,
               'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
               'default' => null,
               'limit' => null,
               'null' => true,
            ])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('tutors');
    }
}
