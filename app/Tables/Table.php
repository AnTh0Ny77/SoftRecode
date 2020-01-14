<?php

namespace App\Tables;

use App\Database;

class Table{
    private Database $Db;

    public function __construct($db){
       $this->Db = $db;
    }
}