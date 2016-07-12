$.each({
	calculateQSIP: function(decimal_digit=2){
        // get gross sum of excluding tax
        sum_excluding_total=0;
        $('.sum-excluding-tax-amount').each(function(){
            text = $(this).text();
            text=text.replace(",",'');
            sum_excluding_total += parseFloat(text); 
        });
        // Get gross sum of including tax first
        sum_including_total=0;
        $('.sum-amount').each(function() {
            text = $(this).text();
            text = text.replace(/,/g,'');
            sum_including_total = sum_including_total + parseFloat(text);
        });


        $('#gross-amount').text(round(sum_including_total,decimal_digit));

        // Manage discount
        if($('#discount').find('input').length){
            discount_amount = 0;
            input_val = $.trim($('#discount').find('input').val());
            input_val = parseFloat(input_val.replace(",",''));

            if(isNaN(input_val)){
                $('#discount').find('input').val(discount_amount);
            }else{
                discount_amount = input_val;
            }

        }else{
            discount_amount = 0;
            text_value = $.trim($('#discount').text());
            text_value = parseFloat(text_value);

            if(isNaN(text_value)){
                $('#discount').text(discount_amount);
            }else{
                discount_amount = text_value;
            }

        }

        grand_total =  sum_including_total - discount_amount;
        $('#net_amount').text(grand_total);        
	}

}, $.univ._import);