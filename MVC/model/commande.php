<?php

class commande {
	
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
	
	// Récupérer toutes les commandes d'un client passé en paramètre
	public function getCommandesClient($client) {
		$sql = "SELECT * FROM commande WHERE idClient = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':id', $client, PDO::PARAM_INT);
		$req->execute();
		
		return $req->fetchAll();
	}
	
	// Permet de créer la commande du client passé en paramètre avec l'ensemble des articles qu'il a commandé en paramètre
	public function validerCommande($client, $lesArticles) {
		
		$sql = "INSERT INTO commande (dateCommande, idClient) VALUES (:laDateCommande, :leNumClient)";
		$req = $this->pdo->prepare($sql);
		$date = date('Y-m-d');
        $req->bindParam(':laDateCommande', $date, \PDO::PARAM_STR);
		$req->bindParam(':leNumClient', $client, \PDO::PARAM_INT);
        $req->execute();

		$numeroCommande = $this->pdo->lastInsertId();
		
		foreach ($lesArticles as $produit => $quantite) {
			
			$sql = "INSERT INTO commander (numeroCommande, codeProduit, quantite) VALUES (:leNumCommande, :leCodeProduit, :laQuantite)";
			$req = $this->pdo->prepare($sql);
			$req->bindParam(':leNumCommande', $numeroCommande, \PDO::PARAM_INT);
			$req->bindParam(':leCodeProduit', $produit, \PDO::PARAM_INT);
			$req->bindParam(':laQuantite', $quantite, \PDO::PARAM_INT);
			$req->execute();
			
		}
	}
}