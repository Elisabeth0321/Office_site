<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['signed' => false])
            ->addColumn('firstname', 'string', ['limit' => 255])
            ->addColumn('lastname', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('salt', 'string', ['limit' => 255])
            ->addColumn('token', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('time_last_login', 'datetime', ['null' => true])
            ->addColumn('is_verified', 'boolean', ['default' => false])
            ->addForeignKey('id', 'employees', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
