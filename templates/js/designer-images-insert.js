$.each({
        makeInsertBtn: function(){
                //Load Designer Tool
            designer = $(".xshop-designer-tool").data("ui-xepan_xshopdesigner");
            //Image Hover add or replace

            // button hover effects
            $('.xshop-designer-item-images').hover( function(){
                  $(this).find('.xshop-designer-add-replace').fadeIn(300);
            },function(){
                  $(this).find('.xshop-designer-add-replace').fadeOut(100);
            });

            // replace button click
            $(".xshop-designer-add-replace-button").click(function(){
                  var self = this;
                  image_src = $(this).attr('selectedimagesrc');
                  image_id = $(this).data('image-id');
                  var opener = $('.xshop-designer-image-toolbtn').data('tool');

                  // var image_replace_mode = "fit_to_scale";

                  // if(opener.designer_tool.current_selected_component.options.replace_mode){
                  //       image_replace_mode = opener.designer_tool.current_selected_component.options.replace_mode;
                  // }

                  // Case #1: Insert Image Clicked (In case existing image selected, it is made undefined on btn click) 
                  if(opener.designer_tool.current_selected_component == undefined){
                        opener.addImage(image_src);
                        $(this).closest('.dialog').dialog('close');
                  }
                  // else if(opener.designer_tool.current_selected_component.options.mask_added){
                  //       // mask added condition
                  //       var current_image_option = opener.designer_tool.current_selected_component.options;
                  //       var current_image = opener.designer_tool.current_selected_component.element;
                  //       // Add New Images
                  //       mask_image = current_image_option.mask_options.url = image_src;
                  //       opener.designer_tool.current_selected_component.updateMask();
                  //       // mask_image.options.is_mask_image = true;
                  //       //Append Mask Image to Current Selected Image
                  //       // $(mask_image.element).appendTo(current_image);
                  //       // $(mask_image.element).css('top',0);
                  //       // $(mask_image.element).draggable({
                  //       //  containment: current_image
                  //       // });
                  //       //Close the Dialog Box
                  //       $(this).closest('.dialog').dialog('close');
                  //       //Add Mask Options to Current Selected Image
                  // }
                  else{
                        // Case #2: Image Replace clicked or background btn clicked
                        // Common portion, set source etc ...
                        var csc = opener.designer_tool.current_selected_component; 

                        csc.options.replace_image = true;
                        csc.options.crop = false;
                        csc.options.url = image_src;
                        
                        // Cropping specific section 
                        // In Background or in image replace_mode set so 
                        if(csc.options.type == "BackgroundImage" || csc.options.replace_mode!='fit_to_scale'){
                              o = opener.designer_tool.current_selected_component.options;
                              xx= $('<div class="xshop-designer-backgroundimage-crop"></div>');
                              crop_image = $('<img class="xshop-img" src='+image_src+'></img>').appendTo(xx);
                              x = $('<div></div>').appendTo(crop_image);
                              y = $('<div></div>').appendTo(crop_image);
                              width = $('<div></div>').appendTo(crop_image);
                              height = $('<div></div>').appendTo(crop_image);
                              $(this).closest('.dialog').dialog('close');

                              

                              x_label = $('<label>x: </label>').appendTo(xx);
                              x_f = $('<input style="width:100px;"/>').appendTo(x_label);
                              y_label = $('<label>y: </label>').appendTo(xx);
                              y_f = $('<input style="width:100px;"/>').appendTo(y_label);
                              
                              width_label = $('<label>width: </label>').appendTo(xx);
                              width_f = $('<input style="width:100px;"/>').appendTo(width_label);

                              height_label = $('<label>height: </label>').appendTo(xx);
                              height_f = $('<input style="width:100px;"/>').appendTo(height_label);


                              var req_aspect_ratio = csc.options.width / csc.options.height;
                              if(csc.options.type =='BackgroundImage')
                                    req_aspect_ratio = opener.designer_tool.canvas.width() / opener.designer_tool.canvas.height();
                              if(csc.options.replace_mode && csc.options.replace_mode == 'free_crop')
                                    req_aspect_ratio = false;

                              xx.dialog({
                                    minWidth: 800,
                                    modal:true,
                                    open: function( event, ui ) {
                                          $(crop_image).cropper({
                                                aspectRatio: req_aspect_ratio,
                                                multiple: true,
                                                data: {
                                                      x: o.crop == true? o.crop_x: 0,
                                                      y: o.crop == true? o.crop_y: 0,
                                                      width: o.crop == true? o.crop_width: $(crop_image).width(),
                                                      height: o.crop == true? o.crop_height: $(crop_image).height()
                                                },
                                                done: function(data) {
                                                      $(x).val(Math.round(data.x));
                                                      $(y).val(Math.round(data.y));
                                                      $(width).val(Math.round(data.width));
                                                      $(height).val(Math.round(data.height));

                                                      $(x_f).val(Math.round(data.x));
                                                      $(y_f).val(Math.round(data.y));
                                                      $(width_f).val(Math.round(data.width));
                                                      $(height_f).val(Math.round(data.height));
                                                      // console.log(Math.round(data.width));
                                                }
                                          });

                                          var readData = function(){
                                                var vals = {x: $(x_f).val(), y: $(y_f).val(), width: $(width_f).val(), height: $(height_f).val()};
                                                $(crop_image).cropper('setData',vals);
                                          }

                                          x_f.on('change',readData);
                                          y_f.on('change',readData);
                                          width_f.on('change',readData);
                                          height_f.on('change',readData);
                                    },
                                    close: function( event, ui ) {
                                    //add new background images
                                    // opener.designer_tool.pages_and_layouts[opener.designer_tool.current_page][opener.designer_tool.current_layout].components.push(opener.designer_tool.current_selected_component);
                                    // console.log(opener);
                                    },
                                    buttons: {
                                          Continue: function() {
                                                opener.designer_tool.current_selected_component.options.crop = true;
                                                opener.designer_tool.current_selected_component.options.crop_x = $(x).val();
                                                opener.designer_tool.current_selected_component.options.crop_y = $(y).val();
                                                opener.designer_tool.current_selected_component.options.crop_width = $(width).val();
                                                opener.designer_tool.current_selected_component.options.crop_height = $(height).val();
                                                opener.designer_tool.current_selected_component.render(opener.designer_tool);
                                                $(this).dialog("close"); //closing on Ok click
                                          }
                                    }
                              });
                        }else{
                              $(this).closest('.dialog').dialog('close');
                              opener.designer_tool.current_selected_component.render(opener.designer_tool,true);
                        }
                        // console.log(opener.designer_tool.current_selected_component);
                        // console.log('undefinedd');
                        }
                  });

            $('.xshop-designer-remove-member-image-button').click(function(event) {
                  var count = parseInt($(this).attr('img-no-of-time-used'));
                  var img_id = $(this).attr('data-image-id');
                  var granted = true;
                  if(count > 0){
                        if(!confirm('Are You Sure this image used in '+count+' Designs')){granted = false;
                        }
                  }
            // alert('Only delete if not in use by search url like in item designs');
            });
      }

    }, $.univ._import);
