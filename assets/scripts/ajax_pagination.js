jQuery(document).ready(function() {

  // Remplacement des liens de la pagination pour un appel en ajax
  $('.pagination a').live('click', function(e) {
    $.fancybox.showActivity();

    // Bloc parent du lien cliqué pour mise à jour future
    var content = $(this).closest('.content');

    $.ajax({
       type: "GET",
       url: $(this).attr('href'),
       success: function(html) {
        // Mise à jour du bloc parent du lien cliqué
        $(content).html(html);
        $.fancybox.hideActivity();
        }
    });

    // Empêche le lien de fonctionner normalement
    return false;
  });
});
