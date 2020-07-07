<?php

include("vues/v_sommairecomptable.php");
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];

switch ($action) {
    case 'suivre': {
            $lesvisiteurs = $pdo->getVisiteursVA();
            include('vues/v_visiteurEtMois.php');
            break;
        }
    case 'voirEtatFrais': {
            $lesvisiteurs = $pdo->getVisiteursVA();
            include('vues/v_visiteurEtMois.php');
            $idetmois = $_REQUEST['visieurs'];
            $mois = substr($idetmois, -6);
            $_SESSION['mois'] = $mois;
            $idVisiteur = substr($idetmois, 0, -6);
            $_SESSION['idVisiteur'] = $idVisiteur;
            $numMois = substr($mois, -2);
            $numAnnee = substr($mois, 0, -2);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $nb = $pdo->nombrejust($idVisiteur, $mois);
            $just = $pdo->getNbJust($idVisiteur, $mois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $montantValide = $pdo->sommetot($idVisiteur, $mois);
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_followetat.php");
            break;
        }
    case 'validFiche': {
            $idVisiteur = $_SESSION['idVisiteur'];
            $mois = $_SESSION['mois'];
            $validFiche = $pdo->validerFicheRB($idVisiteur, $mois);
            $lesvisiteurs = $pdo->getVisiteursVA();
            include('vues/v_visiteurEtMois.php');
        }
}