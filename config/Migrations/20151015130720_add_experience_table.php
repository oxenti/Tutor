<?php
use Migrations\AbstractMigration;

class AddExperienceTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('experiences');
        $table
            ->addColumn('tutor_id', 'integer', [
                'limit' => 11
            ])
            // ->addForeignKey('tutor_id', 'tutors', 'id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
            ->addColumn('company', 'string', [
               'default' => null,
               'limit' => 45,
               'null' => false,
            ])
            ->addColumn('position', 'string', [
               'default' => null,
               'limit' => 45,
               'null' => false,
            ])
            ->addColumn('start', 'date', [
               'default' => null,
               'null' => false,
            ])
            ->addColumn('end', 'date', [
               'default' => null,
            ])
            ->addColumn('current', 'boolean', [
                'default' => false,
            ])
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
            ->addIndex(
                [
                    'tutor_id',
                ]
            )
            ->create();
            $table
            ->addForeignKey(
                'tutor_id',
                'tutors',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('experiences');
    }
}
