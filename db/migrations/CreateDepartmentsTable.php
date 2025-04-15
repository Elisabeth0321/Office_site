<?php

use Phinx\Migration\AbstractMigration;

class CreateDepartmentsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('departments')
            ->addColumn('name', 'string', ['limit' => 255])
            ->create();
    }
}
