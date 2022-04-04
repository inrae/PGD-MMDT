<?php

// Chargement fichier config
require_once('../config/config.php');

// Traitement de la recherche
if( isset( $_POST['param'] ) ){

	// Clauses where pour requêtes mongo
    $where_search = array();
    $array_where = array();
    $array_organism = array();
    $array_organismtech = array();
    $array_freefield = ['description','organism', 'organismtech', 'statutdispo', 'respsci','mailsci','resptech','mailtech','nom-commune','latitude','longitude', 'diffusion'];
    $array_multi = ['multivalorisation','tag_dispo','tag_site','tag_dna','tag_pheno','tag_info','tag_envt','tag_contrat', 'tag_espece', 'tag_annee', 'tag_financeur', 'tag_nvxbio'];

    // D&eacute;coder le json contenant les donn&eacute;es à chercher
	$params = json_decode($_POST['param'], TRUE);

	// Options
	$options = [
		'projection' => [
			'title' => true, 'respsci' => true, 'resptech' => true, 'statutdispo' => true, 'diffusion' => true
		],
		'sort' => [
			'title, respsci, resptech, statutdispo, diffusion' => 1
		]
	];

	// Parser le json de donn&eacute;es saisies
	foreach($params as $key => $val) {
		if ($key == 'operator') {
			$operator = $val;
		}
		if (in_array($key, $array_freefield)) { # Champs libres ou balises select
			if( ! empty( $val) ){
				if ($val == 'Tous'){
					if ($key == 'statutdispo' || $key == 'diffusion' || $key == 'organism' || $key == 'organismtech') {
						if ($key == 'statutdispo') {
							$array_temp = array('$or' => array(
						        array(
									"$key" => "Disponible brut",
								),
								array(
									"$key" => "Disponible exploitable",
								),
						        array(
									"$key" => "En cours",
						        ),
							));
							array_push($array_where, $array_temp);
						}
						if ($key == 'diffusion') {
							$array_temp = array('$or' => array(
						        array(
									"$key" => "Public",
								),
								array(
									"$key" => "Priv&eacute;",
								),
						        array(
									"$key" => "Mixte",
						        ),
							));
							array_push($array_where, $array_temp);
						}
						if ($key == 'organism') {
							$cmd_organism = new MongoDB\Driver\Command([
								'distinct' => 'metadata', // specify the collection name
								'key' => 'organism'// specify the field for which we want to get the distinct values
							]);
							$cursor = $client->executeCommand('pgd-db', $cmd_organism); // retrieve the results
							$organisms = current($cursor->toArray())->values; // get the distinct values as an array
							foreach($organisms as $keyorganism => $organism) {
								array_push($array_organism, array("$key" => $organism));
							}
							$array_temp = array( '$or' => $array_organism );
							array_push($array_where, $array_temp);
						}
						if ($key == 'organismtech') {
							$cmd_organismtech = new MongoDB\Driver\Command([
								'distinct' => 'metadata',
								'key' => 'organismtech'
							]);
							$cursor = $client->executeCommand('pgd-db', $cmd_organismtech);
							$organismstech = current($cursor->toArray())->values;
							foreach($organismstech as $keyorganism => $organismtech) {
								array_push($array_organismtech, array("$key" => $organismtech));
							}
							$array_temp = array( '$or' => $array_organismtech );
							array_push($array_where, $array_temp);
						}
					} else {
						$filter = new MongoDB\BSON\Regex("$val","i");
						$array_temp = array("$key" => $filter);
						array_push($array_where, $array_temp);
					}
				} else {
					$filter = new MongoDB\BSON\Regex("$val","i");
					$array_temp = array("$key" => $filter);
					array_push($array_where, $array_temp);
				}
			}
		} else { # Checkboxes sous la forme tag_annee-23 par exemple (.+_\d+)
			if (preg_match('/(\w+_\w+)-\d+/', $key, $matches) == 1) {
				$filter = new MongoDB\BSON\Regex("$val","i");
				$array_temp = array("$matches[1]" => $filter);
				array_push($array_where, $array_temp);
			}
		}
	}

	// Clause where finale
	$where_search = array(
		"$operator" => $array_where
	);

    // Rechercher tous les jeux de donn&eacute;es r&eacute;pondant aux crit&egrave;res saisis
	$query = new MongoDB\Driver\Query($where_search, $options);
	$cursor_count = $client->executeQuery('pgd-db.metadata', $query)->toArray();
	$count = count($cursor_count);
	$cursor = $client->executeQuery('pgd-db.metadata', $query);

   // La recherche renvoie-t-elle des r&eacute;sultats ?
   if( $count > 0 ){
		// Entête
		echo  '<div class="p-3 mb-2 bg-success text-white">Nombre de jeux de donn&eacute;es : '.$count.'</div>';
		echo '<table class="table table-hover">';
        echo '<thead>
				<tr>
					<th scope="col"></th>
					<th scope="col">Nom du jeu de donn&eacute;es</th>
					<th scope="col">Responsable scientifique</th>
					<th scope="col">Responsable technique</th>
					<th scope="col">Statut du jeu de donn&eacute;es</th>
					<th scope="col">Acc&egrave;s au jeu de donn&eacute;es</th>
					<th scope="col">M&eacute;tadonn&eacute;es</th>
				</tr>
			</thead>
			<tr>';

		$countdataset = 1;
		foreach($cursor as $key => $value) {
			foreach($value as $key => $val) {
				if ($key == "_id") { $idmetadata = $val; echo  "<td class=\"st\">".$countdataset."</td>"; $countdataset++; continue; }
				if (is_array($val)) {
					if (count($val) > 1) {
						$val1 = implode("<li>",$val)."</li>";
						echo "<td><li>".$val1."</td>";
					} else {
						$val1 = implode(" ",$val);
						echo "<td>".$val1."</td>";
					}
				} else {
					echo "<td>".$val."</td>";
				}
			}

			// Acces metadonnee
			echo "<td><a target=\"_blank\" href=\"metadata.php?id=$idmetadata\"><img src=\"../img/metadata-icon.png\"></a></td>";
			echo "</tr><tr>";
		}
        echo "</table>";
    } else {
		echo  '<div class="p-3 mb-2 bg-warning text-black">Pas de jeux de donn&eacute;es disponibles r&eacute;pondant à votre recherche ...';
	}
}

?>
