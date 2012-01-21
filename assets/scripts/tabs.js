$(document).ready(function() {

  $(function () {
    var tabContainers = $('div.secondary-navigation ~ div.content');
    tabContainers.hide().filter(':first').show();
    
    $('div.secondary-navigation ul.wat-cf a').click(function () {
				tabContainers.hide();
        
				// id défini pour les blocs de saison
				if (this.id)
				{
					// Numéro de la saison après le '-'
					var a = this.id.split('-');
					
					var season = a[1];

					$.fancybox.showActivity();

					// Elément pour mise à jour future
					var content = $('#block-'+this.id);

					$.ajax({
						 type: "GET",
						 url: site_url+'episodes_by_season/'+tvshow_id+'/'+season,
						 success: function(html) {
							// Mise à jour de l'élément
							$(content).html(html);
							$.fancybox.hideActivity();
							}
					});
				}

				tabContainers.filter(this.hash).show();
        
        $('div.secondary-navigation ul.wat-cf li').removeClass('active');
        $(this).parent().addClass('active');
        
        return false;
    }).filter(':first').click();
  });

});
