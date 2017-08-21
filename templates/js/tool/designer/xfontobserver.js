$.each({
	isLoaded: function(font_list){
		// var count = 0;
		var total_font = 0;
		// name_list = "";

		$.each(font_list,function(index,name){
			total_font = total_font + 1;
			var font = new FontFaceObserver(name);

			font.load().then(function () {
				console.log("Font "+name+" loaded");
			}, function () {
				console.log('Font not loaded');
				// $.univ().errorMessage(" Font "+name+" not loaded, reload page again or wait.");
				// count = count + 1;
				// name_list = name + ",";
			});
		});

		// if(count > 0){
		// 	$.univ().errorMessage(count+" Font not loaded, reload page again or wait. "+name_list);
		// }
		// alert(total_font);

	}
}, $.univ._import);