(function($){



	var replaceMain = function(newImage, oldImage){
		var oldSrc = oldImage.attr('src');
		var newSrc = newImage.attr('src');
		oldImage.attr('src',newSrc);
		newImage.attr('src',oldSrc);

	};

	

	$('.thumb-work img').on('click',function(){	
		
		var $mainPicture = $(this).parents('.thumb-work').first().find('.main-picture img');
		var that = $(this);
		replaceMain(that, $mainPicture);
	});

	


})(jQuery);