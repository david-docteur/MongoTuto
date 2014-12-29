<?php

try {
					// Nouvelle connexion à MongoDB
					// On s'identifie, on ne vas pas laisser libre accès comme certains :)
					$connexion = new MongoClient("mongodb://37.139.4.206/mongotuto", array("username" => "utilisateur", "password" => "St4ck0v3rfl0W")) or Die("Impossible de se connecter à la base de données MongoDB.");
					// Obtenir la base de données
					$collection = $connexion->mongotuto->exemples;
					$documents = $collection->insert(array('sujet' => 'agna', 'message' => 'gnagna'));
					$docs = $collection->find();
					foreach($docs as $doc) {
							print_r($doc) . "<br />";
					}
				} catch(Exception $ex) {
					$erreur = "L'envoi de l'e-mail a échoué. Problème avec le serveur SMTP ?" . $ex;
				}
				
?>
