(function($) {

	$.fn.quickpager = function(options){

		var defaults = {
			pageSelector: ".pager-page",
			step: 4,
			delay: 100,
			numeric: true,
			nextprev: true,
			auto:false,
			pause:4000,
			clickstop:true,
			controls: 'pagination',
			current: 'active',
			pager: $(".pagination"),
			page: 1
		};

		var options = $.extend(defaults, options);
		var step = options.step;
		var lower, upper;
		var children = $(this).children(options.pageSelector);
		var count = children.length;
		var obj, next, prev;
		var page = options.page;
		var timeout;
		var clicked = false;

		function show(){
			clearTimeout(timeout);
			lower = ((page-1) * step);
			upper = parseInt(lower) + parseInt(step);
			$(children).each(function(i){
				var child = $(this);
				child.hide();
				if(i>=lower && i<upper){ setTimeout(function(){ child.fadeIn('fast') }, ( i-( Math.floor(i/step) * step) )*options.delay ); }
				if(options.nextprev){
					if(upper >= count) { next.fadeOut('fast'); } else { next.fadeIn('fast'); };
					if(lower >= 1) { prev.fadeIn('fast'); } else { prev.fadeOut('fast'); };
				};
			});
			options.pager.find('li').removeClass(options.current);
			options.pager.find('li[data-index="'+page+'"]').addClass(options.current);

			if(options.auto){
				if(options.clickstop && clicked){}else{ timeout = setTimeout(auto,options.pause); };
			};
		};

		function auto(){
			if(upper <= count){ page++; show(); }
		};

		this.each(function(){

			obj = this;

			if(count>step){

				var pages = Math.floor(count/step);
				if((count/step) > pages) pages++;

				var ol = options.pager;

				if(options.nextprev){
					prev = $('<li class="prev"><a href="javascript:void(0);">&laquo;</a></li>')
						.hide()
						.appendTo(ol)
						.click(function(){
							clicked = true;
							page--;
							show();
						});
				};

				if(options.numeric){
					for(var i=1;i<=pages;i++){
					$('<li data-index="'+ i +'"><a href="javascript:void(0);">'+ i +'</a></li>')
						.appendTo(ol)
						.click(function(){
							clicked = true;
							page = $(this).attr('data-index');
							show();
						});
					};
				};

				if(options.nextprev){
					next = $('<li class="next"><a href="javascript:void(0);">&raquo;</a></li>')
						.hide()
						.appendTo(ol)
						.click(function(){
							clicked = true;
							page++;
							show();
						});
				};

				show();
			};
		});

	};

})(jQuery);
