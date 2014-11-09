var selectManager ={
	urlPrefix: '../Kategorija/',
	urlSufix: '/Children',
	categoryLabel: 'Kategorije',
	subjectLabel: 'Predmeti',
	errorMessage: 'Došlo je do greške. Provjerite vezu.',
	waitMessage: 'Učitavam...',
	selected: false,
	subjectInputSelector: 'input[type=hidden][name=predmet_id]',
	divContainerSelector: 'div#predmet-select',
        userInputSelector: '[name=instruktor_id]',
	onChange: function(select){
		var selectedOption = jQuery(select.options[select.selectedIndex]);
		var grupa = selectedOption.closest('optgroup').prop('label');
		if(grupa == selectManager.categoryLabel)
			selectManager.onCategoryChosen(jQuery(select), selectedOption.val());
		else selectManager.onSubjectChosen(jQuery(select), selectedOption.val());
	},
	onSubjectChosen: function (select, subjectId) {
		select.siblings('div.sub').remove();
		jQuery(selectManager.subjectInputSelector).val(subjectId);
		selectManager.selected = true;
	},
        getUserId: function(){
            return jQuery(selectManager.userInputSelector).val();
        },
	onCategoryChosen: function (select, categoryId) {
		selectManager.selected = false;
		jQuery(selectManager.subjectInputSelector).val('');
		select.siblings('div.sub').remove();
		divElement = document.createElement('div');
		divElement.className = 'sub';
		var sibling = jQuery(divElement);
		select.parent().append(sibling);
                var userId = selectManager.getUserId();

		jQuery.ajax({
			url: selectManager.urlPrefix + userId
                                + selectManager.urlSufix + '/' + categoryId,
			dataType: 'json',
			type: 'get',
			beforeSend: function(){
				sibling.html(selectManager.waitMessage);
			},
			error: function(){
				sibling.html(selectManager.errorMessage);
			},
			success: function(data){
                            var divElement = sibling.parent();
                            var level;
                            var dropdown;
                            var predmetIdInput = jQuery(selectManager.subjectInputSelector);
                            sibling.remove();
                            for(var key in data){
                                level = data[key];
                                dropdown = selectManager.dataToDropDown(level.content);

                                if(typeof level.selected != 'undefined')
                                {
                                        if(level.selected.type == 'kategorija')
                                                dropdown.find('optgroup[label='+selectManager.categoryLabel+'] option[value='+
                                                        level.selected.id+']').attr('selected', 'selected');
                                        else{
                                                dropdown.find('optgroup[label='+selectManager.subjectLabel+'] option[value='+
                                                        level.selected.id+']').attr('selected', 'selected');
                                                predmetIdInput.val(level.selected.id);
                                                selectManager.selected = true;
                                        }
                                }
                                nestedDiv = document.createElement('div');
                                nestedDiv.className = 'sub';
                                nestedDiv = jQuery(nestedDiv);
                                nestedDiv.append(dropdown);

                                divElement.append(nestedDiv);
                                divElement = nestedDiv;
                            }
				sibling.html(selectManager.dataToDropDown(data));
			}
		});
	},
	onUserChosen: function (select, userId) {
		selectManager.selected = false;
		jQuery(selectManager.subjectInputSelector).val('');
		jQuery(selectManager.divContainerSelector).children('div.sub').remove();
		divElement = document.createElement('div');
		divElement.className = 'sub';
		var sibling = jQuery(divElement);
		jQuery(selectManager.divContainerSelector).append(sibling);

		jQuery.ajax({
			url: selectManager.urlPrefix + userId
                                + selectManager.urlSufix,
			dataType: 'json',
			type: 'get',
			beforeSend: function(){
				sibling.html(selectManager.waitMessage);
			},
			error: function(){
				sibling.html(selectManager.errorMessage);
			},
			success: function(data){
                            var divElement = sibling.parent();
                            var level;
                            var dropdown;
                            var predmetIdInput = jQuery(selectManager.subjectInputSelector);
                            sibling.remove();
                            for(var key in data){
                                level = data[key];
                                dropdown = selectManager.dataToDropDown(level.content);

                                if(typeof level.selected != 'undefined')
                                {
                                        if(level.selected.type == 'kategorija')
                                                dropdown.find('optgroup[label='+selectManager.categoryLabel+'] option[value='+
                                                        level.selected.id+']').attr('selected', 'selected');
                                        else{
                                                dropdown.find('optgroup[label='+selectManager.subjectLabel+'] option[value='+
                                                        level.selected.id+']').attr('selected', 'selected');
                                                predmetIdInput.val(level.selected.id);
                                                selectManager.selected = true;
                                        }
                                }
                                nestedDiv = document.createElement('div');
                                nestedDiv.className = 'sub';
                                nestedDiv = jQuery(nestedDiv);
                                nestedDiv.append(dropdown);

                                divElement.append(nestedDiv);
                                divElement = nestedDiv;
                            }
				sibling.html(selectManager.dataToDropDown(data));
			}
		});
	},
	dataToDropDown: function(data){
		var dropdown = document.createElement('select');
		dropdown.className = 'form-control form-group';
		dropdown.required = 'required';
		dropdown = jQuery(dropdown);
		var optgroup;
		var opt;

		opt = document.createElement('option');
		opt.hidden = 'hidden';
		opt.selected = 'selected';
		opt.disabled = 'disabled';
		dropdown.append(opt);

		if(typeof data.predmeti != 'undefined' && data.predmeti.length > 0){
			optgroup = document.createElement('optgroup');
			optgroup.label = selectManager.subjectLabel;
			optgroup = jQuery(optgroup);
			for(var key in data.predmeti){
				opt = document.createElement('option');
				opt.value = data.predmeti[key].id;
				opt = jQuery(opt);
				opt.html(data.predmeti[key].ime);
				optgroup.append(opt);
			}
			dropdown.append(optgroup);
		}

		if(typeof data.kategorije != 'undefined' && data.kategorije.length > 0){
			optgroup = document.createElement('optgroup');
			optgroup.label = selectManager.categoryLabel;
			optgroup = jQuery(optgroup);
			for(var key in data.kategorije){
				opt = document.createElement('option');
				opt.value = data.kategorije[key].id;
				opt = jQuery(opt);
				opt.html(data.kategorije[key].ime);
				optgroup.append(opt);
			}
			dropdown.append(optgroup);
		}

		dropdown.change(function(){selectManager.onChange(this);});
		return dropdown;
	},
	isPredmetSelected: function() {
		return selectManager.selected;
	},
	init: function(myComplexStructure){
		errorManager.register(selectManager.isPredmetSelected, 'Niste odabrali predmet.');

		var divElement = jQuery(selectManager.divContainerSelector);

		var predmetIdInput = document.createElement('input');
		predmetIdInput.type = 'hidden';
		predmetIdInput.name = 'predmet_id';
		predmetIdInput = jQuery(predmetIdInput);
		divElement.append(predmetIdInput);

		var level;
		var dropdown;
		var opt;
		var nestedDiv;
		for(var key in myComplexStructure){
			level = myComplexStructure[key];
			dropdown = selectManager.dataToDropDown(level.content);

			if(typeof level.selected != 'undefined')
			{
				if(level.selected.type == 'kategorija')
					dropdown.find('optgroup[label='+selectManager.categoryLabel+'] option[value='+
						level.selected.id+']').attr('selected', 'selected');
				else{
					dropdown.find('optgroup[label='+selectManager.subjectLabel+'] option[value='+
						level.selected.id+']').attr('selected', 'selected');
					predmetIdInput.val(level.selected.id);
					selectManager.selected = true;
				}
			}

			nestedDiv = document.createElement('div');
			nestedDiv.className = 'sub';
			nestedDiv = jQuery(nestedDiv);
			nestedDiv.append(dropdown);

			divElement.append(nestedDiv);
			divElement = nestedDiv;
		}
                jQuery(function(){
                    jQuery(selectManager.userInputSelector).change(function(){
                        selectManager.onUserChosen(this, jQuery(this).val());
                    });
                });
	}
};