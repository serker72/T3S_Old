var feedbackDialog = null;

function doReloadFeedback() {
	if (feedbackDialog == null)
		return;
	
	var url = '/feedback/?id='+feedback_id+'&pg='+feedback_page;
	
	jQuery('<div></div>').load(url, null, function() {
		feedbackDialog.empty();
		feedbackDialog.append(jQuery(this).find("div[class=entry-content]"));
	});
}

function doFeedbackDialog(onLoaded) {
	var url = '/feedback/?id='+feedback_id+'&pg='+feedback_page;
	
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
	
	jQuery('<div></div>').load(url, null, function() {
		if (onLoaded != null)
			onLoaded();
		var dWidth = jQuery(window).width() * 0.8;
		var dHeight = jQuery(window).height() * 0.8;
		
		jQuery(d).remove();
		feedbackDialog = jQuery(this).find("div[class=entry-content]").dialog({
			title:"Отзывы",
			modal:true,
			zIndex: 10000,
			autoOpen: true,
			width: dWidth,
			height: dHeight,
			resizable: false,
			buttons: {
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
