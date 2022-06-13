<?php

namespace App;

use PDO;
use PDOException;

class PDODebug   extends PDO {


    public function exec ($statement)
    {
        var_dump ($statement); 
        return (parent::exec ($statement));
    }
    public function query ($statement)
    {
        var_dump ($statement); 
        return (parent::query ($statement));
    }

  
}
