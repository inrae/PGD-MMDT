// Connexion Mongo
conn=new Mongo();
db=conn.getDB("admin");
db=db.getSiblingDB('admin');

// Test existence base mongo pgd-db
if (db.getMongo().getDBNames().indexOf("pgd-db") == -1){
	// Test existence user admin
	if (db.getUser("admin-mongo") == null){
		db.createUser( { user: "admin-mongo", pwd: "aaaaa", roles: [ { role:"userAdminAnyDatabase", db: "admin"}, "readWriteAnyDatabase" ]});
	}
	db=db.getSiblingDB('pgd-db');

	// Test existence user read-write
	if (db.getUser("userw-pgd") == null){
		db.createUser({user: "userw-pgd", pwd: "wwwww", roles: [ { role: "readWrite", db:"pgd-db" } ]});
	}
	// Test existence user read
	if (db.getUser("userr-pgd") == null){
		db.createUser({user: "userr-pgd", pwd: "rrrrr", roles: [ { role: "read", db:"pgd-db" } ]});
	}
	// Création collection metadata
	db.createCollection( "metadata",
	   {
		  validator: { $or:
			 [
				{ "title": { $type: "string" } },
				{ "statutdispo": { $type: "string" } },
				{ "statutconservation": { $type: "string" } },
				{ "diffusion": { $type: "string" } },
				{ "organism": { $type: "string" } },
				{ "respsci": { $type: "string" } },
				{ "mailsci": { $type: "string" } },
				{ "organismtech": { $type: "string" } },
				{ "resptech": { $type: "string" } },
				{ "mailtech": { $type: "string" } },
				{ "tag_financeur": { $type: "array" } },
				{ "tag_contrat": { $type: "array" } },
				{ "multivalorisation": { $type: "array" } },
				{ "tag_dispo": { $type: "array" } },
				{ "tag_site": { $type: "array" } },
				{ "nom_commune": { $type: "string" } },
				{ "latitude": { $type: "string" } },
				{ "longitude": { $type: "string" } },
				{ "tag_annee": { $type: "array" } },
				{ "tag_nvxbio": { $type: "array" } },
				{ "tag_organisme": { $type: "array" } },
				{ "tag_espece": { $type: "array" } },
				{ "tag_habitat": { $type: "array" } },
				{ "tag_dna": { $type: "array" } },
				{ "tag_pheno": { $type: "array" } },
				{ "tag_envt": { $type: "array" } },
				{ "tag_dynamique": { $type: "array" } },
				{ "tag_info": { $type: "array" } },
				{ "tag_autredonnee": { $type: "array" } },
				{ "description": { $type: "string" } },
				{ "chemin": { $type: "string" } }
			 ]
		  }
	   }
	);
};
