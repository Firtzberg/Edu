var selectManager ={
	CategoryLabel: 'Kategorije',
	SubjectLabel: 'Predmeti',
	errorMessage: 'Došlo je do greške. Provjerite vezu.',
	waitMessage: 'Učitavam...',
	onChange: function(select){
		var selectedOption = jQuery(select.options[select.selectedIndex]);
		var grupa = selectedOption.closest('optgroup').prop('label');
		if(grupa == selectManager.CategoryLabel)
			selectManager.onCategoryChosen(jQuery(select), selectedOption.val());
		else selectManager.onSubjectChosen(jQuery(select), selectedOption.val());
	},
	onSubjectChosen: function (select, subjectId) {
		select.siblings('div.sub').remove();
		jQuery('input[type=hidden][name=predmet_id]').val(subjectId);
	},
	onCategoryChosen: function (select, categoryId) {
		jQuery('input[type=hidden][name=predmet_id]').val('');
		var sibling;
		sibling = select.siblings('div.sub');
		if(sibling.length != 1)
		{
			sibling.remove();
			divElement = document.createElement('div');
			divElement.className = 'sub';
			sibling = jQuery(divElement);
			select.parent().append(sibling);
		}
		else
			sibling.empty();
		jQuery.ajax({
			url: '../Kategorija/' + categoryId + '/Children',
			dataType: 'json',
			type: 'get',
			beforeSend: function(){
				sibling.html(selectManager.waitMessage);
			},
			error: function(){
				sibling.html(selectManager.errorMessage);
			},
			success: function(data){
				var dropdown = document.createElement('select');
				dropdown.className = 'form-control form-group';
				dropdown = jQuery(dropdown);
				var optgroup;
				var opt;

				opt = document.createElement('option');
				opt.hidden = 'hidden';
				opt.selected = 'selected';
				opt.disabled = 'disabled';
				dropdown.append(opt);

				if(data.predmeti.length > 0){
					optgroup = document.createElement('optgroup');
					optgroup.label = selectManager.SubjectLabel;
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

				if(data.kategorije.length > 0){
					optgroup = document.createElement('optgroup');
					optgroup.label = selectManager.CategoryLabel;
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
				sibling.html(dropdown);
			}
		});
	}
}