<?php

use Phinx\Migration\AbstractMigration;

class CreateEmployeesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('employees', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['signed' => false, 'identity' => true])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('salary', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
            ->addColumn('position', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('department_id', 'integer')
            ->addForeignKey('department_id', 'departments', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
