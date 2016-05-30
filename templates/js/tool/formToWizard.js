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

        // 2
        $(element).before("<ul class=\"steps\" id='steps_"+form_id+"'></ul>");

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
            });
        }

        function createNextButton(i,form_id) {
            var stepName = "step_" + i + "_" + form_id;

            $("#" + stepName + "commands").append("<a href='#next' id='" + stepName + "Next' class='next'>Next ></a>");

            $("#" + stepName + "Next").bind("click", function(e) {
                $("#" + stepName).hide();
                $("#step_" + (i + 1)+"_" +form_id).show();
                if (i + 2 == count)
                    $(submmitButtonName).show();
                selectStep(i + 1,form_id);
            });
        }

        function selectStep(i,form_id) {
            $("#steps_"+form_id+" li").removeClass("current");
            $("#stepDesc_" + i + "_" +form_id).addClass("current");
            // console.log($("#stepDesc_" + i + "_" +form_id));
        }

    }
})(jQuery); 