<?php
class controleur {
	
	public function accueil() {
		$lesCategories = (new categorie)->getAll();
		(new vue)->accueil($lesCategories);
	}
	
	public function erreur404() {
		$lesCategories = (new categorie)->getAll();
		(new vue)->erreur404($lesCategories);
	}

	public function connexion() {
		if(isset($_POST["ok"])) {
			if((new client)->connexionClient($_POST["email"], $_POST["motdepasse"]))
			{
				echo "Vous êtes bien connecté";
				header('Location: /');
			}
			else
			{
				$lesCategories = (new categorie)->getAll();
				(new vue)->connexion($lesCategories);
				echo "<a style='color: red;'>L'email ou le mot de passe est incorrect</a>";
			}
		}
		else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->connexion($lesCategories);
		}
	}

	public function inscription() {
		if(isset($_POST["ok"])) {
			if((new client)->estDejaInscrit($_POST["email"]))
			{
				echo "Le client existe déjà";
			}
			else{
				$lInscription = (new client)->inscriptionClient($_POST["nom"], $_POST["prenom"], $_POST["email"], $_POST["motdepasse"], $_POST["adresse"], $_POST["cp"], $_POST["ville"], $_POST["tel"]);
				header('Location: /');
			}
		}
		else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->inscription($lesCategories);
		}
	}

	public function produit() {
		if(isset($_GET["id"])) {
			$lesCategories = (new categorie)->getAll();
			$infosArticle = (new produit)->getInfosProduit($_GET["id"]);

			if(count($infosArticle) > 0) {
				$message = null;
				// Action du bouton ajouter au panier sur la page du produit
				if(isset($_POST["ajoutPanier"]) && isset($_POST["quantite"])) {
					if((new produit)->estDispoEnStock($_POST["quantite"], $_GET["id"])) {
						if(!(isset($_SESSION["panier"]))) {
							$_SESSION["panier"] = array();
						}
						for($i = 0; $i < $_POST["quantite"]; $i++) {
							array_push($_SESSION["panier"], $_GET["id"]);
						}

						// Message de succès à retourner à la vue
						$message = "<a style='color: green'>Le produit a été correctement ajouté au panier</a>";
					}
					else {
						// Message d'erreur à retourner à la vue
						$message = "<a style='color: red'>Le produit n'a pas pu être ajouté au panier en raison d'une rupture de stock</a>";
					}
				}

				(new vue)->produit($lesCategories, $infosArticle, $message);
			}
			else {
				(new vue)->erreur404($lesCategories);
			}
		}
		else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->erreur404($lesCategories);
		}
	}

	public function panier() {
		$lesCategories = (new categorie)->getAll();
		$lesArticles = array(); // Toutes les infos des produits du panier seront dans cette variable
		$doublonsPanier = array_unique($_SESSION["panier"]);

		// Récupérer toutes les infos des produits dans le panier
		foreach($doublonsPanier as $idDoublonsPanier)
		{
			array_push($lesArticles, (new produit)->getInfosProduit($idDoublonsPanier));
		}

		(new vue)->panier($lesCategories, $lesArticles, null);
	}

	public function commander() {
		if(isset($_POST["supprimer"])) {

			$panier = $_SESSION['panier'];

			$taillePanier = count($panier);
			
			for($i = 0; $i < $taillePanier; $i++) {
				
				if($panier[$i] == $_POST["supprimer"]) {
					
					unset($panier[$i]);
					
				}
				
			}

			$panier = array_values($panier);
			$_SESSION['panier'] = $panier;
			$this->panier();
		}

		if(isset($_POST["valider"])) {
			// Validation du panier

			/*
				On doit vérifier si l'utilisateur est connecté, si ce n'est pas le cas alors il faut l'inviter à se connecter.
				Si l'utilisateur est connecté alors il faut vérifier que la quantité commandée de chaque produit du panier soit disponbile en stock.
				Si tout est ok alors on créé sa commande dans la base et l'utilisateur doit être averti que sa commande est validée et le panier doit être vidé
				Sinon il faut revenir à la page du panier et avertir l'utilisateur quel produit (préciser sa désignation) pose problème.
			*/
			$lesCategories = (new categorie)->getAll();
			if($_SESSION["connexion"] != "") {
				
				if(!empty($_SESSION['panier'])) {
					
					$lePanier = array_count_values($_SESSION["panier"]);
					$laCommande = true;
					
					foreach ($lePanier as $produit => $quantite) {
						
						if((new produit)->estDispoEnStock($quantite, $produit)) 
						{
							
							if($laCommande) {
								
								(new vue)->commandeCorrect($lesCategories, "Votre commande à bien été prise en compte. Vous recevrez un mail incéssemment sous peu.");
								(new commande)->validerCommande($_SESSION['connexion'], array_count_values($_SESSION['panier']));
								(new produit)->retirerStockProduit($quantite, $produit);
								$_SESSION['panier'] = array();
								
							} 
							
							else 
								
							{
								
								(new vue)->erreurCommande($lesCategories, "Erreur lors de la commande !");
								
							}
						} 
						
						else 
							
						{
							
							$articleErreur = (new produit)->getInfosProduit($produit);
							(new vue)->erreurCommande($lesCategories, $articleErreur['designationProduit']. "n'a pas autant d'exemplaires");
							$passerCommande = false;
							break;
							
						}
					}
				} 
				
				else 
					
				{
					
					(new vue)->erreurCommande($lesCategories, "Veuillez ajouter des articles à votre panier pour pouvoir passer commande !");
					
				}

			}

			else 
			
			{
				
				(new vue)->erreurCommande($lesCategories, "Veuillez vous connecter avant de valider votre panier!");
				
			}
		}
	}

	public function categorie() {
		$lesCategories = (new categorie)->getAll();

		// Récupérer les articles et le nom de la catégorie
		$nomCategorie = (new categorie)->getNomCategorie($_GET["id"]);
		
		$lesArticles = (new categorie)->getProduits($_GET["id"]);

		(new vue)->categorie($lesCategories, $lesArticles, $nomCategorie);
	}

	public function deconnexion() {
		if(isset($_SESSION["connexion"])) {
			unset($_SESSION["connexion"]);
		}

		$this->accueil();
	}
}