var source_id = 0;

jQuery(document).ready(function() {

  // Validation du formulaire pour l'édition d'utilisateur
  $("#edit_source_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      url   : site_url+'sources/save/'+source_id,
      data  : $(this).serializeArray(),
      success: function(data) {
        $.fancybox.hideActivity();

        // Mise à jour de la liste des sources
        $("#sources-list div.content").load(site_url+'sources/get_list');

        // Message retourné (erreur ou ok)
        $.jGrowl(data.message);
      }
    });

    return false;
  });

  // Edition d'une source depuis la liste
  $("#edit-source-button").live('click', function(e) {
    source_id = $(this).attr('tabindex');

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      cache : false,
      url   : site_url+'sources/edit/'+source_id,
      success: function(string) {
        $.fancybox.hideActivity();
        $("#sources-list div.content").html(string);
      }
    });

    return false;
  });

  // Annule les modifications d'une source et retourne à la liste
  $("#cancel-button").live('click', function(e) {
    // Chargement de la liste des utilisateurs
    $("#sources-list div.content").load(site_url+'sources/get_list');

    return false;
  });

});
