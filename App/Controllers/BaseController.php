<?php

namespace App\Controllers;

use App\Core\EntityManager;

abstract class BaseController
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    abstract public function listAction();
    abstract public function viewAction();

}