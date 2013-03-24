jQuery(document).ready(function($) {

	var activeSchemeColor = null;

	function changeColor(color) {
		if (activeSchemeColor && color != activeSchemeColor.css('background')) {
			var beforeColor = activeSchemeColor.css('background');
			activeSchemeColor.css({'background':color});
			if (beforeColor != activeSchemeColor.css('background')) {
				$('#new-scheme .input').css({'background':color});
				activeSchemeColor.find("input").val(color);
				$("#new-scheme-changed").val(1);			
			}
		}
		return false;
	}

	$("#new-scheme .scheme-color").click(function() {
		var color = $(this).find("input").val();
		$('#new-scheme').find(".input")
			.css({'background':color})
			.find("input").val(color);
	});

	$("#new-scheme .scheme-color").click(function() {
		var color = $(this).find("input").val();
		$('#new-scheme .input')
			.css({'background':color})
			.find("input").val(color);
		activeSchemeColor = $(this);
	});

	$('#new-scheme .new-color input').keypress(function(e) {
	    if (e.which == "13") { 
	        return changeColor($(this).val());
	    }       
	});

	$('#new-scheme .new-color input').change(function(e) {
		return changeColor($(this).val());     
	});

	$('.color-scheme-delete').click(function() {
		$(this).parents(".scheme").find(".delete-flag").val(1);
		$("#save-color-schemes").trigger("click");
		return false;
	});

	$('.color-scheme-embed').click(function() {
		$(this).parents(".scheme").find(".embed").toggle().find('.embed-code').focus().select();
		return false;
	});
});