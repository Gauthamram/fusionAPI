<?php
namespace App\Fusion\Interfaces;

interface UserInterface
{
    public function isAdmin();

    public function isWarehouse();

    public function getRoleId();
}
