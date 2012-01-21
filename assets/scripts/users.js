var user_id = 0;

jQuery(document).ready(function() {

  // Validation du formulaire pour l'édition d'utilisateur
  $("#edit_user_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      url   : site_url+'users/save/'+user_id,
      data  : $(this).serializeArray(),
      success: function(data) {
        $.fancybox.hideActivity();

        // Mise à jour de la liste des utilisateurs
        $("#users-list div.content").load(site_url+'users/get_list');

        // Message retourné (erreur ou ok)
        $.jGrowl(data.message);
      }
    });

    return false;
  });

  // Edition d'un utlisateur depuis la liste
  $("#edit-user-button").live('click', function(e) {
    user_id = $(this).attr('tabindex');

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      cache : false,
      url   : site_url+'users/edit/'+user_id,
      success: function(string) {
        $.fancybox.hideActivity();
        $("#users-list div.content").html(string);
      }
    });

    return false;
  });

  // Annule les modifications d'un utlisateur et retourne à la liste
  $("#cancel-button").live('click', function(e) {
      // Chargement de la liste des utilisateurs
      $("#users-list div.content").load(site_url+'users/get_list');

      return false;
  });

  // Suppression d'un utlisateur depuis la liste
  $("#delete-user-button").live('click', function(e) {
    user_id = $(this).attr('tabindex');

    // A changer par une fancybox plus tard
    var answer = confirm(msg_confirm_delete);

    if (answer)
    {
      $.ajax({
        type  : "POST",
        cache : false,
        url   : site_url+'users/delete',
        data  : 'user_id='+user_id,
        success: function(data) {

          // Message retourné (erreur ou ok)
          $.jGrowl(data.message);

          // Mise à jour de la liste des utilisateurs
          $("#users-list div.content").load(site_url+'users/get_list');
        }
      });
    }

    return false;
  });

  // Attache l'ajoût d'un utilisateur sur le bouton
  $("#add-user-button").live('click', function(e) {
    $("#user_add").slideToggle("slow");

    // Efface les champs du formulaire
    $("#add-user-username").val('');
    $("#add-user-password").val('');

    $("#add-user-button").hide();
  });

  // Gère l'annulation sur le formulare d'ajoût
  $("#cancel-button").click(function(){
    $("#user_add").slideToggle("hide");

    // Efface les champs du formulaire
    $("#add-user-username").val('');
    $("#add-user-password").val('');

    $("#add-user-button").show();
    return false;
  });

  // Validation du formulaire pour l'ajoût d'utilisateur
  $("#add_user_form").bind("submit", function() {

    // Gestion d'un minimum de caractères à saisir
    if ($("#add-user-username").val().length < 1 || $("#add-user-password").val().length < 1) {
        $.jGrowl(msg_bad_data);
        return false;
    }

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      url   : site_url+'users/add',
      data  : $(this).serializeArray(),
      success: function(data) {
        $.fancybox.hideActivity();

        // Mise à jour de la liste des utilisateurs
        $("#users-list div.content").load(site_url+'users/get_list');

				$("#user_add").slideToggle("hide");

				// Efface les champs du formulaire
				$("#add-user-username").val('');
				$("#add-user-password").val('');

				$("#add-user-button").show();

        // Message retourné (erreur ou ok)
        $.jGrowl(data.message);
      }
    });

    return false;
  });

});
