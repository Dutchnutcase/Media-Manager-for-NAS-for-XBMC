jQuery(document).ready(function() {

  // Edition du résumé
  $('.overview_editable').editable(submitEdit, {
      type: 'textarea',
      cssclass : 'form',
      rows : '10',
      cols : '80',
      onblur: 'cancel',
      tooltip: btn_clic_edit,
      submit: '<button class="button" type="button"><img src="'+site_url+'/assets/gui/tick.png" /> '+btn_save+'</button>',
      cancel: '<span class="text_button_padding">'+btn_or+'</span><button class="button" type="button"><img src="'+site_url+'/assets/gui/cross.png" /> '+btn_cancel+'</button><hr class="clear" />'
  });

  // Transmission des données modifiées + affichage bulle d'information
  function submitEdit(value, settings)
  {
    var field = $(this).attr('id');

    var result = value;
    $.ajax({
           url: site_url+'tvshows/change_data/'+movie_id,
           type: 'POST',
           data : {'field': field, 'value': value},
           dataType : 'json',
           success : function (data)
           {
             $.jGrowl(data.message);
           }
    });
    return(value);
  }

});