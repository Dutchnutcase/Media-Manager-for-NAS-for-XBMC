
// Appelée après la traitement d'une image téléchargée à gérer
function UploadImageEnd(data)
{
	// En cas de succès
	if (data.success)
	{
		// Efface le contenu du champs du formulaire concerné par data.type
		$("#fake_file_input_"+data.type).val('');

		// Mise à jour de la bonne image en fonction de data.type et data.url
		var timestamp = new Date().getTime();
		$('#'+data.type+'_image').removeAttr("src");
		$('#'+data.type+'_image').attr('src', data.url + '?' +timestamp );  

		$.fancybox.hideActivity();
		$('#fancybox-close').trigger('click');
	}
  
  $.jGrowl(data.message);
}

jQuery(document).ready(function() {

  // L'utilisateur peut changer les images, on simule visuellement un lien
  var title = $('#poster_image').attr('alt');
  $('#poster_image').removeAttr('alt');
  $('#poster_image').attr('title', title);
  $('#poster_image').addClass('select');

  // Déclenche le choix de l'affiche
  $('#poster_image').click(function(){
    $.fancybox({
      'titlePosition'   : 'inside',
      'autoDimensions'  : false,
      'centerOnScroll'  : 'true',
      'title'           : title,
      'width'           : 695,
      'height'          : 205,
      'content' : $('#posters-list').html()
    });
  });

  // Transmission de l'affiche choisie + affichage bulle d'information
  $('#fancybox-content .poster_thumb').live('click', function(e) {
    $.fancybox.showActivity();
    var image_url = this.getAttribute('alt');
    var media_id = this.getAttribute('rel');
    $.ajax({
       type: 'POST',
       url: site_url+'images/change',
       data: 'media_id='+media_id+'&poster_filename='+poster_filename+'&type=poster&image_url='+image_url,
       success : function(data){
              $('#fancybox-close').trigger('click');
              $('#poster_image').removeAttr("src");
              $('#poster_image').attr('src', data.url);
              $.jGrowl(data.message);
       }
    });
    return false;
  });

  // L'utilisateur peut changer les images, on simule visuellement un lien
  var title = $('#banner_image').attr('alt');
  $('#banner_image').removeAttr('alt');
  $('#banner_image').attr('title', title);
  $('#banner_image').addClass('select');

  // Déclenche le choix de la bannière
  $('#banner_image').click(function(){
    $.fancybox({
      'titlePosition'   : 'inside',
      'autoDimensions'  : false,
      'centerOnScroll'  : 'true',
      'title'           : title,
      'width'           : 695,
      'height'          : 420,
      'content' : $('#banners-list').html()
    });
  });

  // Transmission de la bannière choisie + affichage bulle d'information
  $('#fancybox-content .banner_thumb').live('click', function(e) {
    $.fancybox.showActivity();
    var image_url = this.getAttribute('alt');
    var media_id = this.getAttribute('rel');
    $.ajax({
       type: 'POST',
       url: site_url+'images/change',
       data: 'media_id='+media_id+'&banner_filename='+banner_filename+'&type=banner&image_url='+image_url,
       success : function(data){
              $('#fancybox-close').trigger('click');
              var timestamp = new Date().getTime();
              $('#banner_image').attr('src',$('#banner_image').attr('src') + '?' +timestamp );
              $.jGrowl(data.message);
       }
    });
    return false;
  });

  // L'utilisateur peut changer les images, on simule visuellement un lien
  var title = $('#backdrop_image').attr('alt');
  $('#backdrop_image').removeAttr('alt');
  $('#backdrop_image').attr('title', title);
  $('#backdrop_image').addClass('select');

  // Déclenche le choix du fond d'écran
  $('#backdrop_image').click(function(){
    $.fancybox({
      'titlePosition'   : 'inside',
      'autoDimensions'  : false,
      'centerOnScroll'  : 'true',
      'title'           : title,
      'width'           : 655,
      'height'          : 358,
      'content' : $('#backdrops-list').html()
    });
  });

  // Transmission du fond d'écran choisi + affichage bulle d'information
  $('#fancybox-content .backdrop_thumb').live('click', function(e) {
    $.fancybox.showActivity();
    var image_url = this.getAttribute('alt');
    var media_id = this.getAttribute('rel');
    $.ajax({
       type: 'POST',
       url: site_url+'images/change',
       data: 'media_id='+media_id+'&backdrop_filename='+backdrop_filename+'&type=backdrop&image_url='+image_url,
       success : function(data){
              $('#fancybox-close').trigger('click');
              var timestamp = new Date().getTime();
              $('#backdrop_image').attr('src',$('#backdrop_image').attr('src') + '?' +timestamp );
              $.jGrowl(data.message);
       }
    });
    return false;
  });

	// Attache le chargement d'une nouvelle affiche
  $("#add_poster_button").click(function(){
		$.fancybox({
		'scrolling' : 'no',
		'padding'   : 0,
		'autoScale' : true,
		'titleShow' : false,
		'type' 			: 'inline',
		'href' 			: '#box_add_poster'
		});
  });

  // Validation du formulaire pour le chargement d'une nouvelle affiche
  $("#add_poster_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();
  });

	// Attache le chargement d'un nouveau fond d'écran
  $("#add_backdrop_button").click(function(){
		$.fancybox({
		'scrolling' : 'no',
		'padding'   : 0,
		'autoScale' : true,
		'titleShow' : false,
		'type' 			: 'inline',
		'href' 			: '#box_add_backdrop'
		});
  });

  // Validation du formulaire pour le chargement d'un nouveau fond d'écran
  $("#add_backdrop_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();
  });

	// Attache le chargement d'une nouvelle photo
  $("#add_photo_button").click(function(){
		$.fancybox({
		'scrolling' : 'no',
		'padding'   : 0,
		'autoScale' : true,
		'titleShow' : false,
		'type' 			: 'inline',
		'href' 			: '#box_add_photo'
		});
  });

  // Validation du formulaire pour le chargement d'une nouvelle photo
  $("#add_photo_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();
  });
  		
});
