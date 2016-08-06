$.each({
	calculateQSIP: function(rounding_standard){
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


        $('#gross-amount').text(sum_including_total);

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

        rounded_gross_amount = sum_including_total;

        switch(rounding_standard){
         case 'Standard' :
           rounded_gross_amount =  Math.round(sum_including_total);
            break; 
         case 'Up' :
            rounded_gross_amount =  Math.ceil(sum_including_total);
            break; 
         case 'Down' :
            rounded_gross_amount =  Math.floor(sum_including_total);
            break;              
        }

        round_amount = sum_including_total-rounded_gross_amount;
        $('#round_amount').text(Math.abs((Math.round(round_amount * 100)/100).toFixed(2)));

        grand_total =  sum_including_total - discount_amount - round_amount;
        $('#net_amount').text(grand_total); 
	}

}, $.univ._import);