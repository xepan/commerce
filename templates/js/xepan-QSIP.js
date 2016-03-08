$.each({
	calculateQSIP: function(){

		// Get gross sum first
    	
    	total=0;
		$('.sum-amount').each(function() {
            text = $(this).text();
            text=text.replace(",",'');
		    total += parseFloat(text);
		});
        $('#gross-amount').text(total);


        // Manage discount
        if(isNaN($('#discount').find('input').val()) || !$('#discount').find('input').val()) 
            $('#discount').find('input').val(0);
        
        discount = parseFloat($('#discount').find('input').val());


        // Go Total
        $('#total').text(total-discount);

        // VAT (Sum of all tax amount up in table)
        tax_total=0;
		$('.sum-tax').each(function() {
            text = $(this).text();
            text=text.replace(",",'');
		    tax_total += parseFloat(text);
		});
        $('#tax_amount').text(tax_total);

        // Net Amount or grand total
        $('#net_amount').text(total - discount + tax_total);
	}
}, $.univ._import);