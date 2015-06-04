function loadScript() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&callback=autocomplete_initialize';
	document.body.appendChild(script);
}

function autocomplete_initialize() {
	if (isInitialized()) {
		jQuery('input[autocomplete=city]').each(function() {
			//new google.maps.places.Autocomplete((this), { types: ['(regions)'] });
			new google.maps.places.Autocomplete((this), { types: ['(cities)'] });
			jQuery(this).removeAttr('autocomplete');
		});
	}
}

function isInitialized() {
	return typeof google === 'object' && typeof google.maps === 'object';
}

jQuery(document).ready(function(){
	jQuery(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
	if (isInitialized()) {
		autocomplete_initialize();
	} else {
		loadScript();
	}
});