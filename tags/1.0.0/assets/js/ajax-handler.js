;(function($){
	
	$(function() {
		$('.site').on('click', function() {
		$(this).addClass('active-site').siblings().removeClass('active-site');
	})
	})
	
})(jQuery)
