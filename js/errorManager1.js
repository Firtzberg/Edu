errorManager = {
	registry: [],

	register: function(mustPass, message){
		errorManager.registry.push({callback: mustPass, message: message});
	},
	unregister: function(mustNotPass) {
		var index = null;
		for(var key in errorManager.registry) {
			if(errorManager.registry[key].callback === mustNotPass) {
				index = key;
				break;
			}
		}
		if (index !== null) {
			errorManager.registry.splice(index, 1);
			return true;
		}
		return false
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