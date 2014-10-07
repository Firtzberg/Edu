var klijentManager = {
	url: '../Klijent/Suggestions',
	containerSelector: '#form-klijenti-container',
	addButtonSelector: '#form-klijent-add',
	itemPrefix: 'form-klijenti-item-',
	phoneSufix: '-broj_mobitela',
	nameSufix: '-ime',
	removeSufix: '-remove',
	total: 0,

	remove: function (buttonId) {
		if(buttonId.length <= klijentManager.itemPrefix.length + klijentManager.removeSufix.length)
			return false;
		if(buttonId.substring(0, klijentManager.itemPrefix.length) != klijentManager.itemPrefix)
			return false;
		if(buttonId.substring(buttonId.length - klijentManager.removeSufix.length, buttonId.length) != klijentManager.removeSufix)
			return false;
		var index = buttonId.substring(klijentManager.itemPrefix.length, buttonId.length - klijentManager.removeSufix.length);

		jQuery('#' + klijentManager.itemPrefix + index).remove();
		klijentManager.total--;

		index = parseInt(index);
		var key;
		var oldKey;
		var elementDiv;
		for (var i = index; i <= klijentManager.total; i++) {
			key = klijentManager.itemPrefix + i;
			oldKey = klijentManager.itemPrefix + (i + 1);
			elementDiv = jQuery('#' + oldKey);
			elementDiv.find('input[name=' + oldKey + klijentManager.nameSufix + ']').attr('name', key + klijentManager.nameSufix);
			elementDiv.find('input[name=' + oldKey + klijentManager.phoneSufix + ']').attr('name', key + klijentManager.phoneSufix);
			elementDiv.find('#' + oldKey + klijentManager.removeSufix).attr('id', key + klijentManager.removeSufix);
			elementDiv.attr('id', key);
		};
	},

	add: function (broj_mobitela, ime) {
		if(typeof broj_mobitela == 'undefined')
			broj_mobitela = '';
		if(typeof ime == 'undefined')
			ime = '';
		klijentManager.total++;
		var key = klijentManager.itemPrefix + klijentManager.total;
		var elementDiv = document.createElement('div');
		elementDiv.id = key;
		elementDiv.className = 'form-group row';

		var nestedDiv = document.createElement('div');
		nestedDiv.className = 'col-xs-4';
		var numberInput = document.createElement('input');
		numberInput.type = 'text';
		numberInput.name = key + klijentManager.phoneSufix;
		numberInput.required = 'required';
		numberInput.className = 'form-control';
		numberInput.placeholder = 'Broj mobitela';
		numberInput.value = broj_mobitela;
		nestedDiv.appendChild(numberInput);
		elementDiv.appendChild(nestedDiv);

		var nestedDiv = document.createElement('div');
		nestedDiv.className = 'col-xs-5';
		var nameInput = document.createElement('input');
		nameInput.type = 'text';
		nameInput.name = key + klijentManager.nameSufix;
		nameInput.required = 'required';
		nameInput.className = 'form-control';
		nameInput.placeholder = 'Ime i prezime';
		nameInput.value = ime;
		nestedDiv.appendChild(nameInput);
		elementDiv.appendChild(nestedDiv);

		var removeButton = document.createElement('button');
		removeButton.type = 'button';
		removeButton.innerHTML = 'Remove';
		removeButton.id = key + klijentManager.removeSufix;
		removeButton.className = 'btn btn-default col-xs-3';
		elementDiv.appendChild(removeButton);

		jQuery(klijentManager.containerSelector).append(elementDiv);

		klijentManager.adjust(jQuery(elementDiv));
	},

	adjust: function(jQueryElementDiv){
		divId = jQueryElementDiv.attr('id');
		if(divId.length <= klijentManager.itemPrefix.length)
			return false;
		if(divId.substring(0, klijentManager.itemPrefix.length) != klijentManager.itemPrefix)
			return false;
		var index = divId.substring(klijentManager.itemPrefix.length, divId.length);
		var numberInput = jQueryElementDiv.find('input[name=' + divId + klijentManager.phoneSufix + ']');
		if(numberInput.length != 1)
			return false;
		var nameInput = jQueryElementDiv.find('input[name=' + divId + klijentManager.nameSufix + ']');
		if(nameInput.length != 1)
			return false;
		var removeButton = jQueryElementDiv.find('#' + divId + klijentManager.removeSufix);
		if(removeButton.length != 1)
			return false;
		removeButton.click(function(){klijentManager.remove(this.id);});
		function sourceFunction(request, response){
			var elementId = this.element.attr('name');
			var isNumber = (elementId.substring(elementId.length - klijentManager.phoneSufix.length, elementId.length) == klijentManager.phoneSufix);
			var elementKey;
			if(isNumber)
				elementKey = elementId.substring(0, elementId.length - klijentManager.phoneSufix.length);
			else elementKey = elementId.substring(0, elementId.length - klijentManager.nameSufix.length);
			var numberInput = jQuery('input[name=' + elementKey + klijentManager.phoneSufix + ']');
			var nameInput = jQuery('input[name=' + elementKey + klijentManager.nameSufix + ']');
			jQuery.ajax({
				url: klijentManager.url,
				dataType: 'json',
				type: 'post',
				data: {
					broj: numberInput.val(),
					ime: nameInput.val(),
					_token: klijentManager.formToken
				},
				success: function(data){
					var phones = jQuery('input[name$=' + klijentManager.phoneSufix + '][readonly]').map(
						function(){return this.value;});
					data = data.filter(function(item){
						return jQuery.inArray(item.broj_mobitela, phones) == -1;
					});
					response(jQuery.map(data, function(item){
						return {
							value: (isNumber ? item.broj_mobitela : item.ime),
							broj_mobitela: item.broj_mobitela,
							ime: item.ime
						};
					}));
				}
			});
		};
		function selectFunction(event, ui){
			event.preventDefault();
			var elementId = jQuery(this).attr('name');
			var isNumber = (elementId.substring(elementId.length - klijentManager.phoneSufix.length, elementId.length) == klijentManager.phoneSufix);
			var elementKey;
			if(isNumber)
				elementKey = elementId.substring(0, elementId.length - klijentManager.phoneSufix.length);
			else elementKey = elementId.substring(0, elementId.length - klijentManager.nameSufix.length);

			var key;
			for(var i = 0; i < klijentManager.total; i++){
				key = klijentManager.itemPrefix + i;
				if(key == elementKey)
					continue;
				if(jQuery('input[name=' + key + klijentManager.phoneSufix + ']').val() == ui.item.broj_mobitela){
					alert('Već ste unijeli odabranog polaznika.');
					return;
				}
			}
			var numberInput = jQuery('input[name=' +elementKey + klijentManager.phoneSufix + ']');
			var nameInput = jQuery('input[name=' +elementKey + klijentManager.nameSufix + ']');
			numberInput.val(ui.item.broj_mobitela);
			nameInput.val(ui.item.ime);
			numberInput.attr('readonly', 'readonly');
			nameInput.attr('readonly', 'readonly');
		};
		numberInput.autocomplete({
			minLength: 3,
			source: sourceFunction,
			select: selectFunction
		});
		nameInput.autocomplete({
			minLength: 1,
			source: sourceFunction,
			select: selectFunction
		});
		return true;
	}
}

jQuery(document).ready(function () {
	klijentManager.formToken = jQuery('input[name=_token]').val();
	jQuery(klijentManager.addButtonSelector).on('click', function(){klijentManager.add();});
	jQuery('form').on('submit', function(){
		if(klijentManager.total < 1){
			alert('Potreban je barem jedan polaznik.');
			return false;
		}
		return true;
	});
	if(klijentManager.total == 0)
		klijentManager.add();
});