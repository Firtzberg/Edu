errorManager = {
	registry: [],

	register: function(mustPass, message){
		errorManager.registry.push({callback: mustPass, message: message});
	},
	submitCallback: function(){
		for(var key in errorManager.registry){
			if(!errorManager.registry[key].callback()){
				alert(errorManager.registry[key].message);
				return false;
			}
		}
		return true;
	},
	init: function () {
		jQuery('form').on('submit', errorManager.submitCallback);
	}
}

jQuery(errorManager.init);