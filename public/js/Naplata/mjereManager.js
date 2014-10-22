var mjereManager = {
	changeMjereSelector: 'input[type=checkbox][name=mjerechanged]',
	mjereContainerSelector: '#mjere-container',
	quantitySelector: 'input[name=stvarna_kolicina]',
	mjeraSelector: 'select[name=stvarna_mjera]',
	perPersonPerUnitDispalySelector: '#perPersonPerUnit',
	mjeraNameDisplaySelector: '.mjera_ime_display',
	quantityDisplaySelector: '.kolicina_display',
	perPersonDisplaySelector: '.perPersonDisplay',

	cijene: [],
	perPerson: 0,
	perPersonPerUnit: 0,
	isDefault: true,
	current: {
		cijena: null,
		quantity: 0
	},
	default: {
		cijena: null,
		quantity: 0
	},
	getPersonCount: function(){
		return missedManager.personCount;
	},
	changeState: function(toCurrent){
		if(mjereManager.isDefault != toCurrent)
			return;
		mjereManager.isDefault = !toCurrent;

		var cijena;
		var quantity;
		if(toCurrent){
			cijena = mjereManager.current.cijena;
			quantity = mjereManager.current.quantity;
		}
		else{
			cijena = mjereManager.default.cijena;
			quantity = mjereManager.default.quantity;
		}

		var personCount = mjereManager.getPersonCount();
		var perPersonPerUnit = cijena.individualno - (personCount - 1)*cijena.popust;
		if(perPersonPerUnit < cijena.minimalno)
			perPersonPerUnit = cijena.minimalno;

		jQuery(mjereManager.mjeraNameDisplaySelector).html(cijena.ime);
		jQuery(mjereManager.quantityDisplaySelector).html(quantity.toString());
		mjereManager.setPerPersonPerUnit(perPersonPerUnit);
	},
	setDefault: function(){
		mjereManager.changeState(false);
	},
	setCurrent: function(){
		mjereManager.changeState(true);
	},
	onPersonCountChanged: function(){
		var personCount = mjereManager.getPersonCount();
		var cijena;
		if(mjereManager.isDefault)
			cijena = mjereManager.default.cijena;
		else cijena = mjereManager.current.cijena;

		var perPersonPerUnit = cijena.individualno - (personCount - 1)*cijena.popust;
		if(perPersonPerUnit < cijena.minimalno)
			perPersonPerUnit = cijena.minimalno;

		mjereManager.setPerPersonPerUnit(perPersonPerUnit);
	},
	setCijena: function(cijena){
		if(mjereManager.current.cijena == cijena)
			return;
		mjereManager.current.cijena = cijena;

		if(mjereManager.isDefault)
			return;

		var personCount = mjereManager.getPersonCount();
		var perPersonPerUnit = cijena.individualno - (personCount - 1)*cijena.popust;
		if(perPersonPerUnit < cijena.minimalno)
			perPersonPerUnit = cijena.minimalno;

		jQuery(mjereManager.mjeraNameDisplaySelector).html(cijena.ime);
		mjereManager.setPerPersonPerUnit(perPersonPerUnit);
	},
	setPerPersonPerUnit: function(perPersonPerUnit){
		mjereManager.perPersonPerUnit = perPersonPerUnit;
		jQuery(mjereManager.perPersonPerUnitDispalySelector).html(perPersonPerUnit.toString());
		mjereManager.onPerPersonChanged();
	},
	setQuantity: function(quantity){
		if(mjereManager.current.quantity == quantity)
			return;
		mjereManager.current.quantity = quantity;

		if(mjereManager.isDefault)
			return;

		jQuery(mjereManager.quantityDisplaySelector).html(quantity.toString());
		mjereManager.onPerPersonChanged();
	},
	onPerPersonChanged: function(){
		if(mjereManager.isDefault)
			mjereManager.perPerson = mjereManager.default.quantity * mjereManager.perPersonPerUnit;
		else mjereManager.perPerson = mjereManager.current.quantity * mjereManager.perPersonPerUnit;

		jQuery(mjereManager.perPersonDisplaySelector).html(mjereManager.perPerson.toString());
		naplataManager.onPerPersonChanged();
	},
	getCijenaFromInput: function(){
		var mjeraId = jQuery(mjereManager.mjeraSelector).val();
		for(var key in mjereManager.cijene)
			if(mjereManager.cijene[key].id == mjeraId)
				return mjereManager.cijene[key];
		return null;
	},
	begin: function(cijene){
		mjereManager.cijene = cijene;

		jQuery(function(){
			mjereManager.current.cijena = mjereManager.default.cijena =
				mjereManager.getCijenaFromInput();
			mjereManager.current.quantity = mjereManager.default.quantity =
				jQuery(mjereManager.quantitySelector).val();
			mjereManager.setDefault();

			jQuery(mjereManager.quantitySelector).change(function(){
				mjereManager.setQuantity(jQuery(this).val());
			});

			jQuery(mjereManager.mjeraSelector).change(function(){
				mjereManager.setCijena(mjereManager.getCijenaFromInput());
			});

			jQuery(mjereManager.changeMjereSelector).change(function(){
				if(jQuery(this).is(':checked')){
					jQuery(mjereManager.mjereContainerSelector).show();
					mjereManager.setCurrent();
				}
				else{
					jQuery(mjereManager.mjereContainerSelector).hide();
					mjereManager.setDefault();
				}
			});
		});
	}
};

var missedManager = {
	personCountDisplaySelector: '.personCount',
	missedContainerSelector: '#klijenti-container',
	changeMissedSelector: 'input[type=checkbox][name=polaznicichanged]',

	personCount: 0,
	default: {
		personCount: 0
	},
	current: {
		personCount: 0
	},
	isDefault: true,

	setDefault: function(){
		if(missedManager.isDefault)
			return;
		missedManager.isDefault = true;
		missedManager.personCount = missedManager.default.personCount;
		missedManager.onPersonCountChanged();
	},
	setCurrent: function(){
		if(!missedManager.isDefault)
			return;
		missedManager.isDefault = false;
		missedManager.personCount = missedManager.current.personCount;
		missedManager.onPersonCountChanged();
	},
	setPersonCount: function(personCount){
		if(missedManager.current.personCount == personCount)
			return;
		missedManager.current.personCount = personCount;

		if(missedManager.isDefault)
			return;
		missedManager.personCount = personCount;
		missedManager.onPersonCountChanged();
	},
	onPersonCountChanged: function(){
		jQuery(missedManager.personCountDisplaySelector).html(missedManager.personCount.toString());
		naplataManager.onPersonCountChanged();
	},
	begin: function(){
		jQuery(function(){
			var checkboxes = jQuery('input[type=checkbox][name^=klijent-came-]');
			missedManager.personCount =
			missedManager.current.personCount =
			missedManager.default.personCount =
				checkboxes.length;

			checkboxes.change(function(){
				missedManager.setPersonCount(jQuery('input[type=checkbox][name^=klijent-came-]:not(:checked)').length);
			});

			jQuery(missedManager.changeMissedSelector).change(function(){
				if(jQuery(this).is(':checked')){
					jQuery(missedManager.missedContainerSelector).show();
					missedManager.setCurrent();
				}
				else{
					jQuery(missedManager.missedContainerSelector).hide();
					missedManager.setDefault();
				}
			});
		});
	}
};

missedManager.begin();

var naplataManager = {
	totalDisplaySelector: '.total_display',
	total: 0,
	onPersonCountChanged: function(){
		mjereManager.onPersonCountChanged();
	},
	onPerPersonChanged: function(){
		naplataManager.total = mjereManager.perPerson * missedManager.personCount;
		jQuery(naplataManager.totalDisplaySelector).html(naplataManager.total.toString());
	}
};