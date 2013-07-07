(function($){

	var getCurrentAttachments = function($container){
		var attachments = [];
		$container.find('.thumb-image-portfolio').each(function(){
			attachments.push($(this).attr('src'));
		});
		return attachments;
	};

	var appendImages = function (uploads,$container) {

		var newAttachments = uploads;

		$('.images-portfolio-container').empty();
		for(var i = 0; i < newAttachments.length; i++){
			$container.append(
				'<div class="images-portfolio-container">'
					+'<img src="'+newAttachments[i]+'" class="thumb-image-portfolio" style="width:80px"/>'
					+'<a href="#" class="delete-image-portfolio">Supprimer</a>'
				+'</div>'
			);						
		}
	};


	var deleteImage = function($img,$container){

		var currentImageAttr = $img.attr('src');
		var oldAttachments = $container.find('#meta_box_images').val();
		oldAttachments = oldAttachments.split(',')


		newAttachments = [];

		for (var i = 0; i < oldAttachments.length; i++){
			if(oldAttachments[i] != currentImageAttr){
				newAttachments.push(oldAttachments[i]);				
			}
		}

		$img.remove();
		appendImages(newAttachments,$container);
		$container.find('input').val(newAttachments.join(','));



	};
	
	var initFirstLoad = function(){
		$('.uploader-container').each(function(){
			var that = $(this);
			var attachments = $(this).find('#meta_box_images').val();
			appendImages(attachments.split(','), $(this));

			$(this).find('.images-portfolio-container').on('click','.delete-image-portfolio',function(e){

				e.preventDefault();
				var currentImage = $(this).parents('.images-portfolio-container').first().find('img');
				deleteImage(currentImage,that);
			});


		});


	}();


	$('.add-image-portbolio').on('click',function(e){
		

		var $el = $(this).parents('.uploader-container').first();		
		e.preventDefault();
		
		var uploader = wp.media({
			title : 'Envoyer une image',
			button : {
				text : 'Choisir un fichier'
			},
			multiple : true
		})
		.on('select',function(){
			var selection = uploader.state().get('selection');
			var attachments = [];
			selection.map(function(attachment){
				attachment = attachment.toJSON();
				attachments.push(attachment.url);

			});
			
			
			console.log(attachments.concat(getCurrentAttachments($el)));
			appendImages(attachments.concat(getCurrentAttachments($el)),$el);
			
			$el.find('input').val(getCurrentAttachments($el).join(','));

			/*
				var attacment = selection.first().toJSON();
				$('input', $el).val(attacment.url);
				$('img', $el).attr('src',attacment.url);
			*/

		})
		.open();
	});

})(jQuery);