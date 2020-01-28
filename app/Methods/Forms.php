<?php

namespace App\Methods;


 class Forms {

    public static function mail($mail){
        $result = filter_var($mail, FILTER_VALIDATE_EMAIL);
        if ($result) {
            return $mail;
         }else { return $result;} 
    }

    public static function phone($phone){
        $result = preg_match( '+?\d{1,4}?[-.\s]?\(?\d{1,3}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}' , $phone );
        if ($result) {
           return $phone;
        }else { return $result;}
    }
    
    public static function societeValidate($forms){
            if (!empty($forms['societe']) && !empty($forms['cp']) &&  !empty($forms['ville'])){

                $societe = $forms['societe'];
                $adr1 =    $forms['adr1'];
                $adr2 =    $forms["adr2"];
                $cp =      $forms["cp"];
                $ville =   $forms['ville'];

            return array(

                "societe"=> $societe,
                'adr1'=> $adr1,
                'adr2'=> $adr2,
                'cp'=> $cp,
                'ville'=> $ville    
            );
            }
            else {
                return false ;
                header('location : nouveauDevis');
            }
    }

    

}