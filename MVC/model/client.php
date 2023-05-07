<?php

class client {
	
	// Objet PDO servant à la connexion à la base
	private $pdo;

	// Connexion à la base de données
	public function __construct() {
		$config = parse_ini_file("config.ini");
		
		try {
			$this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	// Connexion d'un client (vérification email + mot de passe)
	public function connexionClient($email, $mdp) {
		$sql = "SELECT idClient, motDePasseClient FROM client WHERE emailClient = :mail";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':mail', $email, PDO::PARAM_STR);
		$req->execute();
		
		$ligne = $req->fetch();

		if($ligne != false) {
			// Client existant

			// On vérifie si le hash du mot de passe stocké dans la base correspond au mot de passe saisi dans le formulaire
			if(password_verify($mdp, $ligne["motDePasseClient"])) {
				// Connexion vérifiée
				$_SESSION["connexion"] = $ligne["idClient"];
				return true;
			}
			else {
				// Mot de passe incorrect
				return false;
			}
		}
		else {
			// Client inconnu
			return false;
		}
	}

	// Vérifie si le client est déjà inscrit
	public function estDejaInscrit($email) {
		$sql = "SELECT COUNT(*) AS nombre FROM client WHERE emailClient = :mail";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':mail', $email, PDO::PARAM_STR);
		$req->execute();
		
		$ligne = $req->fetch();

		if($ligne["nombre"] == 0) {
			// Pas de compte trouvé à l'adresse mail indiquée
			return false;
		}
		else {
			// Compte trouvé à l'adresse mail indiquée
			return true;
		}
	}

	// Récupérer les infos d'un client
	public function getInfosClient($leClient) {
		$sql = "SELECT nomClient, prenomClient, emailClient, telClient FROM client WHERE nomClient = :leNomClient";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':leNomClient', $leClient, PDO::PARAM_STR);
		$req->execute();
		
		$ligne = $req->fetch();
		
		if($ligne["nomClient"])
		{
			return $ligne["nomClient"]." - ".$ligne["prenomClient"]." - ".$ligne["emailClient"]." - ".$ligne["telClient"];
		}
		else
		{
			return "Ce client n'existe pas";
		}
	}

	// Inscrire un client
	public function inscriptionClient($nom, $prenom, $email, $motDePasse, $rue, $cp, $ville, $tel) {
		
			$sql = "INSERT INTO client (nomClient, prenomClient, emailClient, motDePasseClient, rueClient, cpClient, villeClient, telClient) VALUES (:leNom, :lePrenom, :lEmail, :leMotDePasse, :laRue, :leCp, :laVille, :leTel)";
			
			$req = $this->pdo->prepare($sql);
			
			$req->bindParam(':leNom', $nom, PDO::PARAM_STR);
			$req->bindParam(':lePrenom', $prenom, PDO::PARAM_STR);
			$req->bindParam(':lEmail', $email, PDO::PARAM_STR);
			$mdphash = password_hash($motDePasse, PASSWORD_BCRYPT);
			$req->bindParam(':leMotDePasse', $mdphash, PDO::PARAM_STR);
			$req->bindParam(':laRue', $rue, PDO::PARAM_STR);
			$req->bindParam(':leCp', $cp, PDO::PARAM_STR);
			$req->bindParam(':laVille', $ville, PDO::PARAM_STR);
			$req->bindParam(':leTel', $tel, PDO::PARAM_STR);
			
			var_dump($req->errorInfo());
			$req->debugDumpParams();
			
			$req->execute();
	}
}