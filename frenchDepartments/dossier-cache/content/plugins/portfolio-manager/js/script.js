(function($){
	
	
	var $name = $('.name.column-name')
	$('.column-name').hide();
	var $table = $('.wp-list-table.widefat');
	
	if($name.length > 0){
		$table.find('.column-title a span').first().text('Nom');
		$table.find('#the-list tr').each(function(){
			var textName = 	$(this).find('.column-name').text();
			var title = $(this).find('.row-title').text(textName);
		});
	}



})(jQuery);