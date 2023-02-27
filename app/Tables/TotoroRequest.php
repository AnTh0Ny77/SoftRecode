<?php

namespace App\Tables;
use App\Tables\Table;
use App\Totoro;
use PDO;

class TotoroRequest extends Table {

	public Totoro $Db;

	public function __construct($db) {
		$this->Db = $db;
    }

    public function get_sortie_sn($sn){
        $request = $this->Db->Pdo->query('SELECT max(out_datetime) as sortie FROM locator WHERE num_serie = "'.$sn.'"');
        $data = $request->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
}