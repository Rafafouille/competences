<?php
/*********************************************
	LISTE DE TOUTES LES BOITES DE DIALOGUE
**************************************************/

?>



		<!-- MESSAGE RETOUR --------------------------------->
		<div id="dialog-messageRetour" title="Message-retour">
		testsss
		</div>
		<script>
			$( "#dialog-messageRetour").dialog({
				autoOpen: false,
				modal: false,
				show: {
					effect: "blind",
					duration: 500
				  },
				 hide: {
					effect: "fade",
					duration: 500
				  },
				  position:{my:"center top",at:"center top"},
				  minHeight:50
			});
			$("#dialog-messageRetour").siblings('div.ui-dialog-titlebar').remove();//Supprime la barre de titre
		</script>


		<!-- BOITE AJOUTER UN UTILISATEUR ------------------- -->
		<div id="dialog-addUser" title="Ajouter/Mettre à jour un utilisateur">
			<form action="#" method="POST">
				<table>
					<tr>
						<td><label for="newUser_nom">Nom : </label></td>
						<td><input type="text" name="newUser_nom" id="newUser_nom" placeholder="Nom de l'élève" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_prenom"/>Prénom : </label></td>
						<td><input type="text" name="newUser_prenom" id="newUser_prenom" placeholder="Prénom de l'élève" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_classe"/>Classe : </label></td>
						<td><input type="text" name="newUser_classe" id="newUser_classe" placeholder="Classe" /></td>
					</tr>
					<tr>
						<td><label for="newUser_login"/>Nom d'utilisateur : </label></td>
						<td><input type="text" name="newUser_login" id="newUser_login" placeholder="Nom de connexion" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_psw"/>Mot de passe : </label></td>
						<td><input type="password" name="newUser_psw" id="newUser_psw" placeholder="(Secret)" required /></td>
					</tr>
				</table>
				<input type="hidden" name="newUser_id" id="newUser_id" value="-1"/>
			</form>
		</div>
		<script>
			$( "#dialog-addUser").dialog({
				autoOpen: false,
				modal: true,
				minWidth: 500,
				buttons: {
							"Ajouter/MAJ": function() {ajouteUpdateUser();$("#dialog-addUser").dialog( "close" );},
							"Annuler": function() {$("#dialog-addUser").dialog( "close" );}
						}
			});
		</script>
		

		<!-- BOITE UPGRADE UN UTILISATEUR ------------------- -->
		<div id="dialog-upgradeUser" title="Rendre super-Utilisateur">
			Voulez-vous vraiment augmenter l'utilisateur n°<span id="boiteUpgrade-id"></span> (<span id="boiteUpgrade-nom"></span>) ?
		</div>
		<script>
			$( "#dialog-upgradeUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-upgradeUser").dialog( "close" );upgradeUser($("#boiteUpgrade-id").text());},
							"Non": function() {$("#dialog-upgradeUser").dialog( "close" );}
						}
			});
		</script>
		
		<!-- BOITE DOWNGRADE UN UTILISATEUR ------------------- -->
		<div id="dialog-downgradeUser" title="Rendre Utilisateur Normal">
			Voulez-vous vraiment diminuer l'utilisateur n°<span id="boiteDowngrade-id"></span> (<span id="boiteDowngrade-nom"></span>) ?
		</div>
		<script>
			$( "#dialog-downgradeUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-downgradeUser").dialog( "close" );downgradeUser($("#boiteDowngrade-id").text());},
							"Non": function() {$("#dialog-downgradeUser").dialog( "close" );}
						}
			});
		</script>

		
		<!-- BOITE POUR SUPPRIMER UN UTILISATEUR ------------------- -->
		<div id="dialog-deleteUser" title="Supprimer l'utilisateur">
			Voulez-vous vraiment supprimer l'utilisateur n°<span id="dialog-deleteUser-id">0</span> (<span id="dialog-deleteUser-nom">inconnu</span>) ?
		</div>
		<script>
			$( "#dialog-deleteUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-deleteUser").dialog( "close" );supprimeUser(parseInt($("#dialog-deleteUser-id").text()));},
							"Non": function() {$("#dialog-deleteUser").dialog( "close" );}
						}
			});
		</script>
		
		<!-- BOITE POUR AJOUTER UN GROUPE DE COMPETENCES ------------------- -->
		<div id="dialog-addGroupeCompetences" title="Ajouter un groupe de compétences">
			<form>
				<label for="dialog-addGroupeCompetences-nom">Nom du groupe :</label>
				<input type="text" name="dialog-addGroupeCompetences-nom" id="dialog-addGroupeCompetences-nom" />
			</form>
		</div>
		<script>
			$( "#dialog-addGroupeCompetences").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Créer": function() {$("#dialog-addGroupeCompetences").dialog( "close" );addGroupeCompetences($("#dialog-addGroupeCompetences-nom").val());},
							"Annuler": function() {$("#dialog-addGroupeCompetences").dialog( "close" );}
						}
			});
		</script>
		
		
		<!-- BOITE POUR AJOUTER UNE COMPETENCE ------------------- -->
		<div id="dialog-addCompetence" title="Ajouter une compétence">
			<p>(Groupe : "<span class="dialog-addCompetence_nomGroupe"></span>")</p>
			<form>
				<label for="dialog-addCompetence-nom">Intitulé de la compétence :</label>
				<input type="text" name="dialog-addCompetence-nom" id="dialog-addCompetence-nom" />
				<input type="hidden" name="dialog-addCompetence-idGroupe" id="dialog-addCompetence-idGroupe"/>
			</form>
		</div>
		<script>
			$( "#dialog-addCompetence").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Ajouter": function() {$("#dialog-addCompetence").dialog( "close" );addCompetence($("#dialog-addCompetence-nom").val(),parseInt($("#dialog-addCompetence-idGroupe").val()));},
							"Annuler": function() {$("#dialog-addCompetence").dialog( "close" );}
						}
			});
		</script>
		
		<!-- BOITE POUR AJOUTER UN INDICATEUR------------------- -->
		<div id="dialog-addIndicateur" title="Ajouter un Indicateur">
			<p>(Compétence : "<span class="dialog-addIndicateur_nomCompetence"></span>")</p>
			<form>
				<label for="dialog-addIndicateur-nom">Nom :</label>
				<input type="text" name="dialog-addIndicateur-nom" id="dialog-addIndicateur-nom" />
				<br/>
				<label for="dialog-addIndicateur-details">Détails (facultatif) :</label><br/>
				<input type="text" name="dialog-addIndicateur-details" id="dialog-addIndicateur-details" />
				<br/>
				<label for="dialog-addIndicateur-niveaux">Nombre de niveaux :</label>
				<select name="dialog-addIndicateur-niveaux" id="dialog-addIndicateur-niveaux">
					<?php
						for($i=1;$i<=$NB_NIVEAUX_MAX;$i++)
							{echo'
					<option value="'.$i.'"';
							if($i==$NIVEAU_DEFAUT)
								echo " selected";
							echo'>'.$i.'</option>';
							};
					?>
				</select>

				<input type="hidden" name="dialog-addIndicateur-idCompetence" id="dialog-addIndicateur-idCompetence"/>
			</form>
		</div>
		<script>
			$( "#dialog-addIndicateur").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Ajouter": function() {$("#dialog-addIndicateur").dialog( "close" );addIndicateur($("#dialog-addIndicateur-nom").val(),$('#dialog-addIndicateur-details').val(),$('#dialog-addIndicateur-niveaux').val(),parseInt($("#dialog-addIndicateur-idCompetence").val()));},
							"Annuler": function() {$("#dialog-addIndicateur").dialog( "close" );}
						}
			});
		</script>
		
		
		<!-- BOITE ERREUR ------------------- -->
		<div id="dialog-error" title="Erreur">
			Erreur.
		</div>
		<script>
			$( "#dialog-error").dialog({
				autoOpen: false,
				modal: true
			});
		</script>