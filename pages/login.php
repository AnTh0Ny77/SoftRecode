<?php
 require "./vendor/autoload.php";
 use App\twigloader;
 use App\Database;
 use App\Tables\Table;
 use App\Tables\Users;
 
 $Database = new App\Database('devisrecode');
 $Database->DbConnect();
 
 $Users = new App\Tables\Users($Database,'utilisateur');

 
 var_dump( $Users->login("AB","louane77"));


?>

<h1>login Works</h1>
