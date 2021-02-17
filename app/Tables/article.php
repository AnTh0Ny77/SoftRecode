<?php

namespace App\Tables;
use App\Tables\Table;
use App\Database;
use PDO;
use stdClass;

/*.                    .    o8o            oooo            
     .888.                 .o8    `"'            `888            
    .8"888.     oooo d8b .o888oo oooo   .ooooo.   888   .ooooo.  
   .8' `888.    `888""8P   888   `888  d88' `"Y8  888  d88' `88b 
  .88ooo8888.    888       888    888  888        888  888ooo888 
 .8'     `888.   888       888 .  888  888   .o8  888  888    .o 
o88o     o8888o d888b      "888" o888o `Y8bod8P' o888o `Y8bod8*/
 

class Article extends Table 
{
  public string $Table;
  public Database $Db;
  private object $Request;

  public function __construct($db) 
  {
    $this->Db = $db;
  }

  public function getAll() 
  { /* ancienne table article2 */
    $request =$this->Db->Pdo->query('SELECT DISTINCT art_type  FROM articles2 ORDER BY  art_type ASC');
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function getART($art_filtre=FALSE)
  { /* nouvelle tables article (composé de art_fmm, art_pn, art_marque) */
    $SQL_WHERE = $SQL_GROUPBY = $SQL_ORDER = '';
    // order par defaut si pas de filtre voir les X dernières créations
    $SQL_ORDER = 'ORDER BY art_pn.apn__date_modif DESC ';
    if ($art_filtre)
    {
      $first_digit = substr($art_filtre,0,1);
      $art_filtre_special = trim(substr($art_filtre,1));
      $SQL_ORDER = 'ORDER BY keyword.kw__ordre ASC, art_fmm.afmm__modele ASC, art_pn.apn__pn ASC ';
      switch ($first_digit) 
      {
        case ",": // Famille
          $SQL_WHERE .= 'WHERE ( keyword.kw__lib     like(\''.$art_filtre_special.'\') ) ';
          break;
        case ";": // Marque
          $SQL_WHERE .= 'WHERE ( art_marque.am__marque like(\''.$art_filtre_special.'\') ) ';
          break;
        case ":": // Modele
          $SQL_WHERE .= 'WHERE ( art_fmm.afmm__modele  like(\''.$art_filtre_special.'\') ) ';
          break;
        case "!": // PN (recherche sur le PN court)
          $art_filtre_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $art_filtre_special); // pour avoir le PN court (que du alpha et nombre)
          $SQL_WHERE .= 'WHERE ( art_pn.apn__pn        like(\''.$art_filtre_court.'\') ) ';
          break;
        default:
          // decoupage du filtre par mots
          $x_filtre       = trim($art_filtre);
          $x_filtre       = str_replace("-", " ", $x_filtre); // le - est un char special et donc non vue dans str-word-count
          $nb_mots_filtre = str_word_count($x_filtre, 0, '0123456789'); // Nombre de mots 
          $mots_filtre    = str_word_count($x_filtre, 1, '0123456789'); // renvoie un tableau - le 3eme paramettre est pour prendre en compte les chiffre comme mot et non comme separateur
          $opperateur     = 'AND ';
          if ($nb_mots_filtre > 0) $mode_filtre = TRUE; else $mode_filtre = FALSE; 
          // Construction du WHERE
          if ($mode_filtre) 
          { // boucle avec les # mots et le bon opperateur Booleen
            $SQL_WHERE .= 'WHERE ';
            $SQL_WHERE .= '( ';
            for ($i = 0; $i < $nb_mots_filtre; $i++) 
            {
              $SQL_WHERE .= '( ';
              $SQL_WHERE .= 'art_fmm.afmm__modele     like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'art_marque.am__marque    like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'art_pn.apn__pn           like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'keyword.kw__lib        like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'art_pn.apn__desc_short   like(\'%'.$mots_filtre[$i].'%\') ';
              $SQL_WHERE .= ') '.$opperateur;
            }
            $SQL_WHERE = substr($SQL_WHERE,0,-1*strlen($opperateur)); // supprimer le dernier opperateur.
            $SQL_WHERE .= ') ';
          }
      }
    }
    $SQL = 'SELECT 
    keyword.kw__lib as Famille, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele, 
    art_pn.apn__pn_long as PN, art_pn.apn__desc_short as Info, 
    art_pn.apn__id_user_modif as ID_User_Modif, art_pn.apn__date_modif as Date_Modif,
    art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc,
    art_pn.apn__image as PN_Image, 	art_pn.apn__doc as PN_Doc
    FROM art_pn
    INNER JOIN art_fmm ON art_pn.apn__afmm__id = art_fmm.afmm__id
    INNER JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id
    INNER JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' '.
    $SQL_WHERE.$SQL_ORDER; // LIMIT 0,50
    //print $SQL; // pour debug
    $request =$this->Db->Pdo->query($SQL);
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    $TA = array();
    $i = 0;
     foreach($data as $row => $ligne) 
    {
      // je cherche la bonne image en priorité le PN si non le MODELE
      $Image = '';
      if ($ligne->FMM_Image) $Image = 'Modele_Image/'.$ligne->FMM_Image;
      if ($ligne->PN_Image)  $Image = 'PN_Image/'.$ligne->PN_Image;
      if ($Image) $Image = '<img src="public/_Documents_/'.$Image.'" class=" ml-5 my-2" height="55">';
      // je cherche la doc en priorité le PN si non le MODELE
      $Doc = '';
      if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
      if ($ligne->PN_Doc ) $Doc = 'PN_Doc/'.$ligne->PN_Doc;
      if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
      $TA[] = array (
        'Famille'     => $ligne->Famille,
        'Marque'      => $ligne->Marque,
        'Modele'      => $ligne->Modele,
        'PN'          => $ligne->PN,
        'Info'        => $ligne->Info,
        'ID_User_Modif' => $ligne->ID_User_Modif,
        'Date_Modif'  => $ligne->Date_Modif,
        'Image'       => $Image,
        'Doc'         => $Doc
      );
      $i++;
    }
    //exit;
    return $TA;
  }

  public function getMODELE($filtre=FALSE)
  { /* nouvelle tables article (composé de art_fmm, art_marque) */
    $SQL_WHERE = $SQL_GROUPBY = $SQL_ORDER = '';
    // order par defaut si pas de filtre voir les X dernières créations
    $SQL_ORDER = 'ORDER BY art_fmm.afmm__id DESC ';
    if ($filtre)
    {
      $first_digit = substr($filtre,0,1);
      $filtre_special = trim(substr($filtre,1));
      $SQL_ORDER = 'ORDER BY keyword.kw__ordre ASC, art_fmm.afmm__modele ASC ';
      switch ($first_digit) 
      {
        case ",": // Famille
          $SQL_WHERE .= 'WHERE ( keyword.kw__lib     like(\''.$filtre_special.'\') ) ';
          break;
        case ";": // Marque
          $SQL_WHERE .= 'WHERE ( art_marque.am__marque like(\''.$filtre_special.'\') ) ';
          break;
        case ":": // Modele
          $SQL_WHERE .= 'WHERE ( art_fmm.afmm__modele  like(\''.$filtre_special.'\') ) ';
          break;
        default:
          // decoupage du filtre par mots
          $x_filtre       = trim($filtre);
          $x_filtre       = str_replace("-", " ", $x_filtre); // le - est un char special et donc non vue dans str-word-count
          $nb_mots_filtre = str_word_count($x_filtre, 0, '0123456789'); // Nombre de mots 
          $mots_filtre    = str_word_count($x_filtre, 1, '0123456789'); // renvoie un tableau - le 3eme paramettre est pour prendre en compte les chiffre comme mot et non comme separateur
          $opperateur     = 'AND ';
          if ($nb_mots_filtre > 0) $mode_filtre = TRUE; else $mode_filtre = FALSE; 
          // Construction du WHERE
          if ($mode_filtre) 
          { // boucle avec les # mots et le bon opperateur Booleen
            $SQL_WHERE .= 'WHERE ';
            $SQL_WHERE .= '( ';
            for ($i = 0; $i < $nb_mots_filtre; $i++) 
            {
              $SQL_WHERE .= '( ';
              $SQL_WHERE .= 'art_fmm.afmm__modele     like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'art_marque.am__marque    like(\'%'.$mots_filtre[$i].'%\') OR ';
              $SQL_WHERE .= 'keyword.kw__lib        like(\'%'.$mots_filtre[$i].'%\') ';
              $SQL_WHERE .= ') '.$opperateur;
            }
            $SQL_WHERE = substr($SQL_WHERE,0,-1*strlen($opperateur)); // supprimer le dernier opperateur.
            $SQL_WHERE .= ') ';
          }
      }
    }
    $SQL = 'SELECT 
    keyword.kw__lib as Famille, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele, 
    art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc, art_fmm.afmm__id as FMM_ID, art_fmm.afmm__design_com as Descom,
    art_fmm.afmm__id
    FROM art_fmm
    INNER JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id
    INNER JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' '.
    $SQL_WHERE.$SQL_ORDER; // 'LIMIT 0,50 ';
    //print $SQL; // pour debug
    $request =$this->Db->Pdo->query($SQL);
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    $TM = array();
    $i = 0;
     foreach($data as $row => $ligne) 
    {
      // je cherche l image
      $Image = '';
      if ($ligne->FMM_Image) $Image = base64_encode($ligne->FMM_Image); // pour image en base (codé sur un BLOB)
      // je cherche la doc 
      $Doc = '';
      if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
      if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
      // creation du tableau a renvoyer 
      $TM[] = array (
        'Famille'     => $ligne->Famille,
        'Marque'      => $ligne->Marque,
        'Modele'      => $ligne->Modele,
        'Descom'      => $ligne->Descom,
        'Image'       => $Image,
        'Doc'         => $Doc,
        'ID'          => $ligne->afmm__id
      );
      $i++;
    }
    //exit;
    return $TM;
  }

  public function getPARTS($art_filtre, $art_modele=FALSE)
  { /* Liste dans Art_Parts les PN en lien avec LE filtre (Modele ou PN) */
    // cet fonction n'est appelé que si il y a un filtre Modele ou PN
    $first_digit = substr($art_filtre,0,1);
    $art_filtre_special = trim(substr($art_filtre,1));
    $art_filtre_court = preg_replace("#[^!A-Za-z0-9_%]+#", "", $art_filtre_special); // pour avoir le PN court (que du alpha et nombre)
    $SQL_WHERE = 'WHERE ( FALSE ) ';
    if($first_digit == "!") // PN (recherche sur le PN court) et sur modele
    {
      $SQL_WHERE  = 'WHERE ( art_parts.apa__pa2_modele like(\''.$art_modele.'\') OR ';
      $SQL_WHERE .= 'art_parts.apa__pa2_pn like(\''.$art_filtre_court.'\') ) ';
    }
    if($first_digit == ":") // MODELE (recherche sur le Modele)
    {
      $SQL_WHERE = 'WHERE ( art_parts.apa__pa2_modele like(\''.$art_filtre_special.'\') ) '; // recherche au mini sur le Modele
    }

    $SQL = 'SELECT 
    keyword.kw__lib as Famille, art_fmm.afmm__famille as Famille_Key, art_marque.am__marque as Marque, art_fmm.afmm__modele as Modele,
    art_pn.apn__pn_long as PN, art_pn.apn__desc_short as Info, art_pn.apn__id_user_modif as ID_User_Modif,
    art_pn.apn__date_modif as Date_Modif, art_fmm.afmm__image as FMM_Image, art_fmm.afmm__doc as FMM_Doc,
    art_pn.apn__image as PN_Image, art_pn.apn__doc as PN_Doc, art_parts.apa__pa2_info as Parts_Info
    FROM art_parts
    INNER JOIN art_pn ON art_parts.apa__pn = art_pn.apn__pn
    INNER JOIN art_fmm ON art_pn.apn__afmm__id = art_fmm.afmm__id
    INNER JOIN art_marque ON art_fmm.afmm__marque = art_marque.am__id
    INNER JOIN keyword ON art_fmm.afmm__famille = keyword.kw__value and keyword.kw__type = \'famil\' '.
    $SQL_WHERE.
    'ORDER BY keyword.kw__ordre ASC, art_pn.apn__pn ASC '; // limit 50

    // print $SQL; // pour debug
    $request =$this->Db->Pdo->query($SQL);
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    $TA_ACC = $TA_CON = $TA_PID = array(); // 3 tableaux pour accessoire, consommable et pieces
    $i = 0;
    // il peut y avoir des doublons dans le resultat de la requette.
    // pas possible de trouver une requette qui ne donne pas de doublon ??? Distinc tou group by ne fonctone pas  ???
    // la bonne nouvelle c'est que c'est trié et donc je supprime le doublon si vois passer un double ...
    $old_pn = '';
    foreach($data as $row => $ligne) 
    {
      if ($old_pn <> $ligne->PN) // pour eviter les doublons...
      { // je cherche la bonne image en priorité le PN si non le MODELE
        $Image = '';
        if ($ligne->FMM_Image) $Image = 'Modele_Image/'.$ligne->FMM_Image;
        if ($ligne->PN_Image ) $Image = 'PN_Image/'.$ligne->PN_Image;
        if ($Image) $Image = '<img src="public/_Documents_/'.$Image.'" class="mx-0 my-0" height="55">';
        $Doc = '';
        if ($ligne->FMM_Doc) $Doc = 'Modele_Doc/'.$ligne->FMM_Doc;
        if ($ligne->PN_Doc ) $Doc = 'PN_Doc/'.$ligne->PN_Doc;
        if ($Doc) $Doc = 'public/_Documents_/'.$Doc;
        $temp = array (
          'Famille'     => $ligne->Famille,
          'Marque'      => $ligne->Marque,
          'Modele'      => $ligne->Modele,
          'PN'          => $ligne->PN,
          'Info'        => $ligne->Info,
          'ID_User_Modif' => $ligne->ID_User_Modif,
          'Date_Modif'  => $ligne->Date_Modif,
          'Image'       => $Image,
          'Doc'         => $Doc,
          'Parts_Info'  => $ligne->Parts_Info
        );
        switch ($ligne->Famille_Key) 
        { 
          case "ACC": // Accessoire option
            $TA_ACC[] = $temp;
            break;
          case "CON": // Accessoire option
            $TA_CON[] = $temp;
            break;
          case "PID": // Accessoire option
            $TA_PID[] = $temp;
            break;
        }
        $i++;
        $old_pn = $ligne->PN;
      }
    }
    //exit;
    return array('ACC'=>$TA_ACC, 'CON'=>$TA_CON, 'PID'=>$TA_PID);
  }

  public function getFAMILLE()
  { /* Liste des famille dans keyword.famil */
    $SQL = 'SELECT kw__value, kw__lib, kw__lib_uk, kw__info
    FROM keyword WHERE kw__type = \'famil\' ORDER BY kw__ordre, kw__lib';
    $request =$this->Db->Pdo->query($SQL);
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  public function getMARQUE()
  { /* Liste des Marques dans  table ART_MARQUE */
    $SQL = 'SELECT am__id, am__marque FROM art_marque WHERE am__actif = 1 ORDER BY am__ordre, am__marque';
    $request =$this->Db->Pdo->query($SQL);
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data;
  }

  /*""b8 88""Yb 888888    db    888888        db    88""Yb 888888            888888 8b    d8 8b    d8 
 dP   `" 88__dP 88__     dPYb     88         dPYb   88__dP   88              88__   88b  d88 88b  d88 
 Yb      88"Yb  88""    dP__Yb    88        dP__Yb  88"Yb    88              88""   88YbdP88 88YbdP88 
  YboodP 88  Yb 888888 dP""""Yb   88       dP""""Yb 88  Yb   88   oooooooooo 88     88 YY 88 88 YY 8*/ 
  public function create($famille, $marque, $modele, $image, $doc, $descom)
  {
    /* exemple : INSERT INTO art_fmm (afmm__famille, afmm__marque, afmm__modele) VALUES ('btm', '14', 'dddd') */
    $request = $this->Db->Pdo->prepare("
    INSERT INTO art_fmm (afmm__famille, afmm__marque, afmm__modele, afmm__image, afmm__doc, afmm__design_com) 
    VALUES              (:famille,      :marque,      :modele,      :image,      :doc,      :descom)"); 
    $request->bindValue(":famille", $famille);
    $request->bindValue(":marque",  $marque);
    $request->bindValue(":modele",  $modele);
    $request->bindValue(":image",   $image);
    $request->bindValue(":doc",     $doc);
    $request->bindValue(":descom",  $descom);
    $request->execute();
    $idFmm = $this->Db->Pdo->lastInsertId();
    return $idFmm;
  }

  public function getModels(){
    $request = $this->Db->Pdo->query(
    'SELECT afmm__id , afmm__modele, k.kw__lib as famille , m.am__marque as Marque
      FROM art_fmm
      INNER JOIN art_marque as m ON afmm__marque = m.am__id
      INNER JOIN keyword as k on afmm__famille = k.kw__value 
      order by k.kw__ordre ASC, afmm__modele ASC');
  
    $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data ; 
  }
  
  //recupère la désignation commerciale pour les suggestions aux commerciaux lors des devis : 
  public function get_article_devis(int  $id_fmm) : stdClass 
  {
    $request = $this->Db->Pdo->query(
    'SELECT afmm__id , afmm__design_com , afmm__image 
    FROM art_fmm
    WHERE afmm__id = '. $id_fmm.'');
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }

  //recupère la désignation commerciale pour les suggestions aux commerciaux lors des devis : 
  public function get_by_id(int  $id_fmm): stdClass
  {
    $request = $this->Db->Pdo->query(
    'SELECT * 
    FROM art_fmm
    WHERE afmm__id = ' . $id_fmm . ''
    );
    $data = $request->fetch(PDO::FETCH_OBJ);
    return $data;
  }
  
  
  public function getPn($id){
  $request = $this->Db->Pdo->query(
    'SELECT  apn__pn , apn__afmm__id  , apn__desc_short , apn__pn_long
     FROM art_pn
     WHERE apn__afmm__id = '.$id.' 
     ORDER BY apn__pn ASC ');
     $data = $request->fetchAll(PDO::FETCH_OBJ);
    return $data ; 
  }
  

}

?>
