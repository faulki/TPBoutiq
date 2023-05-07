<?php

class vue {
	
	private $twig; // Instance de Twig disponible dans toute la classe

    public function __construct () {
        // Appelé au chargement de la vue (en faisant un new)

        $loader = new \Twig\Loader\FilesystemLoader('./templates');
        $this->twig = new \Twig\Environment($loader);
    }
	
	private function entete($lesCategories) {
		echo "
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset='UTF-8'>
					<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">

					<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">
					<link rel=\"stylesheet\" href=\"css/style.css\">

					<title>BOUTIQ</title>
				</head>
				<body>
				<nav class=\"navbar navbar-expand-lg navbar-dark bg-dark\">
					<a class=\"navbar-brand\" href=\"#\">BOUTIQ</a>
					<button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
						<span class=\"navbar-toggler-icon\"></span>
					</button>
				
					<div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent\">
						<ul class=\"navbar-nav mr-auto\">
							<li class=\"nav-item\">
								<a class=\"nav-link\" href=\"index.php?action=accueil\">
									Accueil
								</a>
							</li>
							
							<li class=\"nav-item dropdown\">
								<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdown\" role=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Catégories
								</a>
								<div class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown\">
			";
			
			foreach($lesCategories as $uneCategorie) {
				echo "<a class=\"dropdown-item\" href=\"index.php?action=categorie&id=".$uneCategorie["idCategorie"]."\">".$uneCategorie["nomCategorie"]."</a>";
			}			

			echo "
								</div>
							</li>
			";

			if(isset($_SESSION["connexion"])) {
				echo "
							<li class=\"nav-item\">
								<a class=\"nav-link\" href=\"index.php?action=deconnexion\">Déconnexion</a>
							</li>
				";
			}
			else {
				echo "
							<li class=\"nav-item\">
								<a class=\"nav-link\" href=\"index.php?action=connexion\">Connexion</a>
							</li>
							<li class=\"nav-item\">
								<a class=\"nav-link\" href=\"index.php?action=inscription\">Inscription</a>
							</li>
				";
			}
			
		echo "
							
						</ul>
						<ul class=\"my-2 my-lg-0 navbar-nav\">
							<li class=\"nav-item\" style=\"margin-left:20px;\">
								<a class=\"nav-link active\" href=\"index.php?action=panier\">
									Panier 
			";

		if(isset($_SESSION["panier"])) {
			echo "(".count($_SESSION["panier"]).")";
		}
		else {
			echo "(0)";
		}

		echo "
								</a>
							</li>
						</form>
					</div>
				</nav>
				<div id=\"content\">
		";
	}
	
	private function fin() {
		echo "
					</div>
					<script src=\"js/jquery-3.5.1.min.js\"></script>
					<script src=\"js/bootstrap.min.js\"></script>
				</body>
			</html>
		";
	}

	public function accueil($lesCategories) {
		$this->entete($lesCategories);

		echo "
			<h1>Bienvenue dans BOUTIQ !</h1>
		";

		$this->fin();
	}

	public function connexion($lesCategories) {
		$this->entete($lesCategories);

		echo "
			<form method='POST' action='index.php?action=connexion'>
				<h1>Se connecter :</h1>
				<br/>
				<div class=\"form-group\">
					<label for=\"email\">Adresse email</label>
					<input type=\"email\" name=\"email\" class=\"form-control\" id=\"email\" placeholder=\"name@example.com\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"motdepasse\">Mot de passe</label>
					<input type=\"password\" name=\"motdepasse\" class=\"form-control\" id=\"motdepasse\" placeholder=\"●●●●●●\" required>
				</div>
				<br/>
				<a href=\"index.php?action=inscription\">Vous n'êtes pas encore client ? Inscrivez-vous !</a>
				<br/>
				<br/>
				<br/>
				<button type=\"submit\" class=\"btn btn-primary\" name=\"ok\">Connexion</button>
			</form>
		";

		$this->fin();
	}

	public function inscription($lesCategories) {
		$this->entete($lesCategories);

		echo "
			<form method='POST' action='index.php?action=inscription'>
				<h1>S'inscrire :</h1>
				<br/>
				<div class=\"form-group\">
					<label for=\"nom\">Votre nom</label>
					<input type=\"text\" name=\"nom\" class=\"form-control\" id=\"nom\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"prenom\">Votre prénom</label>
					<input type=\"text\" name=\"prenom\" class=\"form-control\" id=\"prenom\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"email\">Adresse email</label>
					<input type=\"email\" name=\"email\" class=\"form-control\" id=\"email\" placeholder=\"name@example.com\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"motdepasse\">Mot de passe</label>
					<input type=\"password\" name=\"motdepasse\" class=\"form-control\" id=\"motdepasse\" placeholder=\"●●●●●●\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"motdepasse2\">Confirmer le mot de passe</label>
					<input type=\"password\" name=\"motdepasse2\" class=\"form-control\" id=\"motdepasse2\" placeholder=\"●●●●●●\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"adresse\">Votre adresse</label>
					<input type=\"text\" name=\"adresse\" class=\"form-control\" id=\"adresse\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"cp\">Votre code postal</label>
					<input type=\"text\" name=\"cp\" class=\"form-control\" id=\"cp\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"ville\">Votre ville</label>
					<input type=\"text\" name=\"ville\" class=\"form-control\" id=\"ville\" required>
				</div>
				<div class=\"form-group\">
					<label for=\"tel\">Votre numéro de téléphone (facultatif)</label>
					<input type=\"tel\" name=\"tel\" class=\"form-control\" id=\"tel\">
				</div>
				<br/>
				<a href=\"index.php?action=connexion\">Vous êtes déjà client ? Connectez-vous !</a>
				<br/>
				<br/>
				<br/>
				<button type=\"submit\" class=\"btn btn-primary\" name=\"ok\">Inscription</button>
			</form>
		";

		$this->fin();
	}

	public function produit($lesCategories, $infoArticle, $message) {
		$this->entete($lesCategories);

		echo $this->twig->render('produit.twig', ['lesCategories' => $lesCategories, 'infosArticle' => $infoArticle, 'message' => $message]);

		$this->fin();
	}

	public function panier($lesCategories, $lesArticles, $message) {
		$this->entete($lesCategories);

		echo "
			<h1>Panier :</h1>
			<form method='POST' action='index.php?action=commander'>
				<table style='border: 1px solid black;'>
					<tr style='border: 1px solid black;'>
						<th>Désignation des articles</th>
						<th>Prix unitaire</th>
						<th>Quantité</th>
						<th>TOTAL</th>
						<th>Supprimer</th>
					</tr>
		";

		// Créer un ligne du tableau pour chaque article du panier. Mettre un bouton supprimer dans la dernière colonne pour supprimer l'article du panier
					foreach($lesArticles as $value)
					{
						echo "<tr>
							<td>".$value["designationProduit"]."</td>
							<td>".$value["prixProduit"]."</td>
							<td>".array_count_values($_SESSION["panier"])[$value["codeProduit"]]."</td>
							<td>".(array_count_values($_SESSION["panier"])[$value["codeProduit"]])*$value["prixProduit"]."</td>
							<td><button type='submit' class=\"btn btn-danger\" value='".$value["codeProduit"]."' name=\"supprimer\">Supprimer</button></td>
							</tr>"
						;
					}
					echo "</tr>";

		echo "
				</table>

				<button type='submit' class=\"btn btn-primary\" name='valider'>Valider le panier</button>
			</form>
		";

		$this->fin();
	}

	public function categorie($lesCategories, $lesArticles, $nomCategorie) {
		$this->entete($lesCategories);

		echo "
			<h1>".$nomCategorie."</h1>

			<div class=\"container\">
				<div class=\"row row-cols-3\">
		";

		// Afficher les articles de la catégorie sous forme de grille (https://getbootstrap.com/docs/4.5/layout/grid/#row-columns) avec pour chaque article, à afficher : photo, désignation (avec lien pour aller sur la page du produit), prix
		
		foreach($lesArticles as $value)
		{
			echo " <form method='POST' action=''>
			
					<a style= 'text-decoration: none;' href='/index.php?action=produit&id=".$value["codeProduit"]."'>
					<div class='col'><img style='max-width: 100%; max-height= 100%;' src='/images/".$value["photoProduit"]."'/>
					<span>".$value["designationProduit"]."</span><br/><br/>
					<span style='font-weight: 500;'>".$value["prixProduit"]." €</span></div>
				  </a>
				  </form>";
		}

		echo "
				</div>
			</div>
		";

		$this->fin();
	}

	public function commandeValidee($lesCategories) {
		$this->entete($lesCategories);

		echo "
			<h1>Commande effectuée !</h1>
			<p>
				Votre commande a été validée avec succès !
			</p>
		";

		$this->fin();
	}

	public function erreur404($lesCategories) {
		http_response_code(404);

		$this->entete($lesCategories);

		echo "
			<h1>Erreur 404 : page introuvable !</h1>
			<br/>
			<p>
				Cette page n'existe pas ou a été supprimée !
			</p>
		";

		$this->fin();
	}

	public function commandeCorrect($lesCategories, $message) {
		$this->entete($lesCategories);
		echo "<a style='color: green'>".$message."</a>";
		$this->fin();
	}

	public function erreurCommande($lesCategories, $message) {
		$this->entete($lesCategories);
		echo "<a style='color: red'>".$message."</a>";
		$this->fin();
	}
}