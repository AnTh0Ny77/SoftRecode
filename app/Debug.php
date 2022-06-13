<?php

namespace App;

use PDO;
use PDOException;
use ReflectionClass;

class Debug {

    public function DumpMethod($class , $method_name){
        $class = new ReflectionClass($class);
        $methods = $class->getMethods();
        foreach($methods as $method){
                if ( $method->name == $method_name ) {
                    
                } 
        }
        
    }
  
}
