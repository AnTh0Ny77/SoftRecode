<?php

/*FFFFFFFFFFFFFFFFFFFF                                                             tttt            iiii                                                      
F::::::::::::::::::::F                                                          ttt:::t           i::::i                                                     
F::::::::::::::::::::F                                                          t:::::t            iiii                                                      
FF::::::FFFFFFFFF::::F                                                          t:::::t                                                                      
  F:::::F       FFFFFFooooooooooo   nnnn  nnnnnnnn        ccccccccccccccccttttttt:::::ttttttt    iiiiiii    ooooooooooo   nnnn  nnnnnnnn        ssssssssss   
  F:::::F           oo:::::::::::oo n:::nn::::::::nn    cc:::::::::::::::ct:::::::::::::::::t    i:::::i  oo:::::::::::oo n:::nn::::::::nn    ss::::::::::s  
  F::::::FFFFFFFFFFo:::::::::::::::on::::::::::::::nn  c:::::::::::::::::ct:::::::::::::::::t     i::::i o:::::::::::::::on::::::::::::::nn ss:::::::::::::s 
  F:::::::::::::::Fo:::::ooooo:::::onn:::::::::::::::nc:::::::cccccc:::::ctttttt:::::::tttttt     i::::i o:::::ooooo:::::onn:::::::::::::::ns::::::ssss:::::s
  F:::::::::::::::Fo::::o     o::::o  n:::::nnnn:::::nc::::::c     ccccccc      t:::::t           i::::i o::::o     o::::o  n:::::nnnn:::::n s:::::s  ssssss 
  F::::::FFFFFFFFFFo::::o     o::::o  n::::n    n::::nc:::::c                   t:::::t           i::::i o::::o     o::::o  n::::n    n::::n   s::::::s      
  F:::::F          o::::o     o::::o  n::::n    n::::nc:::::c                   t:::::t           i::::i o::::o     o::::o  n::::n    n::::n      s::::::s   
  F:::::F          o::::o     o::::o  n::::n    n::::nc::::::c     ccccccc      t:::::t    tttttt i::::i o::::o     o::::o  n::::n    n::::nssssss   s:::::s 
FF:::::::FF        o:::::ooooo:::::o  n::::n    n::::nc:::::::cccccc:::::c      t::::::tttt:::::ti::::::io:::::ooooo:::::o  n::::n    n::::ns:::::ssss::::::s
F::::::::FF        o:::::::::::::::o  n::::n    n::::n c:::::::::::::::::c      tt::::::::::::::ti::::::io:::::::::::::::o  n::::n    n::::ns::::::::::::::s 
F::::::::FF         oo:::::::::::oo   n::::n    n::::n  cc:::::::::::::::c        tt:::::::::::tti::::::i oo:::::::::::oo   n::::n    n::::n s:::::::::::ss  
FFFFFFFFFFF           ooooooooooo     nnnnnn    nnnnnn    cccccccccccccccc          ttttttttttt  iiiiiiii   ooooooooooo     nnnnnn    nnnnnn  ssssssssss*/    

	// retourne le nb de secondes avec virgule en microsecondes
	function microtime_float() 
	{     
	  list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	} 

	// retourne le nb de secondes avec virgule entre time_start et maintenant
	function time_page($time_start) 
	{
		$time_end = microtime_float();
		$time = $time_end - $time_start;
		return substr($time,0,6);
	}

	// Transformation de format de datetime type sql format AAAA-MM-JJ HH:mm:ss
	// en format plus simple pour affichage Absent
	function dt2dts($dt, $option=false) 
	{ 
		$aaaa   = substr($dt, 0,4);
		$n_mois = substr($dt, 5,2);
		$n_jour = substr($dt, 8,2);
		$heure  = substr($dt,11,2);
		$minut  = substr($dt,14,2);
		$d_now  = date("Y-m-d"); 	
		$d_hier = date("Y-m-d", time() - 86400); // 86400 c'est 24*60*60 h*m*s
		$d_dt   = substr($dt, 0,10);
		$txt_j  = "le ";
		$txt_h  = "à ";
		$dts    = false; 
		if ($d_now == $d_dt) // si c'est le meme jour je ne tien compte que de l'heure
			$dts = "aujourd'hui "; 
		if ($d_hier == $d_dt) // si c'est hier je ne tien compte que de l'heure
			$dts = "hier "; 
		if(!$dts)	
			{// si c'est un autre jour je l'indique.
			if ($option) $dts = $txt_j.$dts;
			$dts .= $n_jour."/".$n_mois."/".$aaaa." ";
			}
		// je met l'heure et minutes
		if ($option) $dts = $dts.$txt_h;
		$dts .= $heure.":".$minut;
		return $dts; // indique la valeur à renvoyer 
	} 

	// Transformation de format de date type a la francaise  jj/mm/aaaa vers un format  SQl type AAAA-MM-JJ
	function dtfr2dtsql($dtfr) 
	{ 
		$dtfr = trim($dtfr);
		$dt = explode(' ',$dtfr); // separe la date de time
		if (count($dt) == 1) // il n'y a pas de time
			{$dfr = $dtfr; $tfr = "";}
		else	
			{$dfr = $dt[0]; $tfr = $dt[1];}
		$amj = explode('/',$dfr); 
		if (count($amj) == 3)	
		{
			$timestamp = mktime(0 ,0 ,0 ,$amj[1] ,$amj[0] ,$amj[2]);
			$dsql = date("Y-m-d", $timestamp);
		}
		else 
			$dsql = "";
		$dtsql = trim($dsql . " " . $tfr);
		return $dtsql; // indique la valeur à renvoyer 
	} 

	// Transformation de format de date type SQl type AAAA-MM-JJ vers un format a la francaise jj/mm/aaaa  !!! attention timestamp limite 2038 !!!
	function dtsql2dtfr($dtsql, $option=false) 
	{ 
		$dtsql = trim($dtsql);
		$dt = explode(' ',$dtsql); // separe la date de time
		if (count($dt) == 1 or $option == 'NOTIME') // il n'y a pas de time
			{$dsql = substr($dtsql,0,10) ; $tsql = "";}
		else	
		{
			$dsql = $dt[0]; $tsql = $dt[1];
			if ($option == 'NOSEC') $tsql = substr($tsql,0,5); // Supprime les secondes
		}
		$amj = explode('-',$dsql); 
		if (count($amj) == 3)	
		{
			$timestamp = mktime(0 ,0 ,0 ,$amj[1] ,$amj[2] ,$amj[0]);
			$dfr = date("d/m/Y", $timestamp);
			if($amj[0] == "0000") $dfr = "";
		}
		else 
			$dfr = "";
		$dtfr = trim($dfr . " " . $tsql);
		return $dtfr; // indique la valeur à renvoyer 
	} 

	// inverse de nl2br (trouvé sur : http://fr.php.net/manual/fr/function.nl2br.php
	function br2nl($text)
	{
		return str_replace("<br>", "\n", $text); 
	}

	// Limite un txt a un nombre de ligne (\n ou <br>) et un nombre de Characteres
	function limit_txt($txt, $nb_char=500, $nb_lg=10, $new_lg="<br>")
	{
		$long_txt    = strlen($txt);
		$limit_txt   = substr($txt,0,$nb_char); // je coupe au nombre de char max.
		$last_pos    = 0;
		$long_new_lg = strlen($new_lg);
		for ($l = 0; $l < $nb_lg-1; $l++) // recherche X-1 fois le saut de ligne <BR> ou \n
		{
			$pos  = strpos($limit_txt, $new_lg, $last_pos);
			if ($pos !== false) // il a trouvé
				$last_pos = $pos+$long_new_lg;
		}
		$pos  = strpos($limit_txt, $new_lg, $last_pos);
		if ($pos !== false) // il a trouvé
			$last_pos = $pos;
		else
			$last_pos = $nb_char;	
		if ($last_pos > 0)
			$limit_txt   = substr($limit_txt,0,$last_pos); // je coupe au nombre de char max.
		if (strlen($limit_txt) < $long_txt) $limit_txt .= "...";
		return $limit_txt;
	}

	// Retourne le text avec une limte de x caracteres si plus grand coupé et ...
	function lg_txt_max($txt,$lg_max) 
	{ 
		$txt = trim($txt);
		$lg  = strlen($txt);
		if($lg > $lg_max)
			$txt = substr($txt,0,$lg_max)."...";
		return $txt; // indique la valeur à renvoyer 
	} 

	// Retourne le text Normalisé pour marque  et modèle (majus+remplece signes par -)
	function txt_normalize($txt) 
	{ 
		$txt = trim($txt);
		$txt = strtoupper($txt);
		$no_signe = array("&", '"', "'", "(", ")", "_", ".", ",", "*", "<", ">", "/", "+", " ");
		$txt = str_replace($no_signe, "-", $txt);
		$txt = str_replace("---", "-", $txt);
		$txt = str_replace("--", "-", $txt);
		$txt = trim($txt, "-"); // pour supprimer les - de debut ou de fin 
		return $txt; // indique la valeur à renvoyer 
	} 

	// Retourne le telephone avec des espaces tout les 2 car
	function botel($tel) 
	{
				$tel = trim($tel);
		$botel = $tel;
		$sep = array(" ", ".", ",","-"); 
		$tel = str_replace($sep, "", $tel);
		$l = strlen($tel);
		if ($l == 10)
			$botel = substr($tel,0,2).".".substr($tel,2,2).".".substr($tel,4,2).".".substr($tel,6,2).".".substr($tel,8,2);
		return $botel; // indique la valeur à renvoyer 

	}

	// Retourne dans la variable ce qui a été posté en get ou post et/ou Session
	function get_post($get_post, $arg=FALSE, $get_sess=FALSE) 
	{
		// récupération des Post et Get + Session en option 
		// $arg Option remplace vide par : [0:0, 1:false, 2:false/true, 3:Null, 8:img, 7:SQL, 9:Tab] 
		// Valeur Session[FALSE:pas pris en compte, 'SESS':prend en priorité la session, 'GETPOST':prend en priorité le get post]

		$vide = "";  
		$is_array = $is_img = $add_slashes = $is_getpost = $is_sess = FALSE;

		$val_sess = FALSE;
		if (isset($_SESSION[$get_post])) { $is_sess = TRUE; $val_sess = $_SESSION[$get_post]; }

		if ($arg)
		{ // si il a argument suplementaire
			switch ($arg)
			{ // regarde l argument suplementaire
				case 0: // si pas de valeur retour alors 0 a la place de ""
					$vide = 0; break;
				case 1: // si pas de valeur retour alors FALSE a la place de ""
					$vide = FALSE; break;
				case 2: // si pas de valeur retour alors FALSE a la place de "" si valeur retour de TRUE a la place de l argument.
					$vide = FALSE; break;
				case 3: // si pas de valeur retour alors NULL a la place de "" 
					$vide = NULL; break;
				case 8: // c est une image je recupere la coordoné x
					$is_img = TRUE; break;
				case 7: // c est un text destiner à etre mis dans une commande SQL donc je le ADDSLASHES()
					$add_slashes = TRUE; break;
				case 9: // C est un tableau je n utilise pas la fonction trim
					$is_array = TRUE; break;
			}			
		}
		$val_get_post = $vide;
		if ($is_array or $is_img) // il semble que le trim efface la structure tableau ????
		{
			if (isset($_POST[$get_post]))  { $is_getpost=TRUE; $val_get_post  = $_POST[$get_post]; }
		}
		else
		{
			if (isset($_POST[$get_post]))  { $is_getpost=TRUE; $val_get_post  = trim($_POST[$get_post]); }
			if (isset($_GET[$get_post]))   { $is_getpost=TRUE; $val_get_post  = trim($_GET[$get_post]); }
		}
		if ($add_slashes) // je transforme les ' en \' et les " en \"
		{
			$val_get_post  = addslashes($val_get_post);
		}
		if ($is_img) // je le refait en lisant la coordonnée X
		{
			$get_post_x = $get_post.'_x';
			if (isset($_POST[$get_post_x]))  $val_get_post  = 1 + $_POST[$get_post_x]; else $val_get_post = $vide; // le +1 est pour eviter la position 0 qui est interprété comme false
		}
		if ($arg == 2 and $val_get_post !== FALSE) $val_get_post = TRUE; // retour TRUE en cas de 2eme argument a 2 et argument 1 non vide.

		// valeur a renvoyer ?
		$val_return = $val_get_post;
		if($get_sess == 'SESS') // je prend en priorité la session si elle existe
		{
			if($is_sess) $val_return = $val_sess;
		}
		if($get_sess == 'GETPOST') // je prend en priorité le get_post si il existe
		{
			if($is_sess) $val_return = $val_sess;
			if($is_getpost) $val_return = $val_get_post;
		}
		return $val_return; // indique la valeur à renvoyer 
	}

	function debug($info_debug)
	{
		if (isset($_SESSION['user__debug']))
		{
		if ($_SESSION['user__debug']) 
			{
				print '
				<div class="alert alert-warning alert-dismissible" role=alert>
					<div class=form-group>
						<div class=row>
							<div class=col-md-1><a data-toggle=tooltip title="Debug"><i class="fa fa-bug fa-3x text-warning"></i></a></div>
							<div class=col-md-11>
								<button type=button class=close data-dismiss=alert aria-label=Close><span aria-hidden=true>&times;</span></button>
								'.$info_debug.'
							</div>
						</div>
					</div>
				</div>';
			}
		}
	}

	function info($info_info)
	{
		$info_info = trim($info_info);
		if ($info_info) 
		{
			$alerte = false;
			$info_info = trim($info_info);
			$info_logo = "fa-info"; $info_title = "Information"; $info_variation = "info";
			// si $info_info debute par un ! c'est une alerte donc en rouge et avec un autre picto et je supprime le !
			if (substr($info_info,0,1) == '!') 
			{
				$info_logo = "fa-hand-stop-o";
				$info_info = substr($info_info,1);
				$info_variation = "danger";
				$info_title = "Alerte";
			}
			print '
			<div class="alert alert-'.$info_variation.' alert-dismissible" role=alert>
				<div class="form-group">
					<div class="row">
						<div class="col-md-1"><a data-toggle="tooltip" title="'.$info_title.'"><i class="fa '.$info_logo.' fa-3x text-'.$info_variation.'"></i></a></div>
						<div class="col-md-11 h4" >
							<button type=button class=close data-dismiss=alert aria-label=Close><span aria-hidden=true>&times;</span></button>
							'.$info_info.'
						</div>
					</div>
				</div>
			</div>';
		}
	}

	// redirection de page
	function redirect($page)
	{ // ecrit un script de redirection de la page.
		// Attention c'est un script qui s'ecrit  dans la page donc la page continue a s'executer
		// et ce n'est qua la fin de la page que la redirection a lieu
		global $debug;
		if ($debug)
			debug("Debug Redirect :<br><a href=\"".$page."\">".$page."</a>");
		else
		{
			print "
			<SCRIPT language=javascript>
			document.location.href=\"$page\"
			</SCRIPT>";
		}
	}

	// Lecture de la table keyword pour les menus deroulants et Mise en tableau.
	function lire_keyword() 
	{
		global $_MYSQLI;
		// les ordres <0 ne sont pas lues... (ils sont dans la base mais plus dispo a la lecture des menu)
		$requete  = "SELECT kw__firm_id, kw__type, kw__valeur, kw__ordre, kw__libel ";
		$requete .= "FROM keyword where kw__ordre >= 0 ";
		$requete .= "ORDER BY kw__type, kw__ordre ";
		$res_kw   = mysqli_query($_MYSQLI, $requete);
		//debug($requete);
		return($res_kw);
	}

/*P"Y8 888888 88     888888  dP""b8 888888            88  dP Yb        dP 
`Ybo." 88__   88     88__   dP   `"   88              88odP   Yb  db  dP  
o.`Y8b 88""   88  .o 88""   Yb        88              88"Yb    YbdPYbdP   
8bodP' 888888 88ood8 888888  YboodP   88   oooooooooo 88  Yb    YP  */    

	// creation de menu deroulant a partir de la table keyword  
	function select_kw($type, $firm=0, $keyword=FALSE, $out=FALSE, $libel_default='Aucune s&eacute;lection.', $select_disabled=FALSE)
	{
		// type = type dans la table keyword pour savoir quoi proposer 
		// firm = ID de la société (la societe 0 est special c'est utilisé pour toutes les sociétés)
		// keyword = valeur a presélectionner doit etre du meme type que $out 
		// out = le menu affiche les libel et retourne valeur ou libel si out = 'L'
		// out = si il y a un - ajout de aucune séléction au debut de la liste qui renvoie une valeur vide.
		// out = si il y a un + ajout de -tous- au debut de la liste qui renvoie une valeur *ALL*.
		// out = si il y a un * c'est un choix multiple possible (sur 10 lignes) renvoie un tableau
		// out = si il y a un ? c'est Avec un champ de recherche dans le menu deroule
		// select_disabled = si vrai : le menu est desactivé.

		/* exemple * voir https://silviomoreto.github.io/bootstrap-select/ pour les possibilitées
		
		pour une simple selection 1 element avec refermeture du deroulant apres selection.
		<select id="basic" class="selectpicker show-tick form-control">
		  <option>cow</option>
		  <option data-subtext="option subtext">bull</option>
		  <option class="get-class" disabled>ox</option>
		  <optgroup label="test" data-subtext="optgroup subtext">
			<option>ASD</option>
			<option selected>Bla</option>
			<option>Ble</option>
		  </optgroup>
		</select> 

		pour une selection multiple (il faut refermer le deroulant a la main)
		<select id="maxOption2" class="selectpicker form-control" multiple data-max-options=2>
		  <option>chicken</option>
		  <option>turkey</option>
		  <option disabled>duck</option>
		  <option>horse</option>
		</select>*/
		global $table_keyword;
		$ret_libel = (strpos($out,"L") !== false ? true : false ); 
		$unique    = (strpos($out,"+") !== false ? false : true );
		$noselect  = (strpos($out,"-") !== false ? true : false );
		$multi     = (strpos($out,"*") !== false ? true : false );
		$search    = (strpos($out,"?") !== false ? true : false );
		mysqli_data_seek($table_keyword,0);
		$datalive  = ($search) ? "data-live-search=true": ""; 
		$txt_disabled = '';
		if ($select_disabled) $txt_disabled = 'disabled';

		if ($multi)
			print '<select id="skw_'.$type.'" '.$txt_disabled.' name="skw_'.$type.'" class="selectpicker form-control" multiple data-max-options=2 title="Choisir X &eacute;l&eacute;ments" >';
		else
			print '<select id="skw_'.$type.'" '.$txt_disabled.' name="skw_'.$type.'" class="selectpicker show-tick form-control" '.$datalive.' data-header="'.$libel_default.'" title="'.$libel_default.'">';
		//if ($noselect) print '<option value="" > '.$libel_default.'</option> <br>';
		if (!$unique)  print '<option value="*ALL*"> - Tous - </option>';
		while ($arr_kw=mysqli_fetch_array($table_keyword))
		{
			if ($arr_kw['kw__type'] == $type and ($arr_kw['kw__firm_id'] == $firm or $arr_kw['kw__firm_id'] == 0) )	
			{
				// recherche si $arr_kw['keyword'] possed un # qui separe le libel d'un complement
				if ($ret_libel) // je retourne le champ libel
				{
					print '<option value="'.$arr_kw['kw__libel'].'" ';
					if (strtoupper($keyword) == strtoupper(trim($arr_kw['kw__valeur']))) 
						print 'selected="selected" ';
				}
				else // je retourne le champ keyword
				{
					print '<option value="'.$arr_kw['kw__valeur'].'" ';
					if (strtoupper($keyword) == strtoupper(trim($arr_kw['kw__valeur']))) 
						print 'selected="selected" ';
				}		
				print '> '.$arr_kw['kw__libel'].' </option>';
			}
		}
		print '</select>';
	}

/*8888 Yb  dP 888888            888888 88""Yb  dP"Yb  8b    d8            88  dP Yb        dP 
  88    YbdP    88              88__   88__dP dP   Yb 88b  d88            88odP   Yb  db  dP  
  88    dPYb    88              88""   88"Yb  Yb   dP 88YbdP88            88"Yb    YbdPYbdP   
  88   dP  Yb   88   oooooooooo 88     88  Yb  YbodP  88 YY 88 oooooooooo 88  Yb    YP  */    
	function txt_from_kw($type, $keyword, $firm=0) 
	// retourne le libel en fonction de type et valeur et firm_id de la table keyword
	{
		global $table_keyword;
		mysqli_data_seek($table_keyword,0);
		$result = "";
		while ($arr_kw=mysqli_fetch_array($table_keyword))
		{
			if ($arr_kw['kw__type'] == $type and strtoupper($arr_kw['kw__valeur']) == strtoupper($keyword) and ($arr_kw['kw__firm_id'] == $firm or $arr_kw['kw__firm_id'] == 0) )	
				$result = trim($arr_kw['kw__libel']); 

		}
		return $result;
	}

	// retourne le prenom et nom a partir du user_id
	function txt_from_user_id($user_id, $form=false)
	{
		global $_MYSQLI;
		$result = FALSE;
		if ($user_id)
		{
			$requete     = "SELECT user.user__nom, user.user__prenom, user.user__tel, user.user__gsm, firm.firm__nom  FROM user INNER JOIN firm ON firm.firm__id = user.user__firm_id WHERE user__id = $user_id ";
			//debug($requete);
			$res_user    = mysqli_query($_MYSQLI, $requete);
			$row_user    = mysqli_fetch_assoc($res_user);  // FLM gérer les erreurs (si pas de retour sql)
			$user_nom    = $row_user['user__nom'];
			$user_prenom = $row_user['user__prenom'];
			$user_tel    = $row_user['user__tel'];
			$user_gsm    = $row_user['user__gsm'];
			$firm_nom    = $row_user['firm__nom'];
			if ($user_prenom == '**GRP**') 
				$user_prenom = ""; // special pour ne pas voir **GRP** pour les groupes
			$result = trim($user_prenom.' '.$user_nom);
			if (strpos($form,'FIRM') !== FALSE)
				$result .= ' - '.$firm_nom.' '; 
			if (strpos($form,'TG') !== FALSE) 
			{ 
				if (strlen($user_tel) > 0) $result .= '<a data-toggle="tooltip" title="Tel : '.$user_tel.'"><i class="fa fa-phone  fa-lg"></i></a> '; 
				if (strlen($user_gsm) > 0) $result .= '<a data-toggle="tooltip" title="GSM : '.$user_gsm.'"><i class="fa fa-mobile fa-lg"></i></a> '; 
			}
		}
		return $result;
	}



	// Génération d'une chaine aléatoire
	function chaine_aleatoire($nb_car)
	{
		$chaine = 'azertyuiopqsdfghjklmwxcvbn123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$nb_lettres = strlen($chaine) - 1;
		$generation = '';
		for($i=0; $i < $nb_car; $i++)
		{
			$pos = mt_rand(0, $nb_lettres);
			$car = $chaine[$pos];
			$generation .= $car;
		}
		return $generation;
	}

	// Lecture de fichier vers variable tableau.
	function fic2tab($nom_fichier, $no_vide=true, $no_comment=true)
	{ // Lecture d'un fichier type txt et renvoie d'un tableau avec le contenue (une ligne de tableau par ligne de text)
		// l'element [0] contien le nombre d'elements dans le tableau 
		// si no_vide est a True les ligne vide son ignorés
		// si no_comment est a True les lignes qui debutes par -- ou // sont ignorées
		// si le fichier n'est pas trouver la valeur de retour est -1 dans elememnt [0]
		$tab[0] = 0;
		if (file_exists($nom_fichier)) 
		{
			$fic = fopen ($nom_fichier, "r");
			$i = 0;
			while (!feof ($fic)) 
			{  
				$buffer = trim(fgets($fic, 4096));
				if ( !(strlen($buffer) == 0 and $no_vide) ) // saute si la ligne est vide et $no_vide a true
				{
					if ( !((substr($buffer, 0, 2) == '--' or substr($buffer, 0, 2) == '//' ) and $no_comment) ) // saute si ligne de commentaires et $no_comment a true
					{
						$tab[] = $buffer;  // inutile de preciser le numéro de l'element PHP le fait tout seul.
						$i ++;
					}
				}
			}
			fclose ($fic);
			$tab[0] = $i;
		}
		else // pas de fichier contrat
			$tab[0] = -1; // le fichier n'existe pas !
		return $tab; // indique la valeur à renvoyer 
	}

  $time_start = microtime_float();
?>

