(function($){

	var Memory = function(){

		this.datas = [];
		this.allow = true;
		var that = this;

		$('.datas li').each(function(){
			that.datas.push($(this).text());
		});

		this.datas = this.datas.concat(this.datas);

		this.initRender();

	};

	Memory.prototype.initRender = function(){

		var that = this;

		for(var i = 0; i < this.datas.length; i++){
			var random = Math.round(Math.random(0,2));
			var newCard = $('.card').first().clone();
			newCard.removeClass('first');
			
			if(/^http/.test(this.datas[i])) newCard.find('.item').html('<img src="'+this.datas[i]+'"/>');
			else newCard.find('.item').text(this.datas[i]);

			newCard.find('.item').attr('data-value',this.datas[i]);
			if (random == 0 ) $('.container').append(newCard);
			if (random == 1 ) $('.container').prepend(newCard);
			
		}
		$('.card.first').first().remove();	
			$('.container').on('click','.card',function(){
				if(that.allow){
					$(this).find('.mask').hide();
					$(this).find('.mask').parents('.card').first().addClass('visible');
					that.checkMatch();
				}
			});


	};

	Memory.prototype.checkMatch = function(){
	
		var value = [];
		var that = this;

		if($('.card.visible').length == 2){
			that.allow = false;
			$('.card.visible').each(function(){
				value.push($(this).find('.item').attr('data-value'));
			});

			if(value[0] == value[1]) {

				var timer = window.setInterval(function(){
					$('.card.visible').each(function(){
						var $value = $(this).find('.item').attr('data-value');
						if( $value == value[0] || $value == value[1]){
							$(this).removeClass('visible');
							$(this).hide();
							$(this).addClass('finish');
							that.allow = true;
							that.checkWin();
						}
					});				
				},2000);

			} else {
				
				var timer = window.setInterval(function(){
					$('.card').removeClass('visible');
					$('.mask').show();
					that.allow = true;
					clearInterval(timer);
				},2000);
								
			}

		}

	};
	

	Memory.prototype.checkWin = function () {

		if($('.card.finish').length == this.datas.length){
			$('#youWon').css('display','block');
		}

	};

	new Memory();





})(jQuery);