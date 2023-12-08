<?php
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigCustomFunction extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('boTel', [$this, 'boTel'] ),
            new TwigFunction('ajouterZeros', [$this, 'ajouterZeros']),
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

    public function ajouterZeros($chaine) {
        $longueurChaine = strlen($chaine);
        
        if ($longueurChaine < 6) {
            $zerosManquants = 6 - $longueurChaine;
            $chaine = str_repeat('0', $zerosManquants) . $chaine;
        } elseif ($longueurChaine > 6) {
            $chaine = substr($chaine, 0, 6); // Si la chaîne est plus longue, on la tronque à 6 caractères
        }
    
        return $chaine;
    }

  
}
