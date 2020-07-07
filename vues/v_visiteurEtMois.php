<h2>Suivre le paiement des fiches de frais</h2>

<form action="index.php?uc=followfiche&action=voirEtatFrais" method="post">
<div class="corpsForm">
    <p>

   <label for="visiteurs" accesskey="n">Visiteur : </label>
<select id="visiteurs" name="visieurs">
<?php
  foreach ($lesvisiteurs as $unVisiteur) {

    $mois=$unVisiteur['mois'];
    $rest = substr($mois, -2);
    $restt = substr($mois,0,4);
    $moiss=$rest.'/'.$restt;

  ?>

  <?php
    echo '<option value="'.$unVisiteur['idVisiteur'].$unVisiteur['mois'].'"';    
    if (isset($_POST['visieurs']) && $_POST['visieurs'] == $unVisiteur['idVisiteur'].$moiss) echo 'selected="selected"';
    echo ">".$moiss.' - '.$unVisiteur['prenom'].' '.$unVisiteur['nom']."</option>'";    
   ?>


<?php
  }  
?>


   </select>
    </p>
</div>
<div class="piedForm">
<p>
  <input id="ok" type="submit" value="Valider" size="20" />



</p>
</div>

</form>
