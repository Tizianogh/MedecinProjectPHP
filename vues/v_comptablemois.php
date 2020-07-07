 <div id="contenu">
   <h2>Validation des fiches de frais</h2>

   <form action="index.php?uc=validfrais&action=voirEtatFrais" method="post">
     <div class="corpsForm">
       <p>

         <label for="visiteurs" accesskey="n">Visiteur : </label>
         <select id="visiteurs" name="visieurs">
           <?php
            foreach ($lesvisiteurs as $unVisiteur) {



              ?>

             <?php
              echo '<option value="' . $unVisiteur['id'] . '"';
              if (isset($_POST['visieurs']) && $_POST['visieurs'] == $unVisiteur['id']) echo 'selected="selected"';
              echo ">" . $unVisiteur['prenom'] . ' ' . $unVisiteur['nom'] . "</option>'";
              ?>


           <?php
          }
          ?>


         </select>



       </p>
       <p>

         <label for="lstMois" accesskey="n">Mois : </label>
         <select id="lstMois" name="lstMois">

           <?php
            foreach ($les6 as $key => $six) {


              ?>

             <?php
              echo '<option value="' . $key . '"';
              if (isset($_POST['lstMois']) && $_POST['lstMois'] == $key) echo 'selected="selected"';
              echo ">" . $six . "</option>'";

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