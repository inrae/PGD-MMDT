<?php

// Chargement fichier config
require_once('../config/config.php');
require_once('../functions/functions.php');

echo '
<!DOCTYPE html>
<html lang="fr" >

<head>
	<meta charset="UTF-8">
	<title>Jeux de donn&eacute;es / Saisie des m&eacute;tadonn&eacute;es</title>

	<!-- CSS -->
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/bootstrap-toc.css">
</head>

<body>

<div class="left-div">
	<div class="btn-group btn-group-lg" role="group">
		<img src="../img/logo_inrae.svg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<img src="../img/Logo-Biogeco_inra_logo.png">
	</div>
	<div class="center-div">
		<div class="btn-group btn-group-lg" role="group">
		  <a href="../" class="search_data"><button type="button" class="btn btn-outline-secondary mr-2" data-toggle="button" aria-pressed="false" autocomplete="off">Saisie de m&eacute;tadonn&eacute;es</button></a>
		  <a href="./search.html" class="search_data"><button type="button" class="btn btn-outline-secondary mr-2" data-toggle="button" aria-pressed="false" autocomplete="off">Recherche de jeux de donn&eacute;es</button></a>
		  <a href="../docs/doc-pgd-biogeco.html" class="search_data"><button type="button" class="btn btn-outline-info mr-2" data-toggle="button" aria-pressed="false" autocomplete="off">Documentation</button></a>
		</div>
	</div>
</div>

<hr>

<h1>M&eacute;tadonn&eacute;s du jeu de donn&eacute;e</h1>
<br/>
';

// Traitement de la recherche
if( isset( $_GET['id'] ) ){

	// Id jeu de donnée
	$id_metadata =  $_GET['id'];

	// Options affichage
	$options = [
		'projection' => [
			'title' => 1, 'statutdispo' => 1, 'statutconservation' => true, 'multivalorisation' => true, 'tag_annee' => true,
			'tag_autredonnee' => true, 'tag_contrat' => true, 'tag_dispo' => true, 'tag_dna' => true, 'tag_dynamique' => true, 'tag_envt' => true, 'tag_espece' => true, 'tag_financeur' => true, 'tag_habitat' => true, 'tag_info' => true, 'tag_nvxbio' => true, 'tag_organisme' => true, 'tag_pheno' => true, 'tag_site' => true, 'statutconservation' => true, 'organism' => true, 'respsci' => true, 'resptech' => true, 'mailtech' => true, 'nom-commune' => true,'latitude' => true, 'longitude' => true, 'description' => true, 'statutdispo' => true, 'diffusion' => true, 'chemin' => true
		],
		'sort' => [
			'title, statutdispo, statutconservation' => 1
		]
	];

	// Requête pour récupérer l'ensemble des champs de la table metadata pour ce jeu de donnée
	$query = new \MongoDB\Driver\Query(
		[
			'_id' => new \MongoDB\BSON\ObjectID($id_metadata),
			'title' => [ '$exists' => true ]
		],
		$options
	);
	$cursor_count = $client->executeQuery('pgd-db.metadata', $query)->toArray();
	$count = count($cursor_count);
	$cursor = $client->executeQuery('pgd-db.metadata', $query);
	//var_dump($cursor->toArray());

	$order = array('title', 'description', 'statutdispo', 'statutconservation', 'chemin', 'diffusion', 'organism', 'respsci', 'mailsci', 'organismtech', 'resptech', 'mailtech', 'tag_financeur', 'tag_contrat', 'multivalorisation', 'tag_dispo', 'tag_site', 'nom-commune', 'latitude', 'longitude', 'tag_annee', 'tag_nvxbio', 'tag_organisme', 'tag_espece', 'tag_habitat', 'tag_dna', 'tag_pheno', 'tag_envt', 'tag_info', 'tag_dynamique', 'tag_autredonnee');

   // Resultats ?
   if( $count > 0 ){

		// Affichage //
		foreach($cursor as $key => $value) {
			/* sort by field */
			$array_values = get_object_vars($value);
			$array_sorted = SortByKeyList($array_values,$order);

			//print_r($array_sorted);

			foreach($array_sorted as $key => $val) {

				// Val empty
				if (!is_array($val) && empty($val)) { $val = '-'; }

				// Key
				if ($key == "_id") { continue; }

				// Nom jeu de données
				if ($key == "title") {
					echo "<h2>".$val."</h2><br/>";
				}

				// Description jeu de données
				if ($key == "description") {
					echo "<h2 class='description'>".$val."</h2><br/>";
				}

				// STATUT
				if ($key == "statutdispo") {
					echo '<h3>STATUT</h3><div class="table-responsive-sm">
						<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Statut d\'avancement du jeu de données</th>
								<th scope="col">Durée de conservation du jeu de données avant archivage (en années)</th>
								<th scope="col">Emplacement sur NAS</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val.'</td>';
				}
				if ($key == "statutconservation") {
					echo '<td>'.$val.'</td>';
				}
				if ($key == "chemin") {
					echo '<td>'.$val.'</td></tr></tbody></table><hr>';
				}

				// GESTION
				if ($key == "diffusion") {
					echo '<h3>GESTION</h3><div class="table-responsive-sm">
						<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Droits d\'accès aux répertoires</th>
								<th scope="col">Organisme gestion scientifique</th>
								<th scope="col">Responsable scientifique</th>
								<th scope="col">Email responsable scientifique</th>
								<th scope="col">Organisme gestion technique</th>
								<th scope="col">Responsable technique</th>
								<th scope="col">Email responsable technique</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val.'</td>';
				}
				if (in_array($key, array('organism', 'respsci', 'organismtech', 'resptech'))) {
					echo '<td>'.$val.'</td>';
				}
				if (in_array($key, array('mailsci'))) {
					echo '<td><a href="mailto:'.$val.'">'.$val.'</td>';
				}
				if (in_array($key, array('mailtech'))) {
					echo '<td><a href="mailto:'.$val.'">'.$val.'</td></tr></tbody></table>';
				}

				if ($key == "tag_financeur") {
					$val_financeur = display_array($val);
					echo '<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Financeur</th>
								<th scope="col">Contrat</th>
								<th scope="col">Valorisation</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val_financeur.'</td>';
				}
				if ($key == "tag_contrat") {
					$val_contrat = display_array($val);
					echo '<td>'.$val_contrat.'</td>';
				}
				if ($key == "multivalorisation") {
					$val_multivalorisation = display_array($val);
					echo '<td>'.$val_multivalorisation.'</td></tr></tbody></table><hr>';
				}

				// LOCALISATION
				if ($key == "tag_dispo") {
					$val_tag_dispo = display_array($val);
					echo '<h3>LOCALISATION</h3><div class="table-responsive-sm">
						<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Nom du dispositif</th>
								<th scope="col">Nom du site</th>
								<th scope="col">Commune</th>
								<th scope="col">Latitude</th>
								<th scope="col">Longitude</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val_tag_dispo.'</td>';
				}
				if ($key == "tag_site") {
					$val_site = display_array($val);
					echo '<td>'.$val_site.'</td>';
				}
				if (in_array($key, array('nom-commune', 'latitude'))) {
					echo '<td>'.$val.'</td>';
				}
				if (in_array($key, array('longitude'))) {
					echo '<td>'.$val.'</td></tr></tbody></table><hr>';
				}

				// DESCRIPTEURS
				if ($key == "tag_annee") {
					$val_tag_annee = display_array($val);
					echo '<h3>DESCRIPTEURS</h3><div class="table-responsive-sm">
						<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Année(s) de production des données</th>
								<th scope="col">Echelle du vivant</th>
								<th scope="col">Type d\'organisme</th>
								<th scope="col">Espèce(s) étudiée(s)</th>
								<th scope="col">Habitat</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val_tag_annee.'</td>';
				}
				if ($key == "tag_nvxbio") {
					$val_nvxbio = display_array($val);
					echo '<td>'.$val_nvxbio.'</td>';
				}
				if ($key == "tag_organisme") {
					$val_organisme = display_array($val);
					echo '<td>'.$val_organisme.'</td>';
				}
				if ($key == "tag_espece") {
					$val_espece = display_array($val);
					echo '<td>'.$val_espece.'</td>';
				}
				if ($key == "tag_habitat") {
					$val_habitat = display_array($val);
					echo '<td>'.$val_habitat.'</td></tr></tbody></table>';
				}

				if ($key == "tag_dna") {
					$val_dna = display_array($val);
					echo '<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Type donnée omique</th>
								<th scope="col">Type donnée phénotypique</th>
								<th scope="col">Type donnée environnement</th>
								<th scope="col">Type donnée / code informatique</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val_dna.'</td>';
				}
				if ($key == "tag_pheno") {
					$val_pheno = display_array($val);
					echo '<td>'.$val_pheno.'</td>';
				}
				if ($key == "tag_envt") {
					$val_envt = display_array($val);
					echo '<td>'.$val_envt.'</td>';
				}
				if ($key == "tag_info") {
					$val_info = display_array($val);
					echo '<td>'.$val_info.'</td></tr></tbody></table>';
				}

				if ($key == "tag_dynamique") {
					$val_dynamique = display_array($val);
					echo '<table class="table">
						<thead class="thead-light">
							<tr>
								<th scope="col">Dynamique</th>
								<th scope="col">Autre type de donnée</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>'.$val_dynamique.'</td>';
				}
				if ($key == "tag_autredonnee") {
					$val_autredonnee = display_array($val);
					echo '<td>'.$val_autredonnee.'</td>';
				}
			}
		}
        echo "</div>";
    } else {
		echo  '<div class="p-3 mb-2 bg-warning text-black">Pas de m&eacute;tadonn&eacute;es disponibles pour ce jeu de donn&eacute;es ...';
	}
}

?>
