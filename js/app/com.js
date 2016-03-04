var PAGE = function () {
	var fn = {
			isIE : function(){
				return document.all && !window.atob;
			},
			PAGE_NAME : function(){
				if($('html').attr('id')){
					return $('html').attr('id').replace(/^p\-/, '');
				}else{
					return false;
				}
			},
			setFeature: function(){
				$.getScript('js/lib/swiper2.js',function(){
					var wrapper = $('.feature .swiper-wrapper');
					var swiper = new Swiper('.swiper-container', {
						mode: 'vertical',
						pagination: '.swiper-pagination',
						simulateTouch: false,
						mousewheelControl: true,
						paginationClickable :true,
						useCSS3Transforms: false
					});

					var hHeight = $('header').height();
					var fHeight = $('footer').height();
					$(window).on('resize',function(){
						var container = $('.swiper-container');
						//var ratio = 1780/840;
						container.height($(window).height()-hHeight-fHeight-10);
						swiper.resizeFix();
					}).trigger('resize');
				})

			},
			setOtherwise: function(){
				$.getScript('js/lib/swiper2.js',function(){
					var swiper = new Swiper('.swiper-otherwise', {
						slidesPerView: 4,
						paginationClickable: true,
						simulateTouch: false
					});
					$('.otherwise .swiper-prev').on('click', function(e){
						e.preventDefault()
						swiper.swipePrev()
					})
					$('.otherwise .swiper-next').on('click', function(e){
						e.preventDefault()
						swiper.swipeNext()
					})
				});
			},
			setRelated: function(){
				$.getScript('js/lib/swiper2.js',function() {
					var swiper = new Swiper('.swiper-related', {
						paginationClickable: true,
						simulateTouch: false
					});
					$('.p_pics .swiper-prev').on('click', function(e){
						e.preventDefault()
						swiper.swipePrev()
					})
					$('.p_pics .swiper-next').on('click', function(e){
						e.preventDefault()
						swiper.swipeNext()
					})
				});
			},
			setFooter: function(){
				$('footer .info li').each(function(){
					var self = $(this);
					self.css('width',self.width()+10)
						.addClass('inactive');
				})
				$('footer .info').on('mouseenter','li',function(){
					$(this).removeClass('inactive')
						.siblings().addClass('inactive');
				}).on('mouseleave','li',function(){
					$(this).addClass('inactive');
				})
				//$('footer .info li:eq(0)').trigger('click');
			},
			identifyScreen : function(){
				$(window).on('resize',function(){
					if($(window).width()<=1220){
						$('body').addClass('minSize');
					}else{
						$('body').removeClass('minSize');
					}
				}).trigger('resize');
			},
			reload: function(){
				window.location.reload()
			}
		},
		init = function(){
			fn.setFooter();
			fn.identifyScreen();
			if(fn.PAGE_NAME()=='index'){
				fn.setFeature();
			}
			if(fn.PAGE_NAME()=='product_detail'){
				fn.setOtherwise();
			}
			if(fn.PAGE_NAME()=='album_detail'){
				fn.setOtherwise();
				fn.setRelated();
			}
		};
	return {
		fn : fn,
		init : init
	}
}();

$(function(){
	PAGE.init();
})