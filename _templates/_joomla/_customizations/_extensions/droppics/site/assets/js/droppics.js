/**
 * Droppics
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
var editor;
if(typeof(Droppics)==='undefined'){
    var Droppics={};
    Droppics.can = {};
    Droppics.can.create=false;
    Droppics.can.edit=false;
    Droppics.can.delete=false;
    Droppics.baseurl='';
    Droppics.selection = {};
    Droppics.custom = false;
    Droppics.editionMode = false;
}

jQuery(document).ready(function($) {
        if(Droppics.can.edit){
            $('#preview').sortable({
                placeholder: 'highlight',
                revert: 300,
                distance: 15,
                items: ".wimg",
                helper : 'clone',
                update : function(){
                    var json='';
                    $.each($('#preview .img'),function(i,val){
                        if(json!==''){
                            json+=',';
                        }
                        json+='"'+i+'":'+$(val).data('id-picture');
                    });
                    json = '{'+json+'}';
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=files.reorder&order="+encodeURIComponent(json),
                        type    :   "POST"
                    });
                },
                /** Prevent firefox bug positionnement **/
                start: function (event, ui) {
                    var userAgent = navigator.userAgent.toLowerCase();
                    if( ui.helper !== undefined && userAgent.match(/firefox/) ){
                        ui.helper.css('position','absolute').css('margin-top', $(window).scrollTop() );
                    }
                },
                beforeStop: function (event, ui) {
                    var userAgent = navigator.userAgent.toLowerCase();
                    if( ui.offset !== undefined && userAgent.match(/firefox/) ){
                        ui.helper.css('margin-top', 0);
                    }
                }
            });
            $('#preview').disableSelection();
        }

/**
         * Reload a gallery preview
         * @param id_gallery
         */
         Droppics.updatepreview = updatepreview = function(id_gallery){
            if(typeof(id_gallery)==="undefined" || id_gallery===null){
                id_gallery = $('#gallerieslist li.active').data('id-gallery');
                $('input[name=id_gallery]').val(id_gallery);
            }else{
                $('input[name=id_gallery]').val(id_gallery);
            }
            loading('#wpreview');
            $.ajax({
                url     :   "index.php?option=com_droppics&view=gallery&format=raw&id_gallery="+id_gallery,
                type    :   "POST"
            }).done(function(data){
                $('#preview').html($(data));
                if(Droppics.can.edit){
                    $('<div id="dropbox"><span class="message">'+Joomla.JText._('COM_DROPPICS_JS_DROP_FILES_HERE', 'Drop images here to upload')+'.<i> '+Joomla.JText._('COM_DROPPICS_JS_USE_UPLOAD_BUTTON', 'Or use the button below')+'</i></span><input class="hide" type="file" id="upload_input" multiple=""><a href="" id="upload_button" class="btn btn-large btn-primary">'+Joomla.JText._('COM_DROPPICS_JS_SELECT_FILES', 'Select files')+'</a></div><div class="clr"></div>').appendTo('#preview');
                    $('#preview').sortable('refresh');
                }

                initUploadBtn();
                initThemeBtn();
                initImages();
                initInsertBtns();

                showThemes();
                loadParams();
                $('#wpreview').unbind();
                initDropbox($('#wpreview'));
                theme = $('input[name=theme]').val();
                $('.themesblock .selected').removeClass('selected');
                $('.themesblock a[data-theme='+theme+']').addClass('selected');
                rloading('#wpreview');

                if(typeof(Droppics.selection.selection)!=='undefined' && Droppics.selection.selection!=='' && typeof(Droppics.selection.picture)!=='undefined' && Droppics.selection.picture!==null){
                    $('#preview img[data-id-picture='+Droppics.selection.picture+']').trigger('click');
                    $('input[name="imgp_radius"]').val(parseInt($(Droppics.selection.selection).css('border-radius')||0));
                    $('input[name="imgp_border"]').val(parseInt($(Droppics.selection.selection).css('border-width')||0));
                    $('input[name="imgp_border_color"]').val(rgb2hex($(Droppics.selection.selection).css('border-color')));
                    $('input[name="imgp_shadow"]').val($(Droppics.selection.selection).css('box-shadow').replace(/^.*(rgba?\([^)]+\)) ([0-9]+)px.*$/,'$2')||'0').trigger('change');
                    $('input[name="imgp_shadow_color"]').val(rgb2hex($(Droppics.selection.selection).css('box-shadow').replace(/^.*(rgba?\([^)]+\)).*$/,'$1')||'#CCCCCC')).trigger('change');
                    $('input[name="imgp_margin_left"]').val(parseInt($(Droppics.selection.selection).css('margin-left')||4));
                    $('input[name="imgp_margin_right"]').val(parseInt($(Droppics.selection.selection).css('margin-right')||4));
                    $('input[name="imgp_margin_top"]').val(parseInt($(Droppics.selection.selection).css('margin-top')||4));
                    $('input[name="imgp_margin_bottom"]').val(parseInt($(Droppics.selection.selection).css('margin-bottom')||4));

                    $('#imagealign button.active').data('align')
                    $('#imagealign button').removeClass('active');
                    switch ($(Droppics.selection.selection).css('float')){
                        case 'left':
                            $('#imagealign button[data-align="left"]').addClass('active');
                            break;
                        case 'right':
                            $('#imagealign button[data-align="right"]').addClass('active');
                            break;
                        default:
                            $('#imagealign button[data-align="none"]').addClass('active');
                            break;
                    }

                    //onclick
                    if($(Droppics.selection.selection).data('droppicslightbox')=='lightbox'){
                        $('#imgp_click').val('lightbox');
                    }else if($(Droppics.selection.imgparent).is('a')){
                            $('#imgp_click').val('custom').trigger('change');
                            $('#click_content_custom_id').val($(Droppics.selection.imgparent).first('a').attr('href'));
                    }

                    if($(Droppics.selection.imgparent).first('a').attr('target')==='_blank'){
                        $('#imgp_click_target').val('_blank');
                    }else if($(Droppics.selection.imgparent).first('a').hasClass('droppicslightboxsingle')){
                        $('#imgp_click_target').val('lightbox');
                    }else{
                        $('#imgp_click_target').val('current');
                    }

                    if($(Droppics.selection.selection).data('droppicssource')==='original'){
                        $('#imgp_source input[value="original"]').attr('checked','checked');
                    }else if($(Droppics.selection.selection).data('droppicssource')==='thumbnail'){
                        $('#imgp_source input[value="thumbnail"]').attr('checked','checked');
                    }else{
                        Droppics.custom = $(Droppics.selection.selection).data('droppicssource');
                    }
                    Droppics.selection = {};
                }

            });
        };


        $('#imgp_source input[value="custom"]').on('change',function(){
            $('#newCustomSize').show();
            $('#insertimage').addClass('disabled');
        });

        $('#imgp_source input:not([value="custom"])').on('change',function(){
            $('#newCustomSize').hide();
            $('#insertimage').removeClass('disabled');
            id_gallery = $('input[name=id_gallery]').val();
            tst = new Date().getTime();
            switch ($('#imgp_source input:checked').val()){
                case 'thumbnail':
                    $('#singleimage').attr('src',Droppics.baseurl+'/galeria/'+id_gallery+'/thumbnails/'+$('.selimg.selected img').data('file')+'?'+tst);
                    break;
                case 'original':
                    $('#singleimage').attr('src',Droppics.baseurl+'/galeria/'+id_gallery+'/'+$('.selimg.selected img').data('file')+'?'+tst);
                    break;
                default:
                    custom = $('#imgp_source input:checked').val();
                    custom = custom.replace('custom_','');
                    infos = $('.selimg.selected img').data('customs');
                    result = $.grep(infos, function(e){
                        return e.id == custom;
                    });
                    $('#singleimage').attr('src',Droppics.baseurl+'/galeria/'+id_gallery+'/custom/'+result[0].file+'?'+tst);
                    break;
            }
        });

        /* Create custom size button */
        $('#applyNewCustomSize').click(function(){
            currentPicture = $('#preview .selimg.selected img');
            $.ajax({
                url     :   "index.php?option=com_droppics&task=files.customResize",
                type    :   "POST",
                data    :   {
                    id_picture  :   currentPicture.data('id-picture'),
                    width       :   $('#customWidth').val(),
                    height      :   $('#customHeight').val(),
                    filename    :   $('#customFilename').val()
                }
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
                    cloned = $('#imgp_source .template').clone(true).insertBefore('#imgp_source .template').removeClass('template').show();
                    cloned.find('span').html(result.datas.file+' ('+result.datas.width+'x'+result.datas.height+')');
                    customs = $(currentPicture).data('customs');
                    customs.push(result.datas);
                    $(currentPicture).data('customs',customs);
                    cloned.find('input').attr('checked','checked').val('custom_'+result.datas.id).trigger('change');
                }else{
                    bootbox.alert(result.response);
                }
            });
        });

        /* init menu actions */
        initMenu();

        /* Load nestable */
        $('.nested').nestable().on('change', function(event, e){
            pk = $(e).data('id-gallery');
            if($(e).prev('li').length===0){
                position = 'first-child';
                if($(e).parents('li').length===0){
                    //root
                    ref = 0;
                }else{
                    ref = $(e).parents('li').data('id-gallery');
                }
            }else{
                position = 'after';
                ref = $(e).prev('li').data('id-gallery');
            }
            $.ajax({
                url     :   "index.php?option=com_droppics&task=categories.order&pk="+pk+"&position="+position+"&ref="+ref,
                type    :   "POST"
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
    //                console.log(result.datas);
                }else{
                    bootbox.alert(result.response);
                }
            });
        });
        if(Droppics.collapse===true){
            $('.nested').nestable('collapseAll');
        }

        //Check what is loaded via editor
        if(typeof(gcaninsert)!=='undefined' && gcaninsert===true){
            if(typeof(window.parent.tinyMCE)!=='undefined'){
                content = window.parent.tinyMCE.get(e_name).selection.getContent();
                imgparent = window.parent.tinyMCE.get(e_name).selection.getNode().parentNode;
                exp = '<img.*data\-droppicspicture="([0-9]+)".*?>';
                picture = content.match(exp);
                exp = '<img.*data\-droppicscategory="([0-9]+)".*?>';
                category = content.match(exp);
                exp = '<img.*data\-droppicsgallery="([0-9]+)".*?>';
                gallery = content.match(exp);
                Droppics.selection = new Array();
                Droppics.selection.content = content;
                Droppics.selection.imgparent = imgparent;

                if(picture!==null && category!=null){
                    if(picture!==null){
                        elem = $(content).filter('img[data-droppicspicture='+picture[1]+']');
                        Droppics.selection.selection = elem;
                        Droppics.selection.picture = picture[1];
                    }
                    if(category!=null){
                        Droppics.selection.gallery = category[1];
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+category[1]+'"]').addClass('active');
                        updatepreview(category[1]);
                    }
                }else if(gallery!==null){
                    Droppics.selection.gallery = gallery[1];
                    $('#gallerieslist li').removeClass('active');
                    $('#gallerieslist li[data-id-gallery="'+gallery[1]+'"]').addClass('active');
                    updatepreview(gallery[1]);
                }else{
                    updatepreview();
                }


            }
        }else{
            /* Load gallery */
            updatepreview();
        }
        initDeleteBtn();

        function initImages(){
            $(document).unbind('click.window').bind('click.window',function(e){
                if( $(e.target).is('#rightcol') ||
                    $(e.target).parents('#rightcol').length>0 ||
                    $(e.target).is('.modal-backdrop') ||
                    $(e.target).is('.cke_dialog_background_cover') ||
                    $(e.target).parents('.bootbox.modal').length>0 ||
                    $(e.target).parents('.cke_inner').length>0  ||
                    $(e.target).parents('.cke_dialog').length>0 ||
                    Droppics.editionMode ===true
                    ){
                    return;
                }
                $('#preview .selimg.selected').removeClass('selected');
                showThemes();
            });

            $('#preview .img').unbind('click').click(function(e){
               iselected = $(this).parent().find('#preview .selimg.selected').length;
                //Allow multiselect
                if($(this).parents('.wimg').find('.selimg').hasClass('selected')){
                    if (!(e.ctrlKey || e.metaKey)){
                        $('#preview .selimg.selected').removeClass('selected');
                    }
                    $(this).parents('.wimg').find('.selimg').removeClass('selected');
                }else{
                    if (!(e.ctrlKey || e.metaKey)){
                        $('#preview .selimg.selected').removeClass('selected');
                    }
                    $(this).parents('.wimg').find('.selimg').addClass('selected');
                }

               if($('#preview .selimg.selected').length==1){
                   $('#preview').addClass('somethingselected');
                   showImage(this);
               }else if ($('#preview .selimg.selected').length>1){
                   showImages();
               }else{
                   showThemes();
               }
               e.stopPropagation();
            });

            $('#preview .img .wbtn').remove();
        }

        function showThemes(){
            $('#rightcol').animate({'width':'21%'});
            $('#pwrapper').animate({'width':'53%'});
            $('.imageblock').fadeOut(function(){$('.themesblock').fadeIn();});
            $('#insertimage').fadeOut(function(){$('#insertgallery').fadeIn();});
            $('#preview').removeClass('somethingselected');
        }

        function showImage(e){
            //delete ckeditor if exists
            if(typeof(editor)!=='undefined' && editor!==null){
                editor.destroy();
                editor = null;
            }

            $('#pwrapper').animate({'width':'40%'});
            $('#imageparameters').fadeIn();
            $('#imageedit').fadeIn();
            $('#imageblock').fadeIn();
            $('.themesblock').fadeOut(function(){$('.imageblock').fadeIn();});
            $('#insertgallery').fadeOut(function(){
                $('#insertimage').fadeIn();
                $('#editImage').fadeIn();
            });

            $('#rightcol').animate({'width':'34%'},{complete:function(){
                $('#singleimage').attr('src',$(e).attr('src'));
                loadImageParams();
            }});
        }

        function loadImageParams(){
            id_picture = $('.selimg.selected img').data('id-picture');
            id_gallery = $('input[name=id_gallery]').val();
            $.ajax({
                url     :   "index.php?option=com_droppics&view=picture&format=raw&id="+id_picture+'&id_gallery='+id_gallery,
                type    :   "POST"
            }).done(function(data){
                $('#imageparameters').html(data);
                $('#imageparameters form').unbind().on('submit',function(e){
                    //for bxslider only
                    if($("#jform_params_bxslider_image_html").length){
                        $("#jform_params_bxslider_image_html").val($("#bxsliderimagehtml").html());
                    }
                    id_picture = $('.selimg.selected img').data('id-picture');
                    id_gallery = $('input[name=id_gallery]').val();
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=picture.save&id="+id_picture+'&id_gallery='+id_gallery,
                        type    :   "POST",
                        data    :   $(this).find('[name*="jform"], input')
                    }).done(function(data){
                        result = jQuery.parseJSON(data);
                        if(result.response===true){
                            loadImageParams();;
                            $('.selimg.selected img').attr('src',Droppics.baseurl+'/galeria/'+result.datas.id_gallery+'/thumbnails/'+result.datas.file+'.'+$('.selimg.selected img').attr('src').split('.').pop());
                            $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                        }else{
                            bootbox.alert(result.response);
                        }
                    });
                    return false;
                });

                //Remove old customs
                $('#imgp_source input[value^=custom_]').parent().remove();
                //Load customs images
                customs = $('.selimg.selected img').data('customs').each(function(el){
                    cloned = $('#imgp_source .template').clone(true).insertBefore('#imgp_source .template').removeClass('template').show();
                    cloned.find('span').html(el.file.split('.')[0]+' ('+el.width+'x'+el.height+')');
                    cloned.find('input').val('custom_'+el.id);
                    if(Droppics.custom==='custom_'+el.id){
                        cloned.find('input').attr('checked','checked');
                    }
                });
                if(Droppics.custom!==false){
                    Droppics.custom=false;
                }
                if($('#imgp_source input:checked').length===0){
                    $('#imgp_source label:first input').attr('checked','checked').trigger('change');
                }

                rloading('#rightcol');
            });
        }

        function showImages(){
            $('#imageparameters').fadeOut();
            $('#imageedit').fadeOut();
            $('#imageblock').fadeOut();
            $('#insertimage').fadeOut();
            $('#editImage').fadeOut();
        }

        function initDeleteBtn(){
            $('.deleteImage').unbind('click').click(function(e){
                    e.preventDefault();
                    if($(e.target).is('#deleteImage')){
                        message = 'COM_DROPPICS_JS_ARE_YOU_SURE_ALL';
                    }else{
                        message = 'COM_DROPPICS_JS_ARE_YOU_SURE';
                    }
                    bootbox.confirm(Joomla.JText._(message, 'Are you sure')+'?',function(result){
                        if(result===true){
                            //Delete picture
                            if($(e.target).is('#deleteImage')){
                                var pictures = [];
                                $('#preview .selimg.selected img.img').each(function(index){
                                    pictures[index] = $(this).data('id-picture');
                                });
                                $.ajax({
                                    url     :   "index.php?option=com_droppics&task=files.delete",
                                    type    :   "POST",
                                    data    :   {pictures : pictures}
                                }).done(function(data){
                                    result = jQuery.parseJSON(data);
                                    $.each(result,function(index,value){
                                        $('#preview .selimg.selected img.img[data-id-picture="'+value+'"]').parents('.wimg').fadeOut(500, function() {$(this).remove();});
                                    });
                                });
                                showThemes();
                            }else{
                                //delete custom picture
                                custom = $(e.target).siblings('input').val();
                                custom = custom.replace('custom_','');
                                $.ajax({
                                    url     :   "index.php?option=com_droppics&task=files.deleteCustom",
                                    type    :   "POST",
                                    data    :   {id : custom}
                                }).done(function(data){
                                    result = jQuery.parseJSON(data);
                                    if(result.response===true){
                                        infos = $('.selimg.selected img').data('customs');
                                        newinfos = [];
                                        $(infos).each(function(i,v){
                                           if(v.id!==custom){
                                               newinfos.push(v);
                                           }
                                        });
                                        $('.selimg.selected img').data('customs',newinfos)
                                        $(e.target).parent().siblings(':first').find('input').attr('checked','checked').trigger('change');
                                        $(e.target).parent().remove();
                                    }else{
                                        bootbox.alert(result.response);
                                    }
                                });
                            }
                        }
                    });
                    return false;
                });
        }

        function initInsertBtns(){
            $('#insertgallery').unbind('click').click(function(){
               window.parent.jInsertEditorText(insertGallery(),e_name);
               window.parent.SqueezeBox.close();
            });
            $('#insertimage').unbind('click').click(function(e){
                e.preventDefault();
                if($(this).hasClass('disabled')){
                    return;
                }
                datas = '';
                style = getStyle(false);
                id_gallery = $('input[name=id_gallery]').val();
                src= Droppics.baseurl+'/galeria/'+id_gallery+'/';
                switch ($('#imgp_source input:checked').val()){
                    case 'thumbnail':
                        src += 'thumbnails/';
                        datas += 'data-droppicssource="thumbnail"';
                        src += $('.selimg.selected img').data('file');
                        break;
                    case 'original':
                        datas += 'data-droppicssource="original"';
                        src += $('.selimg.selected img').data('file');
                        break;
                    default:
                        custom = $('#imgp_source input:checked').val();
                        custom = custom.replace('custom_','');
                        infos = $('.selimg.selected img').data('customs');
                        result = $.grep(infos, function(e){
                            return e.id == custom;
                        });
                        src += 'custom/'+result[0].file;
                        datas += 'data-droppicssource="custom_'+result[0].id+'"';
                        break;
                }

                nclick = $('#imgp_click').val();

                if(nclick==='lightbox'){
                    datas+=' data-droppicslightbox="lightbox"';
                }

                id_gallery = jQuery('input[name=id_gallery]').val();

                title = $('#click_content_custom_title').val().replace('"','&quot;');

                image = '<img src="'+src+'" title="'+title+'" data-title="'+title+'" data-droppicspicture="'+$('.selimg.selected img').data('id-picture')+'" data-droppicscategory="'+id_gallery+'" style="'+style+'" '+datas+' />';

                vtarget='';
                target = $('#imgp_click_target').val();
                if(target=='_blank'){
                    vtarget='target="_blank"';
                }else if(target=='lightbox'){
                    vtarget='class="droppicslightboxsingle"';
                }

                if(nclick==='article'){
                    article = $('#click_content_article_link').val();
                    if(article!==''){
                        image = '<a href="'+article+'" '+vtarget+' >'+image+'</a>';
                    }
                }else if(nclick==='menuitem'){
                    menuitem = $('#click_content_menuitem_id').val();
                    image = '<a href="index.php?&Itemid='+menuitem+'" '+vtarget+' >'+image+'</a>';
                }else if(nclick==='custom'){
                    custom = $('#click_content_custom_id').val();
                    if(custom.substring(0,4)==='www.'){
                        custom = 'http://'+custom;
                    }
                    image = '<a href="'+custom+'" '+vtarget+' >'+image+'</a>';
                }
                if(window.parent){
                    window.parent.jInsertEditorText(image,e_name);
                    window.parent.SqueezeBox.close();
                }
            });

            //init unique image
            $('#singleimage').attr('style',getStyle(true));
            $('#imageblock input').on('change',function(){
                $('#singleimage').attr('style',getStyle(true));
            });


            function getStyle(isimage){
                style="";
                radius = $('input[name="imgp_radius"]').val();
                if(radius>0){
                    style+='border-radius: '+radius+'px;';
                    style+='-webkit-border-radius: '+radius+'px;';
                    style+='-moz-border-radius: '+radius+'px;';
                }
                border = $('input[name="imgp_border"]').val();
                if(border>0){
                    style+='border-width: '+border+'px; border-style:solid;';
                    bordercolor = $('input[name="imgp_border_color"]').val();
    //                if(bordercolor!=''){
                        style+='border-color: '+bordercolor+';';
    //                }
                }
                shadow = $('input[name="imgp_shadow"]').val();
                shadowcolor = $('input[name="imgp_shadow_color"]').val();
                if(shadowcolor!='' && shadow>0){
                    style += 'shadow-color: '+shadowcolor+';';
                    style += 'box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                    style += '-moz-box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                    style += '-webkit-box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                }
                if(typeof(isimage)===undefined || isimage===false ){
                    margin_left = $('input[name="imgp_margin_left"]').val();
                    if(margin_left>0){
                        style+='margin-left: '+margin_left+'px;';
                    }
                    margin_top = $('input[name="imgp_margin_top"]').val();
                    if(margin_top>0){
                        style+='margin-top: '+margin_top+'px;';
                    }
                    margin_right = $('input[name="imgp_margin_right"]').val();
                    if(margin_right>0){
                        style+='margin-right: '+margin_right+'px;';
                    }
                    margin_bottom = $('input[name="imgp_margin_bottom"]').val();
                    if(margin_bottom>0){
                        style+='margin-bottom: '+margin_bottom+'px;';
                    }
                    switch ($('#imagealign button.active').data('align')){
                        case 'none':
                            break;
                        case 'right':
                            style += 'float: right;';
                            break;
                        default:
                            style += 'float: left;';
                            break;
                    }
                }
                return style;
            }
        }

        function initThemeBtn(){
            $('.themesblock a').unbind('click').click(function(e){
                theme = $(this).data('theme');
                id_gallery = $('input[name=id_gallery]').val();
                $.ajax({
                    url     :   'index.php?option=com_droppics&task=gallery.setTheme&id_gallery='+id_gallery+'&theme='+theme,
                    type    :   'POST'
                }).done(function(data){
                    result = jQuery.parseJSON(data);
                    if(result.response===true){
                        updatepreview(id_gallery);
                    }else{
                        bootbox.alert(result.response);
                    }

                });
                return false;
            });
        }

        function loadParams(){
            id_gallery = $('input[name=id_gallery]').val();
            loading('#rightcol');
            $.ajax({
                url     :   "index.php?option=com_droppics&task=gallery.edit&layout=form&id="+id_gallery,
                type    :   'POST'
            }).done(function(data){
                $('#galeryparams').html(data);
                $('#droppicsparams').on('submit',function(){
                    id_gallery = $('input[name=id_gallery]').val();
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=gallery.save&id="+id_gallery,
                        type    :   "POST",
                        data    :   $('#droppicsparams [name*="jform"], #droppicsparams input')
                    }).done(function(data){
                        result = jQuery.parseJSON(data);
                        if(result.response===true){
                            updatepreview();
                            loadParams();
                            $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                        }else{
                            bootbox.alert(result.response);
                        }
                        loadParams();
                    });
                    return false;
                });
                rloading('#rightcol');
            });
        }

        function initUploadBtn(){
            if(Droppics.can.edit){
                $('#upload_button').on('click',function(){
                    $('#upload_input').trigger('click');
                    return false;
                });
            }
        }

        /**
         * Click on new gallery btn
         */
        $('#newgallery').on('click',function(){
            $.ajax({
                url     :   "index.php?option=com_droppics&task=category.addCategory",
                type    : 'POST',
                data    :   $('#galleryToken').attr('name') + '=1'
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
                   link = ''+
                         '<li class="dd-item dd3-item dd-new-item" data-id-gallery="'+result.datas.id_category+'">'+
                                '<div class="dd-handle dd3-handle"></div>'+
                                '<div class="dd-content dd3-content">';
                        if(Droppics.can.edit){
                                    link += '<a class="edit"><i class="icon-edit"></i></a>';
                        }
                        if(Droppics.can.delete){
                                    link += '<a class="trash"><i class="icon-trash"></i></a>';
                        }
                                    link += '<a href="" class="t">'+
                                        '<span class="title">'+result.datas.name + '</span>' +
                                    '</a>'+
                                '</div>';
                    $(link).appendTo('#gallerieslist');
                    initMenu();
                    $('#mygalleries #gallerieslist li[data-id-gallery='+result.datas.id_category+']').click();
                    $('#insertgallery').show();
                }else{
                    bootbox.alert(result.response);
                }
            });
	    $("#gallerieslist").animate({ scrollTop: $('#gallerieslist')[0].scrollHeight + 24}, 1000);
            return false;
        });


        /**
         * Init the dropbox
         **/
        function initDropbox(dropbox){
            if(!Droppics.can.edit){
                return;
            }
            dropbox.filedrop({
                    paramname:'pic',
                    fallback_id:'upload_input',
                    maxfiles: 30,
                    maxfilesize: 10,
                    queuefiles: 2,
                    data: {
                        id_gallery : function(){
                            return $('input[name=id_gallery]').val();
                        }
                    },
                    url: 'index.php?option=com_droppics&task=files.upload',

                    uploadFinished:function(i,file,response){
                        if(response.response===true){
                            $.data(file).addClass('done');
                            $.data(file).find('img').attr('src', response.datas.thumbnail);
                            $.data(file).find('img').attr('data-id-picture', response.datas.id_picture);
                            $.data(file).find('img').attr('data-file', response.datas.name);
                        }else{
                            bootbox.alert(response.response);
                            $.data(file).remove();
                        }
                    },

                    error: function(err, file) {
                            switch(err) {
                                    case 'BrowserNotSupported':
                                            bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_BROWSER_NOT_SUPPORT_HTML5', 'Your browser does not support HTML5 file uploads!'));
                                            break;
                                    case 'TooManyFiles':
                                            bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_TOO_ANY_FILES','Too many files')+'!');
                                            break;
                                    case 'FileTooLarge':
                                            bootbox.alert(file.name+' '+Joomla.JText._('COM_DROPPICS_JS_FILE_TOO_LARGE', 'is too large')+'!');
                                            break;
                                    default:
                                            break;
                            }
                    },

                    // Called before each upload is started
                    beforeEach: function(file){
                            if(!file.type.match(/^image\//)){
                                    bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_ONLY_IMAGE_ALLOWED','Only images are allowed')+'!');
                                    return false;
                            }
                    },

                    uploadStarted:function(i, file, len){
                            var preview = $('<div class="wimg uploadplaceholder">'+
                                                '<div class="selimg">'+
                                                    '<img class="img" />'+
                                                    '<span class="uploaded"></span>'+
                                                    '<div class="progress progress-striped active">'+
                                                        '<div class="bar"></div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>');
                            var image = $('img', preview);

                            var reader = new FileReader();

                            reader.onload = function(e){

                                    // e.target.result holds the DataURL which
                                    // can be used as a source of the image:

                                    image.attr('src',e.target.result);
                            };

                            // Reading the file as a DataURL. When finished,
                            // this will trigger the onload function above:
                            reader.readAsDataURL(file);

                            preview.appendTo('#preview .droppicsgallery');
                            // Associating a preview container
                            // with the file, using jQuery's $.data():

                            $.data(file,preview);
                    },

                    progressUpdated: function(i, file, progress) {
                            $.data(file).find('.progress .bar').width(progress+'%');
                    },

                    afterAll: function(){
                        $('#preview .progress').delay(300).fadeIn(300).hide(300, function(){
                          $(this).remove();
                        });
                        $('#preview .uploaded').delay(300).fadeIn(300).hide(300, function(){
                          $(this).remove();
                        });
                        $('#preview .wimg').delay(1200).show(1200,function(){
                            $(this).removeClass('done placeholder');
                        });
                        initInsertBtns();
                        initImages();
                    },
                    rename : function(name){
                        ext = name.substr(name.lastIndexOf('.'),name.lenght);
                        name = name.substr(0, name.lastIndexOf('.'));
                        var pattern_accent = new Array("é", "è", "ê", "ë", "ç", "à", "â", "ä", "î", "ï", "ù", "ô", "ó", "ö");
                        var pattern_replace_accent = new Array("e", "e", "e", "e", "c", "a", "a", "a", "i", "i", "u", "o", "o", "o");
                        name = preg_replace (pattern_accent, pattern_replace_accent,name);

                        name = name.replace(/\s+/gi, '-');
                        name = name.replace(/[^a-zA-Z0-9\-]/gi, '');
                        return name+ext;
                    }
            });
        }

        /* Title edition */
        function initMenu(){
            /**
            * Click on delete gallery btn
            */
           $('#gallerieslist .dd-content .trash').unbind('click').on('click',function(){
               id_gallery = $(this).parents('li').data('id-gallery');
               bootbox.confirm(Joomla.JText._('COM_DROPPICS_JS_WANT_DELETE_GALLERY','Do you really want to delete "')+$(this).parent().find('.title').text()+'"?', function(result) {
                   if(result===true){
                       $.ajax({
                           url     :   "index.php?option=com_droppics&task=categories.delete&id_gallery="+id_gallery,
                           type    :   'POST',
                           data    :   $('#galleryToken').attr('name') + '=1'
                       }).done(function(data){
                           result = jQuery.parseJSON(data);
                           if(result.response===true){
                               $('#mygalleries #gallerieslist li[data-id-gallery='+id_gallery+']').remove();
                               $('#preview').contents().remove();
                               first = $('#mygalleries #gallerieslist li').first();
                               if(first.length>0){
                                   first.click();
                               }else{
                                   $('#insertgallery').hide();
                               }
                           }else{
                               bootbox.alert(result.response);
                           }
                       });
                   }
               });
               return false;
           });

            /* Set the active gallery on menu click */
            $('#gallerieslist .dd-content').unbind('click').click(function(e){
                id_gallery = $(this).parent().data('id-gallery');
                $('input[name=id_gallery]').val(id_gallery);
                updatepreview(id_gallery);
                $('#gallerieslist li').removeClass('active');
                $(this).parent().addClass('active');
                return false;
            });

            $('#gallerieslist .dd-content a.edit').unbind().click(function(e){
                e.stopPropagation();
                $this = this;
                link = $(this).parent().find('a span.title');
                oldTitle = link.text();
                $(link).attr('contentEditable',true);
                $(link).addClass('editable');
                $(link).selectText();

                $('#gallerieslist a span.editable').bind('click.mm',hstop);  //let's click on the editable object
                $(link).bind('keypress.mm',hpress); //let's press enter to validate new title'
                $('*').not($(link)).bind('click.mm',houtside);

                function unbindall(){
                    $('#gallerieslist a span').unbind('click.mm',hstop);  //let's click on the editable object
                    $(link).unbind('keypress.mm',hpress); //let's press enter to validate new title'
                    $('*').not($(link)).unbind('click.mm',houtside);
                }

                //Validation
                function hstop(event){
                    event.stopPropagation();
                    return false;
                }

                //Press enter
                function hpress(e){
                    if ( e.which == 13 ) {
                        e.preventDefault();
                        unbindall();
                        updateTitle($(link).text());
                        $(link).removeAttr('contentEditable');
                        $(link).removeClass('editable');
                    }
                }

                //click outside
                function houtside(e){
                    unbindall();
                    updateTitle($(link).text());
                    $(link).removeAttr('contentEditable');
                    $(link).removeClass('editable');
                }


                function updateTitle(title){
                    id_gallery = $(link).parents('li').data('id-gallery');
                    if(title!==''){
                        $.ajax({
                            url     :   "index.php?option=com_droppics&task=category.setTitle&id_gallery="+id_gallery+'&title='+title,
                            type    :   "POST"
                        }).done(function(data){
                            result = jQuery.parseJSON(data);
                            if(result===true){
                                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                                return true;
                            }
                            $(link).text(oldTitle);
                            return false;
                        });
                    }else{
                        $(link).text(oldTitle);
                        return false;
                    }

                }
            });
        }

        function loading(e){
            $(e).addClass('dploadingcontainer');
            $(e).append('<div class="dploading"></div>');
        }
        function rloading(e){
            $(e).removeClass('dploadingcontainer');
            $(e).find('div.dploading').remove();
        }

        /** Initialise single image insertion **/
        $('#imgp_click').on('change',function(){
            $('.click_content_block').hide();
            $('#imgp_click_target_wrap').hide();
            switch($(this).val()){
                case 'article':
                    $('#click_content_article').show();
                    $('#imgp_click_target_wrap').show();
                    break;
                case 'menuitem':
                    $('#click_content_menuitem').show();
                    $('#imgp_click_target_wrap').show();
                    break;
                case 'custom':
                    $('#click_content_custom').show();
                    $('#imgp_click_target_wrap').show();
                    break;
            }

        });

        /** Check new version **/
        $.getJSON( "index.php?option=com_droppics&task=update.check", function(data) {
            if(data!==false){
                $('#updateGroup').show().find('span.versionNumber').html(data);
            }
        });
        $('#hideUpdateBtn').click(function(e){
            e.preventDefault();
            var today = new Date(), expires = new Date();
            expires.setTime(today.getTime() + (7*24*60*60*1000));
            document.cookie = "com_droppics_noCheckUpdates =true; expires=" + expires.toGMTString();
            $('#updateGroup').hide();
        });

});

/**
* Insert the current gallery into a content editor
*/
function insertGallery(){
    id_gallery = jQuery('input[name=id_gallery]').val();
    dir = decodeURIComponent(getUrlVar('path'));
    code = '<img src="'+dir+'/components/com_droppics/assets/images/t.gif"'+
                'data-droppicsgallery="'+id_gallery+'"'+
                'data-droppicsversion="'+Droppics.version+'"'+
                'style="background: url('+dir+'/components/com_droppics/assets/images/gallery.png) no-repeat scroll center center #D6D6D6;'+
                'border: 2px dashed #888888;'+
                'height: 200px;'+
                'border-radius: 10px;'+
                'width: 99%;" data-gallery="'+id_gallery+'" />';
    return code;
}

//From http://jquery-howto.blogspot.fr/2009/09/get-url-parameters-values-with-jquery.html
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function getUrlVar(v){
    if(typeof(getUrlVars()[v])!==undefined){
        return getUrlVars()[v];
    }
    return null;
}

function preg_replace (array_pattern, array_pattern_replace, my_string) {var new_string = String (my_string);for (i=0; i<array_pattern.length; i++) {var reg_exp= RegExp(array_pattern[i], "gi");var val_to_replace = array_pattern_replace[i];new_string = new_string.replace (reg_exp, val_to_replace);}return new_string;}

//https://gist.github.com/ncr/399624
jQuery.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
  return this.each(function(){
    var clicks = 0, self = this;
    jQuery(this).click(function(event){
      clicks++;
      if (clicks == 1) {
        setTimeout(function(){
          if(clicks == 1) {
            single_click_callback.call(self, event);
          } else {
            double_click_callback.call(self, event);
          }
          clicks = 0;
        }, timeout || 300);
      }
    });
  });
}

//From http://stackoverflow.com/questions/1740700/how-to-get-hex-color-value-rather-than-rgb-value
function rgb2hex(rgb) {
    if(typeof(rgb)==='undefined' || rgb===null || rgb==='' || rgb.substring(0,1)==='#'){
        return '#CCCCCC';
    }
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

//From http://stackoverflow.com/questions/19663427/jquery-set-css-background-opacity
function hex2rgba(hex, opacity){
    //extract the two hexadecimal digits for each color
    var patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
    var matches = patt.exec(hex);

    //convert them to decimal
    var r = parseInt(matches[1], 16);
    var g = parseInt(matches[2], 16);
    var b = parseInt(matches[3], 16);

    //create rgba string
    var rgba = "rgba(" + r + "," + g + "," + b + "," + opacity + ")";

    //return rgba colour
    return rgba;
}
