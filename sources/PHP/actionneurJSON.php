<?php
header('Content-type: application/json');
include_once('options.php');
include_once('fonctions.php');

initSession();


/* ******************************
	Programme pour ajax (avec réponse JSON)
****************************** */




//Format de la réponse
/* ***************************** */
$reponseJSON=initReponseJSON();

$action="";
if(isset($_POST['action'])) $action=$_POST['action'];



//CONNECTION =================
// Action qui connecte (i.e. met à jour les variables de session)
// si le couple (utilisateur/mot de passe) est dans la BDD
if($action=="login")
{
	connectToBDD();
	
	//On récupère les variables envoyées
	$login="";
	if(isset($_POST['login'])) $login=$_POST['login'];
	$mdp="";
	if(isset($_POST['mdp'])) $mdp=$_POST['mdp'];
	
	if($login!="" && $mdp!="")	//Si les paramètres ne sont pas vides
	{
		$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE login=:login  AND mdp = :mdp');
		$req->execute(array('login' => $login, 'mdp' => $mdp));
		if($donnees = $req->fetch())	//Si l'utilisateur est dans la BDD, avec le bon mot de passe
		{
			$_SESSION['nom']=$donnees['nom'];
			$_SESSION['prenom']=$donnees['prenom'];
			$_SESSION['statut']=$donnees['statut'];
			$_SESSION['id']=$donnees['id'];
			$reponseJSON["messageRetour"]=":)Vous êtes connecté. Bonjour ".$_SESSION['prenom']." ".$_SESSION['nom']." !";
		}
		else	//Si le couple (utilisateur<->mot de passe) n'est pas trouvé...
			$reponseJSON["messageRetour"]=":(L'identifiant ou le mot de passe est incorrect.";

		$req->closeCursor();//Fin des requêtes
	}
	else	//Si le mot de passe ou l'identifiant est vide
		$reponseJSON["messageRetour"]=":(L'identifiant ou le mot de passe est vide.";
}

// LOGOUT ============================================
// Action qui délogue (met à jour les variables de session)
// l'utilisateur
if($action=="logout")
{
	$_SESSION['nom']="";
	$_SESSION['prenom']="";
	$_SESSION['statut']="";
	$reponseJSON["messageRetour"]=":)Vous êtes déconnecté. Au revoir !";
}


// =====================================================
// FONCTIONS GENERALES
// =====================================================

//Renvoie la liste des classes*************************
if($action=="getListeClasses")
{
	connectToBDD();
	$reponseJSON['listeClasses']=array();
	$reponse = $bdd->query('SELECT DISTINCT(classe) FROM utilisateurs WHERE classe<>""');
	while ($donnees = $reponse->fetch())
		array_push($reponseJSON["listeClasses"],$donnees["classe"]);
}

// =====================================================
// ADMINISTRATION UTILISATEURS
// =====================================================


//LISTE DES UTILISATEURS *****************************
if($action=="getUsersList")
{
	if($_SESSION['statut']=="admin")
	{
		//Critères de sélection
		$classe="[ALL]";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];

		$critere="";
		if($classe!="[ALL]")
			$critere=' WHERE classe="'.$classe.'"';

		//Requete SQL
		connectToBDD();
		$reponse = $bdd->query('SELECT * FROM utilisateurs'.$critere);
		$reponseJSON["listeUsers"]=array();
		while ($donnees = $reponse->fetch())
		{
			$tabUser=array();
			$tabUser['id']=$donnees['id'];
			$tabUser['nom']=$donnees['nom'];
			$tabUser['prenom']=$donnees['prenom'];
			$tabUser['login']=$donnees['login'];
			$tabUser['classe']=$donnees['classe'];
			$tabUser['statut']=$donnees['statut'];
			$tabUser['mail']=$donnees['mail'];
			$tabUser['notifieMail']=$donnees['notifieMail'];
			array_push($reponseJSON["listeUsers"],$tabUser);
			$reponseJSON["messageRetour"]=":XListe des utilisateurs récupérée";
		}
	}
	else//Si pas admin
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas récupérer la liste des utilisateurs";
}


//AJOUT D'UN UTILISATEUR*************************
if($action=="addUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$tableau=array(
				'nom' => $_POST["newUser_nom"],
				'prenom' => $_POST['newUser_prenom'],
				'login' => $_POST['newUser_login'],
				'mdp' => $_POST['newUser_psw'],
				'classe' => $_POST['newUser_classe']
			);
		$req = $bdd->prepare('SELECT id FROM utilisateurs WHERE login=:login');
		$req->execute(array('login'=>$_POST['newUser_login']));
		if($donnees=$req->fetch())//Si le login existe déjà
			$reponseJSON["messageRetour"]=":(Le login \"".$_POST["newUser_login"]."\" existe déjà !";
		else
		{
			$req2 = $bdd->prepare('INSERT INTO utilisateurs(nom, prenom, login, mdp, classe) VALUES(:nom, :prenom, :login, :mdp, :classe)');
			$req2->execute($tableau);
			$reponseJSON["messageRetour"]=":)L'utilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été ajouté !";
		}
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'ajouter un utilisateur !";
	}
}



//UPDATE UN UTILISATEUR*************************
if($action=="updateUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		//Tableau de valeurs
		$tableau=array(
				'nom' => $_POST["newUser_nom"],
				'prenom' => $_POST['newUser_prenom'],
				'login' => $_POST['newUser_login'],
				'mdp' => $_POST['newUser_psw'],
				'classe' => $_POST['newUser_classe'],
				'id'	=>	$_POST['id']
			);
		
		//Vérification que le login n'existe pas déja en cas de changement
		$reponseJSON["debug"]=$_POST['newUser_login'];
		$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE login=":login"');// AND id<>:id');
		$req->execute($tableau);
		if($donnees=$req->fetch())
			$reponseJSON["messageRetour"]=":(Le nom d'utilisateur existe déjà";
		else
		{
			//Modifications
			if($_POST['newUser_psw']!="")//Si un nouveau mot de passe est proposé
				$req = $bdd->prepare('UPDATE utilisateurs SET nom=:nom, prenom=:prenom, mdp=:mdp login=:login, classe=:classe WHERE id=:id');
			else
				$req = $bdd->prepare('UPDATE utilisateurs SET nom=:nom, prenom=:prenom, login=:login, classe=:classe WHERE id=:id');
			$req->execute($tableau);
			$reponseJSON["messageRetour"]=":)L'uutilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été mis à jour !";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier un utilisateur !";
}




// =====================================================
// ADMINISTRATION COMPETENCES
// =====================================================


//Update liste des compétences
if($action=="updateCompetencesSelonClasse")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];

		$reponseJSON['listeGroupes']=array();
		
		//if($classe!="[ALL]")
		//	$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM (SELECT * FROM indicateurs AS i JOIN liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."') as ind JOIN (SELECT co.id AS idComp, co.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS co JOIN groupes_competences AS gr ON  co.groupe=gr.id) AS E1 ON ind.competence = E1.idComp";
		//else

		//On recupere toutes les compétences
			$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM indicateurs as ind JOIN (SELECT co.id AS idComp, co.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS co JOIN groupes_competences AS gr ON  co.groupe=gr.id) AS E1 ON ind.competence = E1.idComp";

		$req = $bdd->query($requete);
		while($reponse=$req->fetch())
		{
			$idGroup=intval($reponse['idGroup']);
			$nomGroup=$reponse['nomGroup'];

			$idComp=intval($reponse['idComp']);
			$nomComp=$reponse['nomComp'];

			$idInd=intval($reponse['idInd']);
			$nomInd=$reponse['nomInd'];
			$detailsInd=$reponse['detailsInd'];
			$niveauxInd=intval($reponse['niveauxInd']);

			//Si le groupe n'existe pas, on le crée
			if(!isset($reponseJSON['listeGroupes'][$idGroup]))
			{
				$reponseJSON['listeGroupes'][$idGroup]["id"]=$idGroup;
				$reponseJSON['listeGroupes'][$idGroup]["nom"]=$nomGroup;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"]=array();
				$reponseJSON['listeGroupes'][$idGroup]["selected"]=false;
			}
			//Si la compétence n'existe pas...
			if(!isset($reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]))
			{
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["id"]=$idComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nom"]=$nomComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=false;
			}

			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveaux"]=$niveauxInd;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=false;
		}	


		//On tag celles qui sont dans la classe souhaitée
		$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM (SELECT * FROM indicateurs AS i JOIN liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."') as ind JOIN (SELECT co.id AS idComp, co.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS co JOIN groupes_competences AS gr ON  co.groupe=gr.id) AS E1 ON ind.competence = E1.idComp";
		$req = $bdd->query($requete);
		while($reponse=$req->fetch())
		{
			$reponseJSON['listeGroupes'][$idGroup]["selected"]=true;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=true;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=true;
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'obtenir la liste des compétences !";

}



//Update liste des compétences
if($action=="lierDelierIndicateurClasse")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];
		$indicateur=0;
		if(isset($_POST['indicateur'])) $indicateur=intval($_POST['indicateur']);
		$lier="";
		if(isset($_POST['lier'])) $lier=$_POST['lier'];

		$reponseJSON["indicateur"]=$indicateur;
		$reponseJSON["classe"]=$classe;

		if($classe!="" and $indicateur!=0 and $lier!="")
		{
			if($lier=="true")
			{
				$requete = $bdd->prepare('INSERT INTO liensClassesIndicateurs(indicateur, classe) VALUES(:indicateur, :classe)');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":)Lier";
				$reponseJSON["lier"]=true;
			}
			else	
			{
				$requete = $bdd->prepare('DELETE FROM liensClassesIndicateurs WHERE indicateur=:indicateur AND classe=:classe');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":)Délier";
				$reponseJSON["lier"]=false;
			}
		}
		else
		{
			$reponseJSON["messageRetour"]=":(Il manque des infos dans la liaison indicateur<->classe !";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de lier/délier une compétence !";

}



echo json_encode($reponseJSON);

?>