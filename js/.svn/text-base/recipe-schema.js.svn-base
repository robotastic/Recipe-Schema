function tagText(field) 
{
	

	txt = getSelectedText();
	
	jQuery('#'+field + '_button').css({'background-color' : '#ccc', 'color' : '#666'});

	
	var title = document.getElementById(field);
	title.value = txt;
	if (field == 'source') {
		url = getSelectedURL();
		if (url) {
			document.getElementById('source-url').value = url;
		}
	}
}

function tagEditorText(field) 
{
	

	ed = tinyMCE.activeEditor;
	var txt = ed.selection.getContent({format : 'text'});
	
	jQuery('#'+field + '_button').css({'background-color' : '#ccc', 'color' : '#666'});

	
	var title = document.getElementById(field);
	title.value = txt;
	if (field == 'source') {
		url = getEditorSelectedURL();
		if (url) {
			document.getElementById('source-url').value = url;
		}
	}
}


function selectText(field) 
{
	ed = tinyMCE.activeEditor;
	var txt = ed.selection.getContent({format : 'HTML'});
	tinyMCE.activeEditor.selection.setContent(' ');
	var selected_text = document.getElementById('selected_text');
	selected_text.value = txt;
	var form = document.getElementById('selectTextForm');
	form.submit();
}
//Grab selected text
function getSelectedText(){ 
    if(window.getSelection){ 
		return window.getSelection().toString(); 
    } 
    else if(document.getSelection){ 
        return document.getSelection(); 
    } 
    else if(document.selection){ 
	
        return document.selection.createRange().text; 
    } 
} 

function getEditorSelectedURL() {
    var aNode = null;
    if (window.getSelection) {
        var sel = tinyMCE.activeEditor.selection.getSel();
        if (sel.rangeCount) {
        		start = sel.getRangeAt(0).startContainer;
        		end = sel.getRangeAt(0).endContainer;
        		if (start.parentNode.tagName == "A")
        		{
        			aNode = start.parentNode;
        		} else {
        			tempNode = start.nextSibling;
        			while (sel.containsNode(tempNode,false)) {
        				if (tempNode.tagName == "A") {
        					aNode = tempNode;
        					break;
        				}	
        				tempNode = tempNode.nextSibling;
        			}
        		}
            
        }
    } else if (document.selection && document.selection.type != "Control") {
        aNode = document.selection.createRange().parentElement();
    }
    
    if (aNode && (aNode.tagName == "A"))
    {
    	return aNode.getAttribute("href").toString().replace(/\\"/g,'');
    } else {
    	return null;
    }
    
}


function getSelectedURL() {
    var aNode = null;
    if (window.getSelection) {
        var sel = window.getSelection();
        if (sel.rangeCount) {
        		start = sel.getRangeAt(0).startContainer;
        		end = sel.getRangeAt(0).endContainer;
        		if (start.parentNode.tagName == "A")
        		{
        			aNode = start.parentNode;
        		} else {
        			tempNode = start.nextSibling;
        			while (sel.containsNode(tempNode,false)) {
        				if (tempNode.tagName == "A") {
        					aNode = tempNode;
        					break;
        				}	
        				tempNode = tempNode.nextSibling;
        			}
        		}
            
        }
    } else if (document.selection && document.selection.type != "Control") {
        aNode = document.selection.createRange().parentElement();
    }
    
    if (aNode && (aNode.tagName == "A"))
    {
    	return aNode.getAttribute("href").toString().replace(/\\"/g,'');
    } else {
    	return null;
    }
    
}

function ROBORemoveRecipe(event) {
jQuery(event.target).closest("tr").remove();
return false;
}
function ROBOAddRecipe(item) {
$table = jQuery(".awesome-recipe-list > tbody:last");
$lastRow = jQuery("tr:last", $table);

if ($lastRow && $lastRow.hasClass('alternate')) {
row = '<tr >';
} else {
row = '<tr class="alternate">';
}
row = row + '<td class="post_title column-post_title"><strong>' + item.label + '</strong>';
row = row + '<input type="hidden" name="recipe_id[]" value="' + item.id + '">';
row = row + '<div class="row-actions"><span class="remove"><a href="#"  onclick="ROBORemoveRecipe(event)">Remove Recipe</a> | </span><span class="image"><a href="#" onclick="ROBOLoadMediaUpload(' + postId + ',' + item.id +')">Change Image</a></span></div></td>';
row = row + '<td class="thumbnail column-thumbnail"><div class="recipe-thumb" id="media-item-thumb-' + item.id  + '">';
if (item.thumb) {
	row = row + '<img style="width:auto; height:auto; max-width:32px; max-height:32px;" src="' + unescape(item.thumb[0]) + '" class="attachment-32x32 wp-post-image">';
	row = row + '</div></td></tr>';
}
if ($lastRow && $lastRow.hasClass('no-items')) {
	$table.empty();
}
$table.append(row);
}




var ROBOSetThumbnailHTML;
(function($){
	ROBOSetThumbnailHTML = function(src, targetId){
	var $thumb = $('#media-item-thumb-' + targetId);
	$thumb.empty();
	
	$thumb.append("<img src='" + src + "' style='width:auto; height:auto; max-width:32px; max-height:32px;'>");
	
		//$('.inside', '#postimagediv').html(html);
	};
})(jQuery);

function RecipeSetAsThumbnail(id, targetId, nonce){
	var $link = jQuery('a#wp-post-thumbnail-' + id);

	$link.text( setPostThumbnailL10n.saving );
	jQuery.post(ajaxurl, {
		action:"set-post-thumbnail", post_id: targetId, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	}, function(str){
		var win = window.dialogArguments || opener || parent || top;
		$link.text( setPostThumbnailL10n.setThumbnail );
		if ( str == '0' ) {
			alert( setPostThumbnailL10n.error );
		} else {
			$link.text( setPostThumbnailL10n.done );
			$link.fadeOut( 2000 );
		
				// Now you can use $ as a reference to jQuery without any problem
			
			var $img = jQuery("img", str);
			var src = $img.attr("src");
			//jQuery('#media-item-thumb-' + targetId).html(str);
			
		
			//win.WPSetThumbnailID(id);
			win.ROBOSetThumbnailHTML(src, targetId);
			win.tb_remove()
		}
	}
	);
}
function ROBOLoadMediaUpload(postId, targetId){
    tb_show('', 'media-upload.php?post_id=' + postId + '&target_id=' +  targetId + '&type=image&tab=feature_image_tab&TB_iframe=true' );
    return false;
}