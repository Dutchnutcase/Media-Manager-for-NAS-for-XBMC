function saveOrder() {
	var order = $("#movies_list li").map(function() { return $(this).attr("id"); }).get();

	$.ajax({
			url: site_url+'sets/change_order/'+set_id,
			type: 'POST',
			data: {
					'order': order,
			},
			success: function (data)
			{
				$.jGrowl(data.message);
			}
	});
};

jQuery(document).ready(function() {

  // L'utilisateur peut changer les images, on simule visuellement un lien
  var media_id = $('#poster_image').attr('title');

  var title = $('#poster_image').attr('alt');
  $('#poster_image').removeAttr('alt');
  $('#poster_image').attr('title', title);
  $('#poster_image').addClass('select');

  // Déclenche le choix de l'affiche
  $('#poster_image').click(function(){
		var media_id = $('#poster_image').attr('title');
    $.fancybox({
      'titlePosition'   : 'inside',
      'autoDimensions'  : false,
      'centerOnScroll'  : 'true',
      'title'           : title,
      'width'           : 695,
      'height'          : 420,
      'content' : $('#posters-list').html()
    });
  });

  // Transmission de l'affiche choisie + affichage bulle d'information
  $('#fancybox-content .poster_thumb').live('click', function(e) {
    $.fancybox.showActivity();
    var image = this.getAttribute('alt');
    $.ajax({
       type: 'POST',
       url: site_url+'images/change',
       data: 'media_id='+media_id+'&type=poster&image='+image,
       success : function(data){
              $('#fancybox-close').trigger('click');
              var timestamp = new Date().getTime();
              $('#poster_image').attr('src',$('#poster_image').attr('src') + '?' +timestamp );
       }
    });
    return false;
  });

	// Rend la liste des films de la saga triable
	$("#movies_list").dragsort({ dragSelector: "span", dragEnd: saveOrder });

  // Cache le formulaire d'ajoût
  $("#add-set-form").hide();

  // Attache l'ajoût d'une saga sur le bouton
  $("#add-set-button").click(function(){
    $("#add-set-form").slideToggle("slow");
    $("#add-set-button").hide();
  });

  // Gère l'annulation sur le formulare d'ajoût
  $("#cancel-button").click(function(){
    $("#add-set-form").slideToggle("hide");
    $("#add-set-button").show();
    return false;
  });

  // Validation du formulaire pour l'ajoût d'une saga
  $("#add-set-the_form").bind("submit", function() {

    // Gestion d'un minimum de caractères à saisir
    if ($("#add-set-name").val().length < 1) {
        $.jGrowl(msg_bad_data);
        return false;
    }

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      url: site_url+'sets/add',
      data: $(this).serializeArray(),
      success: function(data) {
        $.fancybox.hideActivity();

        // Mise à jour de la liste des sagas
//        $("#sets-index div.content").load(site_url+'sets/get_list');

				$("#add-set-form").slideToggle("hide");
				$("#add-set-button").show();

        // Message retourné (erreur ou ok)
        $.jGrowl(data.message);
      }
    });

    return false;
  });

  // Suppression de l'affiche d'une saga
  $("#delete-poster-button").click(function(){

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    // A changer par une fancybox plus tard
    var answer = confirm(msg_confirm_delete);

    if (answer)
    {
      $.ajax({
        type  : "POST",
        cache : false,
				url: site_url+'sets/delete_poster/'+set_id,
				success: function(data) {
					$.fancybox.hideActivity();

					// Message retourné (erreur ou ok)
					$.jGrowl(data.message);
					}
      });
    }

    return false;
  });

});
