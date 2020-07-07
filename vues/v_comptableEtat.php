<h3>Fiche de frais du mois <?php echo $numMois . "-" . $numAnnee ?> :

</h3>
<form action="index.php?uc=validfrais&action=MajFraisForfait" method="post">
    <div class="encadre">
        <p>
            Etat : <?php echo $libEtat ?> depuis le <?php echo $dateModif ?> <br> Montant validé : <?php echo $montantValide; ?>

        </p>
        <table class="listeLegere">
            <caption>Eléments forfaitisés </caption>
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $libelle = $unFraisForfait['libelle'];
                    ?>
                    <th> <?php echo $libelle ?></th>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $quantite = $unFraisForfait['quantite'];
                    $idFrais = $unFraisForfait['idfrais'];
                    $libelle = $unFraisForfait['libelle'];
                    ?>



                    <td class="qteForfait"><input size='8' type='text' name="lesFrais[<?php echo $idFrais ?>]" value='<?php echo $quantite; ?>' /></td>
                    <?php
                }
                ?>
            </tr>

        </table>
        <div>
            <p>
                <input id="ok" type="submit" value="Valider" size="20" />
                <input id="delete" type="submit" value="Effacer" size="20" />



            </p>
        </div>
        <table class="listeLegere">
            <caption>Descriptif des éléments hors forfait <?php echo $nb['nbr']; ?> justificatifs reçus <?php echo $just['nb'];?>
            </caption>
            <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>
                <th class='montant' colspan='2'>Action</th>
            </tr>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $date = $unFraisHorsForfait['date'];
                $libelle = $unFraisHorsForfait['libelle'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id'];
                $etat = $unFraisHorsForfait['statut'];
               
                ?>



                <?php
                if ($etat == 'R') {
                    echo "
                        <tr style='background-color:red;'>
                        <td> $date </td>
                        <td> REFUSE : $libelle</td>
                          <td>$montant </td>
                          <td><a href='index.php?uc=validfrais&action=majEtat&id=$id'>Supprimer</a></td>
                          <td><a href='index.php?uc=validfrais&action=report&id=$id'>reporter</a></td>


                        </tr>
                        ";
                } else {

                    echo "
                          <tr>
                            <td> $date </td>
                            <td>$libelle</td>
                              <td>$montant </td>

                        <td><a href='index.php?uc=validfrais&action=majEtat&id=$id'>Supprimer</a></td>
                        <td><a href='index.php?uc=validfrais&action=report&id=$id'>reporter</a></td>

                        </tr>
                        ";
                }
                ?>

                <?php
                ?>

                </tr>
                <?php
            }
            ?>
        </table>
</form>
<form action="index.php?uc=validfrais&action=validerfiche" method="post">
    <div>
        <p>
            <input id="valider" type="submit" value="Valider la fiche" size="20" />




        </p>
    </div>
</div>
</div>

