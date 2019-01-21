<?php
include 'function.php';

if(isset($_FILES['csv'])){
	//ini_set('display_errors', TRUE);
	$server = $_SERVER['DOCUMENT_ROOT'].'/MAIL_CHECK';
	$dossier = $server.'/upload/';
	$fichier = basename($_FILES['csv']['name']);
	if(move_uploaded_file($_FILES['csv']['tmp_name'], $dossier.$fichier)) {

		// Si la fonction renvoie TRUE, c'est que ça a fonctionné...
		$file = date("i").'-'.$_FILES['csv']['name'];
		$filedelete = date("i").'-DELETE'.$_FILES['csv']['name'];
		$download = '<a href="/MAIL_CHECK/correct/'.$file.'" download class="upload">	Télécharger le csv propre</button></a><br><br>';
		$downloaddelete = '<a style = "background-color: red;" href="/MAIL_CHECK/delete/'.$filedelete.'" download class="upload">	Télécharger le csv des lignes supprimées</button></a><br><br>';
		$lignessupp = 'Adresses supprimées : ';
		$lignemodif = 'Adresses modifiées : ';


		// Paramétrage de l'écriture du futur fichier CSV
		// mettre $serveur + $file
		$chemin = $_SERVER['DOCUMENT_ROOT'].'/MAIL_CHECK/correct/'.$file;
		$chemindelete = $_SERVER['DOCUMENT_ROOT'].'/MAIL_CHECK/delete/'.$filedelete;
		$delimiteur = ','; // Pour une tabulation, utiliser $delimiteur = "t";

		// Création du fichier csv (le fichier est vide pour le moment)
		// w+ : consulter http://php.net/manual/fr/function.fopen.php
		$fichier_csv = fopen($chemin, 'x+');
		$delete_csv = fopen($chemindelete, 'x+');
		// Si votre fichier a vocation a être importé dans Excel,
		// vous devez impérativement utiliser la ligne ci-dessous pour corriger
		// les problèmes d'affichage des caractères internationaux (les accents par exemple)

		fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));
	}
	else{
		// Sinon (la fonction renvoie FALSE).
		$resultat = '<p class="alert">Echec de l\'upload !</p>';
	}

	//Le chemin d'acces a ton fichier sur le serveur
	$fichier = fopen($dossier.$_FILES['csv']['name'], "r");
	$dossier.$_FILES['csv']['name'];
	//tant qu'on est pas a la fin du fichier :
	$i=1;
	$a=0; // compte le nombre de lignes supprimées
	$b=0; // compte le nombre d'email modifié
	$uneLigne = fgets($fichier);
	$uneLigne = str_replace("\r\n", "", $uneLigne);
	$uneLigne = preg_replace("/\xEF\xBB\xBF/", "", $uneLigne);
	$tableauValeurs = explode(",", $uneLigne);

	$nbcol = count($tableauValeurs) + 1;
	$c = 0;
array_push($tableauValeurs, "Modification");
	for ($c = 0; $c < $nbcol; $c++) {
$col = $tableauValeurs[$c];
switch ($col) {
    case "email":
				$nume = $c;
        break;
    case "prenom":
				$nump = $c;
        break;
    case "nom":
				$numn = $c;
        break;
		default:
		break;
}
}
rewind($fichier);
	while (!feof($fichier)){
		// On récupère toute la ligne
		$uneLigne = fgets($fichier);
		// On met dans un tableau les différentes valeurs trouvés (ici séparées par un ',')
		$tableauValeurs = explode(",", $uneLigne);
		//Suppression accent & caractère spéciaux & Remplacer certains domaines definis
		$email = $tableauValeurs[$nume];
		$tableauValeurs[$nume] = Mailcheck($tableauValeurs[$nume]);
		$tableauValeurs[$nump] = Namecheck($tableauValeurs[$nump]);
		$tableauValeurs[$numn] = Namecheck($tableauValeurs[$numn]);

		if(validatemail($tableauValeurs[$nume]) == false && $i != 1){
			$ajout = implode(",", $tableauValeurs);
			$ajout = str_replace("\r\n", "", $ajout);
			$ajout = "$ajout\r\n";
			//ecriture
			fwrite($delete_csv, $ajout);
			$tableauValeurs = '';
		  $a++;
		}
		else{
						// compte nombre d'email modifiés et supprimé + ajout d'une colonne modif
						if( $email != $tableauValeurs[$nume]){
							array_push($tableauValeurs, $email);
							$b++;
						}
						elseif ($i == 1) {
							array_push($tableauValeurs, "Modifications");
						}
						else {
							array_push($tableauValeurs, "");
						}

							$ajout = implode(",", $tableauValeurs);
							$ajout = str_replace("\r\n", "", $ajout);
							$ajout = "$ajout\r\n";
							//ecriture
							fwrite($fichier_csv, $ajout);
		}

	unset($tableauValeurs);
	$i++;
	}
}
// fermeture  fichiesr csv
fclose($delete_csv);
fclose($fichier_csv);
fclose($fichier);
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>Email contrôle</title>

		<!-- CSS -->
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<!-- JS -->
		<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
		<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="js/reunion-script.js"></script>
	</head>

	<body class="body-general">
		<main>
			<section>
				<article>
					<div class="titre_td">Verification du fichier csv</div>
					<?php
					echo $lignessupp; echo $a.'<br>';
					echo $lignemodif; echo $b.'<br><br>';
					echo $download;
					echo $downloaddelete;?>
					<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" class="upload">
						<br>
						<input type="file" name="csv" class="upload" required>
						<br/>
						<input type="hidden" name="MAX_FILE_SIZE" value="20000">
						<p class="upload">
							<br>
							<img src="img/upload.png">
							<br>Drag & drop votre fichier ici.
						</p>
						<button type="submit" class="upload">Envoyer le fichier</button>
					</form><br>
				</article>
			</section>
		</main>
	</body>
</html>
