<?php
namespace App\Fusion\Contracts;

interface UserInterface
{
    public function isAdmin();

    public function isWarehouse();

    public function getRoleId();
}
