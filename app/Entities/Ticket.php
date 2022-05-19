<?php
namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Database;
use stdClass;

Class Ticket{

    
    public $tk__id;

    public $tk__motif;

    public $tk__motif_id;

    public $tk__titre;

    public $tk__lu;

    public $tk__indic;

    public $tk__groupe;

    public $lines;

    public function __construct( stdClass $object){

        if ($object->tk__id != null){
            $this->tk__id = $object->tk__id;
        }else $this->tk__id = null;

        $this->tk__motif = $object->tk__motif;
        $this->tk__motif_id = $object->tk__motif_id;
        $this->tk__titre = $object->tk__titre;
        $this->tk__lu = $object->tk__lu;
        $this->tk__indic = $object->tk__indic;
        $this->tk__groupe = $object->tk__groupe;
    }

    public function validate(){

    }

    public function persist(Ticket $ticket) :self {

        if ($ticket->tk__id != null){
            //create
        }else{
            //update
        }
        return $ticket;
    }


    // public function get_line(){

    // }

}