function displayDistance(city, onLoaded) {
	var url = '/distance-calculator/?calc=&';
	var counter = 0;
	for (var i = 0; i < city.length; i++) {
		if (counter > 0)
			url += '&';
		url += 'city[]=';
		url += encodeURIComponent(city[i]);
		counter++;
	}
	
	var d = jQuery("<div>Загрузка...</div>").dialog({
		title:"Расчет расстояний",
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
			title:"Расчет расстояний",
			modal:true,
			zIndex: 10000,
			autoOpen: true,
			width: 'auto',
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
				if (typeof(redraw) != "undefined") {
					redraw();
				}
			}
		})
	});
}