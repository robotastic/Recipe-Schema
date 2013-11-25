function makeLists() {
  jQuery(document).ready(function($) {

    var textarea = $('#directions');
    textarea.focus();
    this.document.execCommand('insertOrderedList', false, null);
    textarea = $('#ingredients');
    textarea.focus();
    this.document.execCommand('insertUnorderedList', false, null);
  });
}


function recipeTextAreas() {

  jQuery(document).ready(function($) {

    var directions = new Wysiwyg({
      listName: 'Direction',
      listType: 'ordered',
      area: '#directions-area'
    });
    directions.el.insertBefore("#directions-area");
    $('#directions-area').live('keyup focus paste click', directions.updateButton);

    var ingredients = new Wysiwyg({
      listName: 'Ingredient',
      listType: 'unordered',
      area: '#ingredients-area'
    });
    ingredients.el.insertBefore("#ingredients-area");
    $('#ingredients-area').live('keyup focus paste click', ingredients.updateButton);

    $("#post").submit(function() {
      $("<input>").attr({
        type: "hidden",
        id: "directions",
        name: "directions"
      }).appendTo("#post");
      $("#directions").val($("#directions-area").html());

      $("<input>").attr({
        type: "hidden",
        id: "ingredients",
        name: "ingredients"
      }).appendTo("#post");
      $("#ingredients").val($("#ingredients-area").html());
    });
    var textarea = $("#directions-area");
    if(textarea.text() == "") {
      textarea.focus();
      directions.document.execCommand("insertOrderedList", false, null);
    }
    var textarea = $("#ingredients-area");
    if(textarea.text() == "") {
      textarea.focus();
      ingredients.document.execCommand("insertUnorderedList", false, null);
    }


  });


}

function tagText(field) {


  txt = getSelectedText();

  jQuery('#' + field + '_button').css({
    'background-color': '#ccc',
    'color': '#666'
  });


  var title = document.getElementById(field);
  title.value = txt;
  if(field == 'source') {
    url = getSelectedURL();
    if(url) {
      document.getElementById('source-url').value = url;
    }
  }
  if((field == 'ingredients-area') || (field == 'directions-area')) {
    var lines = txt.split("\n");

    if(field == 'ingredients-area') {
      var output = '<ul>';
      for(var i = 0; i < lines.length; i++) {
        output = output + '<li>' + lines[i] + '</li>';
      }
      output = output + '</ul>';
    } else {
      var output = '<ol>';
      for(var i = 0; i < lines.length; i++) {
        output = output + '<li>' + lines[i] + '</li>';
      }
      output = output + '</ol>';
    }
    title.innerHTML = output;
  } else {
    title.value = txt;
  }
}

function tagEditorText(field) {


  ed = tinyMCE.activeEditor;
  var txt = ed.selection.getContent({
    format: 'text'
  });

  jQuery('#' + field + '_button').css({
    'background-color': '#ccc',
    'color': '#666'
  });


  var title = document.getElementById(field);

  if(field == 'source') {
    url = getEditorSelectedURL();
    if(url) {
      document.getElementById('source-url').value = url;
    }
  }
  if((field == 'ingredients-area') || (field == 'directions-area')) {
    var lines = txt.split("\n");

    if(field == 'ingredients-area') {
      var output = '<ul>';
      for(var i = 0; i < lines.length; i++) {
        output = output + '<li>' + lines[i] + '</li>';
      }
      output = output + '</ul>';
    } else {
      var output = '<ol>';
      for(var i = 0; i < lines.length; i++) {
        output = output + '<li>' + lines[i] + '</li>';
      }
      output = output + '</ol>';
    }
    title.innerHTML = output;
  } else {
    title.value = txt;
  }
}

function getIframeSelectionText(iframe) {
  var win = iframe.contentWindow;
  var doc = win.document;

  if(win.getSelection) {
    return win.getSelection().toString();
  } else if(doc.selection && doc.selection.createRange) {
    return doc.selection.createRange().text;
  }
}



function selectText(field) {

  ed = tinyMCE.activeEditor;
  var txt = ed.selection.getContent({
    format: 'HTML'
  });
  tinyMCE.activeEditor.selection.setContent(' ');
  /*var iframe = document.getElementById("contentIfr");
  alert(getIframeSelectionText(iframe));*/
  var selected_text = document.getElementById('selected_text');
  selected_text.value = txt;
  var form = document.getElementById('selectTextForm');
  form.submit();
}
//Grab selected text

function getSelectedText() {
  if(window.getSelection) {
    return window.getSelection().toString();
  } else if(document.getSelection) {
    return document.getSelection();
  } else if(document.selection) {

    return document.selection.createRange().text;
  }
}

function getEditorSelectedURL() {
  var aNode = null;
  if(window.getSelection) {
    var sel = tinyMCE.activeEditor.selection.getSel();
    if(sel.rangeCount) {
      start = sel.getRangeAt(0).startContainer;
      end = sel.getRangeAt(0).endContainer;
      if(start.parentNode.tagName == "A") {
        aNode = start.parentNode;
      } else {
        tempNode = start.nextSibling;
        while(sel.containsNode(tempNode, false)) {
          if(tempNode.tagName == "A") {
            aNode = tempNode;
            break;
          }
          tempNode = tempNode.nextSibling;
        }
      }

    }
  } else if(document.selection && document.selection.type != "Control") {
    aNode = document.selection.createRange().parentElement();
  }

  if(aNode && (aNode.tagName == "A")) {
    return aNode.getAttribute("href").toString().replace(/\\"/g, '');
  } else {
    return null;
  }

}


function getSelectedURL() {
  var aNode = null;
  if(window.getSelection) {
    var sel = window.getSelection();
    if(sel.rangeCount) {
      start = sel.getRangeAt(0).startContainer;
      end = sel.getRangeAt(0).endContainer;
      if(start.parentNode.tagName == "A") {
        aNode = start.parentNode;
      } else {
        tempNode = start.nextSibling;
        while(sel.containsNode(tempNode, false)) {
          if(tempNode.tagName == "A") {
            aNode = tempNode;
            break;
          }
          tempNode = tempNode.nextSibling;
        }
      }

    }
  } else if(document.selection && document.selection.type != "Control") {
    aNode = document.selection.createRange().parentElement();
  }

  if(aNode && (aNode.tagName == "A")) {
    return aNode.getAttribute("href").toString().replace(/\\"/g, '');
  } else {
    return null;
  }

}

function ROBORemoveEquip(e) {
  event = e || window.event
  event.preventDefault();
  div = $j(event.target).closest("div");
  $div = $j(div);
  id = $j(div).attr("id");
  $div.remove();

  return false;
}

function ROBOAddEquip(e) {
  event = e || window.event
  event.preventDefault();
  div = $j(event.target).closest("div");
  $div = $j(div);
  id = $j(div).attr("id");
  recipe_asin = $j('#recipe_asin').val();
  $j('#recipe_asin').val(recipe_asin + ',' + id);
  $div.appendTo("#recipe_equipment");
  $j('#' + id + ' a').remove();
  $div.append("<a href='#' onClick='ROBORemoveEquip()'>Remove</a></div>");

  return false;
}

function ROBOEquipResults(data) {
  $j('#search_results').html('');
  if(data['success']) {
    items = data.items
    for(var i = 0; i < items.length; i++) {
      html = "<div id='" + items[i].asin + "' class='amazon_item'><img src='" + items[i].image + "'><br>";
      html = html + items[i].title + "<br>" + items[i].price + "<br>";
      html = html + "<a href='#' onClick='ROBOAddEquip()'>Add</a> <a href='" + items[i].link + "' target='_blank'>View</a></div>";
      $j('#search_results').append(html);
    }
    $j('#search_results').append('<a href="#" onClick="ROBONextSearchPage()">Next-></a>');
  } else {
    $j('#search_results').html("<h2>" + data.message + "</h2>");
  }
}

function ROBOEquipSearch(term, category, page) {
  $j("#search_results").html(wait);
  $j.ajax({
    type: "POST",
    // the kind of data we are sending
    url: ajaxurl + "?callback=?&action=amazon_search",
    // this is the file that processes the form data
    data: {
      term: term,
      category: category,
      page: page
    },
    success: function(response) {
      ROBOEquipResults(response);
    }
  });
}

function ROBONextSearchPage() {
  page = page + 1;
  ROBOEquipSearch(term, category, page);
}

function ROBOEquipSearchSubmit(e) {
  e.preventDefault();
  term = $j("#search_term").val();
  page = 1;
  category = $j("#amazon_category").val();
  ROBOEquipSearch(term, category, page);
}

function ROBORemoveRecipe(event) {
  jQuery(event.target).closest("tr").remove();
  return false;
}

function ROBOAddRecipe(item) {
  $table = jQuery(".awesome-recipe-list > tbody:last");
  $lastRow = jQuery("tr:last", $table);

  if($lastRow && $lastRow.hasClass('alternate')) {
    row = '<tr >';
  } else {
    row = '<tr class="alternate">';
  }
  row = row + '<td class="post_title column-post_title"><strong>' + item.label + '</strong>';
  row = row + '<input type="hidden" name="recipe_id[]" value="' + item.id + '">';
  row = row + '<input type="hidden" name="recipe_thumbnail_id[]" value="' + item.thumbnail_id + '">';
  row = row + '<div class="row-actions"><span class="remove"><a href="#"  onclick="ROBORemoveRecipe(event)">Remove Recipe</a> | </span><span class="image"><a class="change-recipe-image" href="#">Change Image</a></span></div></td>';
  row = row + '<td class="thumbnail column-thumbnail"><div class="recipe-thumb" id="media-item-thumb-' + item.id + '">';
  if(item.thumb) {
    row = row + '<img style="width:auto; height:auto; max-width:32px; max-height:32px;" src="' + unescape(item.thumb[0]) + '" class="attachment-32x32 wp-post-image">';
    row = row + '</div></td></tr>';
  }
  if($lastRow && $lastRow.hasClass('no-items')) {
    $table.empty();
  }
  $table.append(row);
}


var ROBOSetThumbnailHTML;
(function($) {
  ROBOSetThumbnailHTML = function(src, targetId) {
    var $thumb = $('#media-item-thumb-' + targetId);
    $thumb.empty();

    $thumb.append("<img src='" + src + "' style='width:auto; height:auto; max-width:32px; max-height:32px;'>");

    //$('.inside', '#postimagediv').html(html);
  };
})(jQuery);

function RecipeSetAsThumbnail(id, targetId, nonce) {
  var $link = jQuery('a#wp-post-thumbnail-' + id);

  $link.text(setPostThumbnailL10n.saving);
  jQuery.post(ajaxurl, {
    action: "set-post-thumbnail",
    post_id: targetId,
    thumbnail_id: id,
    _ajax_nonce: nonce,
    cookie: encodeURIComponent(document.cookie)
  }, function(str) {
    var win = window.dialogArguments || opener || parent || top;
    $link.text(setPostThumbnailL10n.setThumbnail);
    if(str == '0') {
      alert(setPostThumbnailL10n.error);
    } else {
      $link.text(setPostThumbnailL10n.done);
      $link.fadeOut(2000);

      // Now you can use $ as a reference to jQuery without any problem
      var $img = jQuery("img", str);
      var src = $img.attr("src");
      //jQuery('#media-item-thumb-' + targetId).html(str);
      //win.WPSetThumbnailID(id);
      win.ROBOSetThumbnailHTML(src, targetId);
      win.tb_remove()
    }
  });
}

// wp.media.controller.FeaturedImage - from MediaView.js
// 
// ---------------------------------
/*
  RecipeFeaturedImage = media.controller.Library.extend({
    defaults: _.defaults({
      id:         'featured-image',
      filterable: 'uploaded',
      multiple:   false,
      toolbar:    'featured-image',
      title:      l10n.setFeaturedImageTitle,
      priority:   60,

      syncSelection: false
    }, media.controller.Library.prototype.defaults ),

    initialize: function() {
      var library, comparator;

      // If we haven't been provided a `library`, create a `Selection`.
      if ( ! this.get('library') )
        this.set( 'library', media.query({ type: 'image' }) );

      media.controller.Library.prototype.initialize.apply( this, arguments );

      library    = this.get('library');
      comparator = library.comparator;

      // Overload the library's comparator to push items that are not in
      // the mirrored query to the front of the aggregate collection.
      library.comparator = function( a, b ) {
        var aInQuery = !! this.mirroring.getByCid( a.cid ),
          bInQuery = !! this.mirroring.getByCid( b.cid );

        if ( ! aInQuery && bInQuery )
          return -1;
        else if ( aInQuery && ! bInQuery )
          return 1;
        else
          return comparator.apply( this, arguments );
      };

      // Add all items in the selection to the library, so any featured
      // images that are not initially loaded still appear.
      library.observe( this.get('selection') );
    },

    activate: function() {
      this.updateSelection();
      this.frame.on( 'open', this.updateSelection, this );
      media.controller.Library.prototype.activate.apply( this, arguments );
    },

    deactivate: function() {
      this.frame.off( 'open', this.updateSelection, this );
      media.controller.Library.prototype.deactivate.apply( this, arguments );
    },

    updateSelection: function() {
      var selection = this.get('selection'),
        id = featuredImage.getRowThumbId(),
        attachment;

      if ( '' !== id && -1 !== id ) {
        attachment = Attachment.get( id );
        attachment.fetch();
      }

      selection.reset( attachment ? [ attachment ] : [] );
    }
  });*/



featuredImage = {


  getRow: function() {
    return this._row;
  },
  getRowThumbId: function() {

    return this._rowThumbId;
  },

  setRow: function(row) {
    this._row = row;
    this._rowThumbId = row.find("input[name='recipe_thumbnail_id[]']")[0].value;
  },


  get: function() {
    return wp.media.view.settings.post.featuredImageId;
  },

  setThumbnailId: function(id) {
    var self = this;
    jQuery(function($) {
      $element = self.getRow();


      recipe_id = $element.find("input[name='recipe_id[]']")[0].value;
      $thumb_input = $($element.find("input[name='recipe_thumbnail_id[]']")[0]);
      $thumb_input.val(id);
      self._rowThumbId = id;

      /*
      var settings = wp.media.view.settings;

      //settings.post.featuredImageId = id;

      wp.media.post( 'set-post-thumbnail', {
        json:         true,
        post_id:      recipe_id, //settings.post.id,
        thumbnail_id: id, //settings.post.featuredImageId,
        _wpnonce:     self.getNonce() //settings.post.nonce
      }).done( function( html ) {
        
      });*/
    });
  },

  frame: function() {
    if(this._frame) {
      return this._frame;
    }



    sel = new wp.media.controller.FeaturedImage();
    sel.updateSelection = function() {
      var selection = this.get('selection'),
        id = featuredImage.getRowThumbId(),
        attachment;


      if('' !== id && -1 !== id) {
        attachment = wp.media.model.Attachment.get(id);
        attachment.fetch();
      };

      selection.reset(attachment ? [attachment] : []);
    }



    this._frame = wp.media({
      state: 'featured-image',
      states: [sel]
    });



    this._frame.on('toolbar:create:featured-image', function(toolbar) {
      this.createSelectToolbar(toolbar, {
        text: wp.media.view.l10n.setFeaturedImage
      });
    }, this._frame);

    this._frame.state('featured-image').on('select', this.select);
    return this._frame;
  },

  select: function() {
    var settings = wp.media.view.settings,
      selection = this.get('selection').single();

    row = featuredImage.getRow();

    model = selection; //.first(),
    sizes = model.get('sizes');

    // @todo: might need a size hierarchy equivalent.
    if(sizes) size = sizes['thumbnail'] || sizes.medium;

    // @todo: Need a better way of accessing full size
    // data besides just calling toJSON().
    size = size || model.toJSON();
    jQuery(function($) {
      //frame.close();
      row.find('img').remove();
      row.find('.recipe-thumb').prepend(
      $('<img />', {
        src: size.url,
        width: '32',
        height: '32'
      }));
    });
    featuredImage.setThumbnailId(selection ? selection.id : -1);

    //wp.media.featuredImage.set( selection ? selection.id : -1 );
  },

};

// basesd on media-editor.js


function ROBONewSelectRecipeThumbnail(frame, row) {


  //if ( recipeSchema.featuredImage ) {
  featuredImage.setRow(row);
  featuredImage.frame().open();
  return row;
  // }


}

function ROBOSelectRecipeThumbnail(postId, targetId) {
  tb_show('', 'media-upload.php?post_id=' + postId + '&target_id=' + targetId + '&type=image&tab=feature_image_tab&TB_iframe=true');
  return false;
}
/*
 if ( ! workflow ) {
        workflow = wp.media({
          title:   title,
          library: {
            type: 'image'
          }
        });

        workflow.selection.on( 'add', function( model ) {
          var sizes = model.get('sizes'),
            size;

          
          $row.find('img').remove();
          // @todo: might need a size hierarchy equivalent.
          if ( sizes )
            size = sizes['post-thumbnail'] || sizes.medium;

          // @todo: Need a better way of accessing full size
          // data besides just calling toJSON().
          size = size || model.toJSON();

          workflow.modal.close();
          workflow.selection.clear();

          $( '<img />', {
            src:    size.url,
            width:  size.width
          }).prependTo( $element );
        });
      }

      workflow.modal.open();

      }

function ROBOLoadMediaUpload(postId, targetId){

}*/