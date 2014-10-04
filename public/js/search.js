var formToken = jQuery('input[name=_token]').val();
var searchStringSelector = 'input[name=searchString]';
var collectionListSelector = '#collection-list';
var paginationLinksSelector = 'ul.pagination > li > a';
function getSearchString(){
	return jQuery(searchStringSelector).val()
}
function updateList(page){
	var searchString = getSearchString();
	jQuery.ajax({
		url: window.location.href + '/list/' + page + (searchString != '' ? '/'+ searchString : ''),
		dataType: 'html',
		type: 'post',
		data: {
			_token: formToken
		},
		success: function(data){
			jQuery(collectionListSelector).hide().html(data).fadeIn('fast');
			updatePaginationLinks();
		},
		error: function(){
			jQuery(collectionListSelector).hide().html('<h3>Došlo je do greške. Provjerite vezu.</h3>').fadeIn('fast');
		}
	});
}
function updatePaginationLinks(){
	jQuery(paginationLinksSelector).click(function(e){
		e.preventDefault();
		var page = jQuery(this).attr('href').split('page=');
		page = page[page.length-1];
		page = page.split('&')[0];
		updateList(page);
	});
};
var updateDelay = 500;
var updateTimer;
jQuery(document).ready(
	function(){
		jQuery(searchStringSelector).on('input', function(){
			clearTimeout(updateTimer);
			updateTimer = setTimeout(
				function(){
					updateList(1);
				},
				updateDelay
			);
		});
		updatePaginationLinks();
	}
);