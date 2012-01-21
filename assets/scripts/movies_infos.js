jQuery(document).ready(function() {

  // Edition de la phrase d'accroche
  $('.tagline_editable').editable(site_url+'movies/change_data/'+movie_id, {
      id: 'field',
      name: 'value',
      type: 'text',
      cssclass : 'form',
      onblur: 'cancel',
      tooltip: btn_clic_edit,
      submit: '<button class="button" type="button"><img src="'+site_url+'/assets/gui/tick.png" /> '+btn_save+'</button>',
      cancel: '<span class="text_button_padding">'+btn_or+'</span><button class="button" type="button"><img src="'+site_url+'/assets/gui/cross.png" /> '+btn_cancel+'</button><hr class="clear" />'
  });

  // Edition du résumé
  $('.overview_editable').editable(site_url+'movies/change_data/'+movie_id, {
      id: 'field',
      name: 'value',
      type: 'textarea',
      cssclass : 'form',
      rows : '10',
      cols : '80',
      onblur: 'cancel',
      tooltip: btn_clic_edit,
      submit: '<button class="button" type="button"><img src="'+site_url+'/assets/gui/tick.png" /> '+btn_save+'</button>',
      cancel: '<span class="text_button_padding">'+btn_or+'</span><button class="button" type="button"><img src="'+site_url+'/assets/gui/cross.png" /> '+btn_cancel+'</button><hr class="clear" />'
  });

  // Pour le choix de la saga
  var load_sets_list = function() {
      $.fancybox.showActivity();

      // Pour la liste des sagas
      var elClone = $("#sets-list").clone(true);

      // Liste des sagas retournées par le serveur
      $.getJSON(site_url+'sets/ajax_get_list', function(json) {
        $(elClone).empty();
        var list = $(elClone).append('<ul></ul>').find('ul');

        $.each(json, function(i,item) {
          $('<li id="' + item.id + '" ><img class="poster_thumb" src="'+item["poster"].url+'" /><br />'+item.name+'<br />('+item.total+')<br /></li>').appendTo(list);
        });

        // Liste des sagas ajoutée au document
        $("#sets-list").replaceWith(elClone);

        // Transmission de la saga choisie + affichage bulle d'information
        // Et mise à jour d'éléments dans la page
        $("#fancybox-content li").live('click', function(e) {
          $.fancybox.showActivity();
          $.ajax({
             type: 'POST',
             url: site_url+'movies/add_to_set/'+movie_id,
             data: 'set_id='+this.id,
             success : function(data){
                 $("#movie_in_set").html(data.movie_in_set);
                 $("#movie_in_set").show();
                 $("#add_to_set_button").remove();
                 $('#fancybox-close').trigger('click');
                 $.jGrowl(data.message);
             }
          });
          return false;
        });

      // Déclenche le choix de la saga
      $.fancybox({
        'titlePosition'       : 'inside',
        'centerOnScroll'      : true,
        'autoDimensions'      : false,
        'width'               : 700,
        'height'              : 400,
        'content' : $('#sets-list').html()
      });
    });
  }

  // Attache le choix de la saga sur le bouton
  $("#add_to_set_button").click(function(){
    load_sets_list();
  });

  // Pour le titre de la fenêtre de sélection d'un film
  var title_refresh_list = '';

  // Pour le choix de la mise à jour
  var load_refresh_list = function() {
      $.fancybox.showActivity();

      // Pour la liste des films
      var elClone = $("#refresh-list").clone(true);

      // Liste des films retournées par le serveur
      $.getJSON(site_url+'movies/ajax_refresh/'+movie_id, function(json) {
        $(elClone).empty();

        title_refresh_list = json.title;

        $.each(json.results, function(i,item) {
          $("<img class='poster_thumb' id='" + item.id + "' title='" + item.title + "' src='" + item.poster + "' />").appendTo(elClone);
        });

        // Liste des films ajoutée au document
        $("#refresh-list").replaceWith(elClone);

        // Transmission de l'identifiant choisi + affichage bulle d'information
        // Et mise à jour d'éléments dans la page
        $("#fancybox-content .poster_thumb").live('click', function(e) {
          $.fancybox.showActivity();
          $.ajax({
             type: 'POST',
             url: site_url+'movies/ajax_update/'+movie_id,
             data: 'movie_id='+this.id,
             success : function(data){
                   $('#fancybox-close').trigger('click');
                   window.location.reload(true);
             }
          });
          return false;
        });

      // Déclenche le choix du film
      $.fancybox({
        'titlePosition'       : 'inside',
        'centerOnScroll'      : true,
        'autoDimensions'      : false,
        'title'               : title_refresh_list,
        'content' : $('#refresh-list').html()
      });
    });
  }

  // Attache la sélection du film sur le bouton
  $("#refresh-button").click(function(){
    load_refresh_list();
  });

});
