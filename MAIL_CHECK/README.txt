Il faudrait qu�on puisse importer un CSV et qu�on ressorte un autre CSV propre.
 
Voil� les contr�le que je vois :
o	R��criture des domaines les plus connus (ex. gmail.fr -> gmail.com) 
o	Function str_replace sur les gmil.com, outlouck, wanadou, aliseadsl,�
o	Je te ferrais une liste de ceux que je rencontre souvent
o	Uniformiser la case sur les pr�noms et les noms 
o	Majuscule en d�but et minuscule pour le reste (en tenant compte des espaces et tirets)
o	Supprimer les caract�res non tol�r�s sur l�email 
o	Remplacement des accents
o	Suppression des espaces ou tabulations
o	Suppression des � et "
o	Remplacer les ; par des .
o	Remplacer les _ par des tirets dans les domaines
o	Cr�er une colonne type � a corriger � dans le csv pour les noter les lignes ou on retrouve des caract�res sp�ciaux <, .., % , etc
-	Supprimer les doublons
o	Supprimer les lignes si pas d�adresse email
 
L�id�e apr�s traitement serait d�afficher le nombre d�adresse modifi�e et le nombre d�adresse supprim�e et enfin un bouton pour t�l�charger le nouveau fichier.
