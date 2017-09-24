<?php
namespace App\Fusion\Contracts;

interface RawSqlInterface
{
    public function query();

    public function filter($param = '');

    public function getSql();
}
