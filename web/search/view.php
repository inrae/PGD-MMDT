<?php
require_once('../config/config.php');

$pag_content = '';
$pag_navigation = '';

//echo $_POST['param'];

if( isset( $_POST['param'] ) ){

	// Clause where pour requête mongo
    $where_search = array();
    $array_where = array();
    $array_freefield = ['description','organism','respsci','mailsci','resptech','mailtech','nom-commune','latitude','longitude', 'statutdispo', 'diffusion'];
    $array_multi = ['multivalorisation','tag_dispo','tag_site','tag_dna','tag_pheno','tag_info','tag_envt','tag_contrat', 'tag_espece', 'tag_annee', 'tag_financeur', 'tag_nvxbio'];

    // Décoder le json contenant les données à chercher
	$params = json_decode($_POST['param'], TRUE);

	// Parser le json de données saisies
	foreach($params as $key => $val) {
		if ($key == 'operator') {
			$operator = $val;
		}
		if (in_array($key, $array_freefield)) { # Champs libres ou balises select
			if( ! empty( $val) ){
				if ($key == 'statutdispo' || $key == 'diffusion') {
					if ($val == 'Tous'){
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
									"$key" => "Privé",
								),
						        array(
									"$key" => "Mixte",
						        ),
							));
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

	$where_search = array(
		"$operator" => $array_where
	);

    //* Rechercher tous les jeux de données répondant aux critères saisis */
	$options = [
		'projection' => [
			'title' => true, 'tag_dispo' => true, 'tag_contrat' => true, 'tag_site' => true, 'tag_espece' => true, 'tag_nvxbio' => true, 'organism' => true, 'respsci' => true, 'tag_financeur' => true, 'diffusion' => true, 'chemin' => true
		],
		'sort' => [
			'title, tag_dispo, tag_contrat, tag_site, tag_espece, tag_nvxbio, organism, respsci, tag_financeur, diffusion' => 1
		]
	];

	$query = new MongoDB\Driver\Query($where_search, $options);
	$cursor_count = $client->executeQuery('pgd-db.metadata', $query)->toArray();
	$count = count($cursor_count);
	$cursor = $client->executeQuery('pgd-db.metadata', $query);

   //* La recherche renvoie-t-elle des résultats ? */
   if( $count > 0 ){
		// Entête
		echo  '<div class="p-3 mb-2 bg-success text-white">Nombre de jeux de données : '.$count.'</div>';
		echo '<table class="table table-hover">';
        echo '<thead>
				<tr>
					<th scope="col"></th>
					<th scope="col">Contrat(s)</th>
					<th scope="col">Dispositif(s)</th>
					<th scope="col">Esp&egrave;ces</th>
					<th scope="col">Financeurs</th>
					<th scope="col">Echelle</th>
					<th scope="col">Site(s)</th>
					<th scope="col">Nom du jeu de données</th>
					<th scope="col">Organismes</th>
					<th scope="col">Resp. scientifique</th>
					<th scope="col">Diffusion</th>
					<th scope="col">Chemin</th>
				</tr>
			</thead>
			<tr>';

		$countdataset = 1;
		foreach($cursor as $key => $value) {
			foreach($value as $key => $val) {
				if ($key == "_id") { echo  "<td class=\"st\">".$countdataset."</td>"; $countdataset++; continue; }
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
			echo "</tr><tr>";
		}
        echo "</table>";
    } else {
		echo  '<div class="p-3 mb-2 bg-warning text-black">Pas de jeux de donn&eacute;es disponibles r&eacute;pondant à votre recherche ...';
	}
}

echo $pag_content;

?>
