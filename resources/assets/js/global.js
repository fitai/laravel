function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

// Mobile navigation open listener
$(document).on('click', '#nav-hamburger', function () {
  $('#nav-menu').addClass('active').show();
});

// Mobile navigation close listener
$(document).on('click', '#close-menu', function () {
  $('#nav-menu').removeClass('active').hide();
});

// Hide mobile navigation if click off screen
$(document).on('click', '#dynamic-content', function () {
	if ($('#nav-menu').hasClass('active')) {
		$('#nav-menu').removeClass('active').hide();
	}
});