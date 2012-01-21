function UploadErrorEnd(message)
{
	// Fin du traitement
  $.fancybox.hideActivity();

	$.jGrowl(message);
}

function UploadAdvancedSettingsEnd()
{
	// Fichier 'advancedsettings.xml' chargé et analysé, on cache ce formulaire
  $("#step2").fadeOut(900);

	// Fin du traitement
  $.fancybox.hideActivity();

	// Premier appel ajax
	$.get(site_url+'setup/i_database')
		.done(function(data){

			// Gestion de la base de données
			$("#database").fadeIn(2000);

			// Fin du traitement
			$.fancybox.hideActivity();

			$('#info_database').html(data);
			$('#info_database').fadeIn(1000);
			
		}).pipe(function(data){
		 return $.get("setup/i_users");
		}).done(function(data){
				$('#info_users').html(data);
				$('#info_users').fadeIn(1000);
		}).pipe(function(data){
		 return $.get("setup/i_xbmc");
		}).done(function(data){
				$('#info_xbmc').html(data);
				$('#info_xbmc').fadeIn(1000);
		}).pipe(function(data){
		 return $.get("setup/i_sources");
		}).done(function(data){
				$('#info_sources').html(data);
				$('#info_sources').fadeIn(2000);

				// Gestion des sources
				$("#step3").fadeIn(5000);
				
				// Base de données paramétrée on cache
				$("#database").fadeOut(4000);
		});
}

function UploadSourcesEnd()
{
	// Fichier 'sources.xml' chargé et analysé, on cache ce formulaire
  $("#step3").fadeOut(900);

	// Fin du traitement
  $.fancybox.hideActivity();

	// Lien symbolique pour les images
	$("#step4").fadeIn(2000);
}

jQuery(document).ready(function() {

  // Validation du formulaire pour le choix de la langue
  $("#language_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      data: 'language='+$('#language option:selected').val(),
      url   : site_url+'setup/language',
      success: function(string) {

				// Langue choisie
				$("#step1").fadeOut(900);

				// Fin du traitement
				$.fancybox.hideActivity();

				// Fichier 'advancedsettings.xml'
				$("#step2").fadeIn(2000);
      }
    });

    return false;
  });

  // Validation du formulaire pour le choix du dossier 'Thumbnails' pour XBMC
  $("#symbolic_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

    $.ajax({
      type  : "POST",
      cache : false,
      data: 'symbolic='+$('#symbolic').val(),
      url   : site_url+'setup/symbolic',
      success: function(data) {

				// Fin du traitement
				$.fancybox.hideActivity();

				if (data.success == 1)
				{
					// Lien symbolique créé
					$("#step4").fadeOut(900);

					// Fin de l'assistant
					$("#step5").fadeIn(2000);
				}
				else
				{
					$.jGrowl(data.message);
				}
      }
    });

    return false;
  });

  // Validation du formulaire pour le chargement du fichier 'advancedsettings.xml'
  $("#upload_advancedsettings_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

  });

  // Validation du formulaire pour le chargement du fichier 'sources.xml'
  $("#upload_sources_form").live("submit", function() {

    // Animation pour l'attente de la réponse ajax
    $.fancybox.showActivity();

  });

  // Validation du formulaire à la fin de l'assistant
  $("end_form").live("submit", function() {

		window.location.href = site_url;
    return false;
  });

	// L'appui sur le bouton déclenche le choix d'un fichier
  $('#browse_button_advancedsettings').click(function() {
		$('#real_file_input_advancedsettings').click();
  });

	// Report du nom de fichier dans la fausse balise
  $('#real_file_input_advancedsettings').change(function() {
    $('#fake_file_input_advancedsettings').val($(this).val());
	});

	// L'appui sur le bouton déclenche le choix d'un fichier
  $('#browse_button_sources').click(function() {
		$('#real_file_input_sources').click();
  });
  
	// Report du nom de fichier dans la fausse balise
  $('#real_file_input_sources').change(function() {
    $('#fake_file_input_sources').val($(this).val());
	});

});
