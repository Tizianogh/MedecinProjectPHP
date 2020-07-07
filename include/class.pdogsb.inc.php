<?php

/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsbv2';
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        PdoGsb::$monPdo = null;
    }

    public function get6() {

        $arr = array();
        for ($i = 0; $i <= 5; $i++) {
            $date = date("m/Y", mktime(0, 0, 0, date("m") - $i, date("d"), date("Y")));
            $dateKey = date("Ym", mktime(0, 0, 0, date("m") - $i, date("d"), date("Y")));
            $arr[$dateKey] = $date;
        }
        return $arr;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $req = "select * from visiteur
		where visiteur.login='$login' and visiteur.mdp='$mdp' ";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    public function getVisiteurs() {

        $result = PdoGsb::$monPdo->query('SELECT DISTINCT visiteur.id, visiteur.nom, visiteur.prenom FROM fichefrais, visiteur WHERE fichefrais.idVisiteur=visiteur.id AND fichefrais.idEtat="CR"');

        $ligne = $result->fetchAll();
        return $ligne;
    }

    public function ValidFiche($prenom, $nom, $date) {

        $result = PdoGsb::$monPdo->query("SELECT f.idEtat FROM visiteur v, fichefrais f WHERE v.id=f.idVisiteur AND v.nom ='$nom' AND v.prenom='$prenom' AND f.mois='$date'");

        $ligne = $result->fetchAll();
        if (count($ligne) == 0) {
            echo 'Aucune fiche de disponible à cette date pour cet utilisateur';
        }
        return $ligne;
    }

    public function getId($prenom, $nom) {

        $result = PdoGsb::$monPdo->query("SELECT id FROM visiteur WHERE nom='$nom' AND prenom ='$prenom'");



        return $result;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur'
		and lignefraishorsforfait.mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle,
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois'
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait(idVisiteur,mois,libelle,date,montant)
		values('$idVisiteur','$mois','$libelle','$dateFr','$montant')";

        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs,
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    public function majStatut($id) {
        $req = "UPDATE ligneFraisHorsForfait SET statut = 'R' WHERE id='$id'";
        PdoGsb::$monPdo->exec($req);
    }

    //nbr d'éléments hors forfait.
    public function nombrejust($idVisiteur, $mois) {
        $req = "SELECT COUNT(*)AS nbr FROM lignefraishorsforfait where idVisiteur='$idVisiteur' and mois='$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $result = $res->fetch();

        return $result;
    }

    public function getNbJust($idVisiteur, $mois) {
        $req = "SELECT nbJustificatifs as nb FROM fichefrais where idVisiteur='$idVisiteur' and mois='$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $result = $res->fetch();
        return $result;
    }

    public function report($id) {

        // RECUPERATION DU MOIS, AJOUT D'UN MOIS EN PLUS ET TRANSFORMATION AU FORMAT SOUHAITE
        $req = "SELECT * FROM lignefraishorsforfait WHERE id='$id'";
        $res = PdoGsb::$monPdo->query($req);
        $result = $res->fetchAll();
        foreach ($result as $unedate) {
            $idVisiteur = $unedate['idVisiteur'];
            $libelle = $unedate['libelle'];
            $datemod = $unedate['date'];
            $montant = $unedate['montant'];
            $test = $unedate['mois'];
            $statut = $unedate['statut'];
        }
        $datenew = date($test);
        $numAnnee = substr($datenew, 0, 4);
        $numMois = substr($datenew, 4, 2);
        $date2 = $numAnnee . '-' . $numMois . '-' . '01';
        $date = new DateTime($date2);
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $datetab = $date->format('Ym');



        /////////////////////////////////////////////////////////////////////////////
        // SUPRESSION DE LA LIGNE EN QUESTIONN
        $sql = "DELETE FROM lignefraishorsforfait WHERE id = '$id'";
        PdoGsb::$monPdo->exec($sql);


        $dateauj = date("Y-m-d");
        $sql3 = "INSERT INTO ficheFrais (idVisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) VALUES
              ('$idVisiteur', '$datetab','0','0','$dateauj','CR')";
        $result = PdoGsb::$monPdo->exec($sql3);

        $sql4 = "INSERT INTO ligneFraisHorsForfait (idVisiteur,mois,libelle,date,montant,statut) values
           ('$idVisiteur','$datetab','$libelle','$datemod','$montant','$statut')";
        PdoGsb::$monPdo->exec($sql4);
    }

    public function getVisiteursVA() {
        $req = 'SELECT ficheFrais.idVisiteur,visiteur.nom,visiteur.prenom, fichefrais.mois
          FROM visiteur, fichefrais
          WHERE fichefrais.idVisiteur=visiteur.id
          AND fichefrais.idEtat="VA"';

        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetchAll();


        return $laLigne;
    }

    public function sommetot($idVisiteur, $mois) {

        $req = "SELECT fraisforfait.id, fraisforfait.montant, fraisforfait.montant*lignefraisforfait.quantite
          as total FROM fraisforfait,lignefraisforfait WHERE
          fraisforfait.id=lignefraisforfait.idFraisForfait
          and lignefraisforfait.idVisiteur='$idVisiteur' AND lignefraisforfait.mois='$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $somme = 0;
        foreach ($res as $unres) {

            $somme += $unres['total'];
        }
        $req2 = "SELECT lignefraishorsforfait.montant FROM lignefraishorsforfait
              WHERE lignefraishorsforfait.idVisiteur='$idVisiteur' AND lignefraishorsforfait.mois='$mois' ";
        $res2 = PdoGsb::$monPdo->query($req2);
        $sommehf = 0;
        foreach ($res2 as $montant) {
            $sommehf += $montant['montant'];
        }

        return $somme + $sommehf;
    }

    public function validerFiche($idVisiteur, $mois) {

        $tot = $this->sommetot($idVisiteur, $mois);
        $req = "UPDATE fichefrais SET montantValide='$tot', idEtat='VA' WHERE idVisiteur='$idVisiteur' AND mois='$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    public function validerFicheRB($idVisiteur, $mois) {
        $req = "UPDATE fichefrais SET idEtat='RB' WHERE idVisiteur='$idVisiteur' AND mois='$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    public function majTot($idVisiteur, $mois) {

        $tot = $this->sommetot($idVisiteur, $mois);
        $req = "UPDATE fichefrais SET montantValide='$tot' WHERE idVisiteur='$idVisiteur' AND mois='$mois'";
        PdoGsb::$monPdo->exec($req);
    }

}
