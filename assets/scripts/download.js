jQuery(document).ready(function() {

  // Attache le téléchargement d'un film ou d'un épisode
  $("#download-button").click(function(){
		$("#download_frame").attr("src",site_url+"files/download/"+media_id);
		return false;
  });

});
