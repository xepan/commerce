$.each({
    columnsum : function(selector,sumSelector){
    	total=0;
		$(selector).each(function() {
            text = $(this).text();
            text=text.replace(",",'');
		    total += parseFloat(text);
		});
        $(sumSelector).text(total);
    }
}, $.univ._import);