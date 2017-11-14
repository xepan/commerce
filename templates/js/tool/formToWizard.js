/* Created by jankoatwarpspeed.com */

(function($) {
    $.fn.formToWizard = function(options) {
        options = $.extend({  
            submitButton: "" 
        }, options); 
        
        var element = this;

        var form_id = $(element).closest('div.xepan-addtocartbutton-multi-step-wizard').attr('id');

        var steps = $(element).find("fieldset");
        var count = steps.size();
        var submmitButtonName = "#" + options.submitButton;
        $(submmitButtonName).hide();

        if(count == 1){
            $(element).find("legend").hide();
            console.log("warning: only one step is found so no need of multistep form ");
            return;
        }

        // 2
        $(element).before("<ul class=\"steps\" id='steps_"+form_id+"'></ul>");

        // on change remove file
        $(this).find('input.required:text, input.required:file, select.required, textarea.required').change(function(e){
            $(this).siblings('.atk-form-error').remove();
        });

        steps.each(function(i) {
            $(this).wrap("<div id='step_" + i + "_" + form_id + "'></div>");
            $(this).append("<p id='step_" + i + "_" + form_id + "commands'></p>");
            
            var name = $(this).find("legend").html();
            $("#steps_"+form_id).append("<li id='stepDesc_" + i + "_" + form_id + "'>Step " + (i + 1) + "<span>" + name + "</span></li>");


            if (i == 0) {
                createNextButton(i,form_id);
                selectStep(i,form_id);
            }
            else if (i == count - 1) {
                $("#step_" + i + "_" + form_id).hide();
                createPrevButton(i,form_id);
            }
            else {
                $("#step_" + i + "_" + form_id ).hide();
                createPrevButton(i,form_id);
                createNextButton(i,form_id);
            }
        });

        function createPrevButton(i,form_id) {
            var stepName = "step_" + i + "_" + form_id;
            $("#" + stepName + "commands").append("<a href='#prev' id='" + stepName + "Prev' class='prev'>< Back</a>");

            $("#" + stepName + "Prev").bind("click", function(e) {
                $("#" + stepName).hide();
                $("#step_" + (i - 1) + "_" + form_id).show();
                $(submmitButtonName).hide();
                selectStep(i - 1, form_id);

                // stop because it's redirecting to home base due to base url
                e.preventDefault();
            });
        }

        function createNextButton(i,form_id) {
            var stepName = "step_" + i + "_" + form_id;

            $("#" + stepName + "commands").append("<a href='#next' id='" + stepName + "Next' class='next'>Next ></a>");

            $("#" + stepName + "Next").bind("click", function(e) {
                // check all field are mendatory
                // $('#5dcd6ef9__ompletelister_addtocart_view_20_form').atk4_form('fieldError','logo','mandatory');
                section = $(this).closest('fieldset');
                var validation_on_field = 0;
                var total_validate_field = $(section).find('input.required:text, input.required:file, select.required, textarea.required').length;

                $(section).find('input.required:text, input.required:file, select.required, textarea.required')
                        .each(function(){
                        
                        user_input_val =  $(this).val();
                        
                        if($(this).attr("type") == "file"){
                            preview = $(this).siblings('.uploaded_files').find('.files-container div:nth-child(2)');
                            has_file = preview.length;
                            if(has_file){
                                user_input_val = $(preview).attr("data-url");
                                // console.log("has file" + user_input_val);
                                validation_on_field += 1;
                            }else{
                                user_input_val = 0;
                                // console.log("no file: "+ user_input_val);
                            }
                        }else{
                            validation_on_field +=1;
                        }

                        if(!user_input_val){
                            $(this).closest('.atk-form-row').removeClass('atk-effect-danger has-error');
                            $(this).closest('.atk-form-field').find('p.atk-form-error').remove();

                            $(this).closest('.atk-form-row').addClass('atk-effect-danger has-error');
                            $('<p class="field-error-template atk-form-error"><span class="field-error-text">mandatory</span></p>').appendTo($(this).closest('.atk-form-field'));
                            validation_on_field -= 1;
                        }else{
                            $(this).closest('.atk-form-row').removeClass('atk-effect-danger .has-error');
                            $(this).closest('.atk-form-field').find('p.atk-form-error').remove();
                        }

                });

                if(validation_on_field === total_validate_field){
                    $("#" + stepName).hide();
                    $("#step_" + (i + 1)+"_" +form_id).show();
                    if (i + 2 == count)
                        $(submmitButtonName).show();
                    selectStep(i + 1,form_id);
                }

                // stop because it's redirecting to home base due to base url
                e.preventDefault();
            });
        }

        function selectStep(i,form_id) {
            $("#steps_"+form_id+" li").removeClass("current");
            $("#stepDesc_" + i + "_" +form_id).addClass("current");
            // console.log($("#stepDesc_" + i + "_" +form_id));
        }

    }
})(jQuery); 