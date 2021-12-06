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
		  { "tag-dispo": { $type: "array" } },
		  { "nom-organisme": { $type: "string" } },
		  { "nom-commune": { $type: "string" } },
		  { "latitude": { $type: "double" } },
		  { "longitude": { $type: "double" } },
		  { "nom-scientifique": { $type: "string" } },
		  { "email-scientifique": { $type: "string" } },
		  { "nom-technique": { $type: "string" } },
		  { "email-technique": { $type: "string" } },
		  { "statut-dispo": { $type: "string" } },
		  { "multi-datatype": { $type: "array" } },
		  { "multi-valorisation": { $type: "array" } },
		  { "diffusion": { $type: "string" } },
		  { "tag-projet": { $type: "array" } },
		  { "tag-espece": { $type: "array" } },
		  { "tag-annee": { $type: "array" } },
		  { "tag-financeur": { $type: "array" } },
		  { "description": { $type: "string" } },
		  { "chemin": { $type: "string" } }
		 ]
		}
	}
  );
};
