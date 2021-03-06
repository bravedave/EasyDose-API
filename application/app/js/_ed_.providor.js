/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
$(document).ready( function() {
	$('thead > tr > td[role="sort-header"]').each( function( i, el) {
		$(el).addClass( 'pointer').on('click', _brayworth_.table.sort);

	});

	$('[data-provide="easyLog-table"]').each( function( i, el) {
		_ed_.easyLog.call( el);

	});

	$('[data-provide="add-comment"]').each( function( i, el) {
		_ed_.easyComment.call( el);

	});

});
