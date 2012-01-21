var site_url = '';
var movie_id = 0;
var set_id = 0;
var actor_id = 0;

var btn_save = '';
var btn_or = '';
var btn_cancel = '';
var btn_clic_edit = '';
var msg_bad_data = '';
var msg_confirm_delete = '';

jQuery(document).ready(function() {

  // Ouvre la boîte de connexion en cas d'appui sur le lien 'connexion'
  $("#login").fancybox({
  'scrolling' : 'no',
  'padding'   : 0,
  'autoScale' : true,
  'titleShow' : false
  });

  // Formulaire de connexion
  $("#login-form").bind("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      url   : site_url+'users/login',
      data  : $(this).serializeArray(),
      success: function(data) {
//        data = $.parseJSON(string);
        if (data.success)
        {
          // Chargement dynamique de script(s) si présent(s)
          if (typeof(data.js) != "undefined")
          {
            $.each(data.js, function(i, file) {
              $.getScript(site_url+'assets/scripts/'+file+'.js');
            });
          }

          // Mise à jour dynamique du contenu toujours à faire
          $.each(data.update, function(selector, content) {
            $(selector).html(content);
          });

          $('#fancybox-close').trigger('click');
        }
        else
          $.fancybox(data['msg']);
      }
    });

    return false;
  });

});
