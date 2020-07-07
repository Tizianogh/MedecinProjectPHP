<?php
include("vues/v_sommairecomptable.php");
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
switch ($action) {
    case 'validerfrais': {
            $lesvisiteurs = $pdo->getVisiteurs();
            $les6 = $pdo->get6();
            include('vues/v_comptablemois.php');
            break;
        }
    case 'voirEtatFrais': {
            $lesvisiteurs = $pdo->getVisiteurs();
            $les6 = $pdo->get6();
            $mois = $_REQUEST['lstMois'];
            $_SESSION['mois'] = $mois;
            $numMois = substr($mois, -2);
            $numAnnee = substr($mois, 0, -2);
            $idVisiteur = $_REQUEST['visieurs'];
            $nb = $pdo->nombrejust($idVisiteur,$mois);
             $just = $pdo->getNbJust($idVisiteur, $mois);
            $_SESSION['visieurs'] = $idVisiteur;
            include("vues/v_comptablemois.php");
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            $pdo->majTot($idVisiteur, $mois);
            include("vues/v_comptableEtat.php");
            break;
        }
    case 'MajFraisForfait': {
            $lesFrais = $_REQUEST['lesFrais'];
            $mois = $_SESSION['mois'];
            $idVisiteur = $_SESSION['visieurs'];
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
                $pdo->majTot($idVisiteur, $mois);
                $lesvisiteurs = $pdo->getVisiteurs();
                $les6 = $pdo->get6();
                $_SESSION['mois'] = $mois;
                $numMois = substr($mois, -2);
                $numAnnee = substr($mois, 0, -2);
                $nb = $pdo->nombrejust($idVisiteur);
                $_SESSION['visieurs'] = $idVisiteur;
                include("vues/v_comptablemois.php");
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
                $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
                $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
                $libEtat = $lesInfosFicheFrais['libEtat'];
                $montantValide = $lesInfosFicheFrais['montantValide'];
                $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
                $dateModif = $lesInfosFicheFrais['dateModif'];
                $dateModif = dateAnglaisVersFrancais($dateModif);
                include("vues/v_comptableEtat.php");
                break;
            } else {
                ajouterErreur("Les valeurs des frais doivent être numériques");
                include("vues/v_erreurs.php");
            }
            break;
        }
    case 'majEtat': {
            $id = $_REQUEST['id'];
            $idVisiteur = $_SESSION['visieurs'];
            $mois = $_SESSION['mois'];
            $pdo->majStatut($id);
            $pdo->majTot($idVisiteur, $mois);
            $lesvisiteurs = $pdo->getVisiteurs();
            $les6 = $pdo->get6();
            $_SESSION['mois'] = $mois;
            $numMois = substr($mois, -2);
            $numAnnee = substr($mois, 0, -2);
            include("vues/v_comptablemois.php");
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_comptableEtat.php");
            break;
        }
    case 'report': {
            $id = $_REQUEST['id'];
            $mois = $_SESSION['mois'];
            $idVisiteur = $_SESSION['visieurs'];
            $pdo->report($id);
            $pdo->majTot($idVisiteur, $mois);
            $lesvisiteurs = $pdo->getVisiteurs();
            $les6 = $pdo->get6();
            $_SESSION['mois'] = $mois;
            $numMois = substr($mois, -2);
            $numAnnee = substr($mois, 0, -2);
            include("vues/v_comptablemois.php");
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_comptableEtat.php");
            break;
        }

    case 'validerfiche': {
            $idVisiteur = $_SESSION['visieurs'];
            $mois = $_SESSION['mois'];
            $pdo->validerFiche($idVisiteur, $mois);
            $lesvisiteurs = $pdo->getVisiteurs();
            $les6 = $pdo->get6();
            $_SESSION['mois'] = $mois;
            $numMois = substr($mois, -2);
            $numAnnee = substr($mois, 0, -2);
            include("vues/v_comptablemois.php");
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_comptableEtat.php");

            break;
        }
}
