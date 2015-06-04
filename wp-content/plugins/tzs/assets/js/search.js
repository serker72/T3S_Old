function doSearchDialog(type, post, onLoaded) {
	doSearchDialog(type, post, onLoaded, false);
}

function doSearchDialog(type, post, onLoaded, following) {
	if ((type === 'cargo') || (type === 'transport'))
            var url = '/searchform/?cargo_trans='+type;
        else
            var url = '/searchprform/?product_auction='+type;
        
	if (following) {
		url += '&following=';
	}
	for (var k in post) {
		url += '&'+k+'=';
		url += encodeURIComponent(post[k]);
	}
	
	var d = jQuery("<div>Загрузка...</div>").dialog({
		title:"Поиск",
		modal:true,
		zIndex: 10000,
		autoOpen: true,
		width: 'auto',
		resizable: false,
		closeOnEscape: false,
		open: function(event, ui) {
			jQuery(this).find(".ui-dialog-titlebar-close", ui.dialog || ui).hide();
		}
	});
	
	jQuery("<div></div>").load(url, null, function() {
		if (onLoaded != null)
			onLoaded();
		jQuery(d).remove();
		jQuery(this).find("div[class=entry-content]").dialog({
			title:"Поиск",
			modal:true,
			zIndex: 10000,
			autoOpen: true,
			width: 'auto',
			resizable: false,
			buttons: {
				'Найти': function () {
					doSearch(type, this, post);
					jQuery(this).dialog("close");
				},
				'Закрыть': function () {
					jQuery(this).dialog("close");
				}
			},
			close: function (event, ui) {
				jQuery(this).remove();
			},
			open: function() {
				//if (typeof(onSearchLoaded) != "undefined") {
					//onSearchLoaded();
				//}
			}
		})
	});
}

function doSearch(type, d, post) {
    if ((type === 'cargo') || (type === 'transport')) {
        var frm = jQuery(d).find('form[name=search_form]')
	if (jQuery(frm).find('[tag=cargo_trans_following]').is(':checked'))
		jQuery(frm).attr('action', '/following/');
	else if (jQuery(frm).find('[tag=cargo_trans_cargo]').is(':checked'))
		jQuery(frm).attr('action', '/cargo/');
	else
		jQuery(frm).attr('action', '/transport/');
        jQuery(frm).submit();
    } else {
        var url = '';
        var url1 = '';
        
        if (post['cur_post_name'] !== undefined) {
            url = encodeURIComponent(post['cur_post_name']) + '/';
        }
	
        for (var k in post) {
            if (k !== 'cur_post_name') { 
                if (url1 !== '') { url1 += '&'; } 
		url1 += k+'=';
		url1 += encodeURIComponent(post[k]);
            }
	}
        
        if (url1 !== '') { 
            url += '?' + url1;
        }
        
        var frm = jQuery(d).find('form[name=search_pr_form]');
        jQuery(frm).attr('action', '/' + type + '/' + url);
/*        if (url === '') {
            jQuery(frm).attr('action', '/' + type + '/');
        } else {
            jQuery(frm).attr('action', '/' + url);
        }*/
        jQuery(frm).submit();
    }
}

function hijackLinks(post_) {
	jQuery('a[tag=page]').each(function() {
		var url = jQuery(this).attr('href');
		jQuery(this).click(function(){
			var frm = jQuery('<form method="POST" action="'+url+'">');
			for (var k in post_) {
				var input = jQuery('<input type="hidden"/>');
				jQuery(input).attr('name', k);
				jQuery(input).attr('value', post_[k]);
				jQuery(frm).append(input);
			}
			jQuery(document.body).append(frm);
			jQuery(frm).submit();
			
			return false;
		});
	});
}