<?php
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigCustomFunction extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('boTel', [$this, 'boTel'] )
        ];
    }


    public function boTel($tel){ 
        $tel = trim($tel);
        $botel = $tel;
        // suprime . [Space] , -
        $sep = array(" ", ".", ",", "-");
        $tel = str_replace($sep, "", $tel);
        $l = strlen($tel);      
        if ($l == 10)   
            $botel = substr($tel,0,2).".".substr($tel,2,2).".".substr($tel,4,2).".".substr($tel,6,2).".".substr($tel,8,2);
        return $botel; // indique la valeur à renvoyer 
    } 

    

  
}
