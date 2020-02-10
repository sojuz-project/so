jQuery( document ).ready(function( $ ){

    var dblclickbuf = {
        'selected':false,
        'value':''
    };
     // [xml representation dynamic]
    function insertxpath(){
        if ($(this).hasClass('wpallimport-placeholder')){ 
            $(this).val('');
            $(this).removeClass('wpallimport-placeholder');
        }
        if (dblclickbuf.selected)
        {
            $(this).val($(this).val() + dblclickbuf.value);
            $('.xml-element[title*="/'+dblclickbuf.value.replace('{','').replace('}','')+'"]').removeClass('selected');
            dblclickbuf.value = '';
            dblclickbuf.selected = false;                   
        }
    }

    $(window).scroll(function(){ 
      if ($(this).scrollTop() >135) {
          $('#wpsi-nodes-preview-sticky').css('top','50px');
      } else {
          $('#wpsi-nodes-preview-sticky').css('top','unset');
      }
    });

    $.fn.xml = function (opt) {
        if ( ! this.length) return this;
        var $self = this;
        var opt = opt || {};
        var action = {};
        if ('object' == typeof opt) {
            action = opt;
        } else {
            action[opt] = true;
        }

        action = $.extend({init: ! this.data('initialized')}, action);
        
        if (action.init) {
          /*  this.data('initialized', true);
            // add expander
            this.find('.xml-expander').live('click', function () {
                var method;
                if ('-' == $(this).text()) {
                    $(this).text('+');
                    method = 'addClass';
                } else {
                    $(this).text('-');
                    method = 'removeClass';
                }
                // for nested representation based on div
                $(this).parent().find('> .xml-content')[method]('collapsed');
                // for nested representation based on tr
                var $tr = $(this).parent().parent().filter('tr.xml-element').next()[method]('collapsed');
            });*/
        }
        if (action.dragable) { // drag & drop
            var _w; var _dbl = 0;
            var $drag = $('__drag'); $drag.length || ($drag = $('<input type="text" id="__drag" readonly="readonly" />'));

            $drag.css({
                position: 'absolute',
                background: 'transparent',
                top: -50,
                left: 0,
                margin: 0,
                border: 'none',
                lineHeight: 1,
                opacity: 0,
                cursor: 'pointer',
                borderRadius: 0,
                zIndex:99
            }).appendTo(document.body).mousedown(function (e) {
                if (_dbl) return;
                var _x = e.pageX - $drag.offset().left;
                var _y = e.pageY - $drag.offset().top;
                if (_x < 4 || _y < 4 || $drag.width() - _x < 0 || $drag.height() - _y < 0) {
                    return;
                }
                $drag.width($(document.body).width() - $drag.offset().left - 5).css('opacity', 1);
                $drag.select();
                _dbl = true; setTimeout(function () {_dbl = false;}, 400);
            }).mouseup(function () {
                $drag.css('opacity', 0).css('width', _w);
                $drag.blur();
            }).dblclick(function(){
                if (dblclickbuf.selected)
                {
                    $('.xml-element[title*="/'+dblclickbuf.value.replace('{','').replace('}','')+'"]').removeClass('selected');

                    if ($(this).val() == dblclickbuf.value)
                    {
                        dblclickbuf.value = '';
                        dblclickbuf.selected = false;
                    }
                    else
                    {
                        dblclickbuf.selected = true;
                        dblclickbuf.value = $(this).val();
                        $('.xml-element[title*="/'+$(this).val().replace('{','').replace('}','')+'"]').addClass('selected');
                    }
                }
                else
                {
                    dblclickbuf.selected = true;
                    dblclickbuf.value = $(this).val();
                    $('.xml-element[title*="/'+$(this).val().replace('{','').replace('}','')+'"]').addClass('selected');
                }
            });
            
            $('#title, #content, .widefat, input[name^=custom_name], textarea[name^=custom_value], input[name^=featured_image], input[name^=unique_key]').bind('focus', insertxpath );
            
            $(document).mousemove(function () {
                if (parseInt($drag.css('opacity')) != 0) {
                    setTimeout(function () {
                        $drag.css('opacity', 0);
                    }, 50);
                    setTimeout(function () {
                        $drag.css('width', _w);
                    }, 500);
                }
            });

            this.find('.xml-tag.opening > .xml-tag-name, .xml-attr-name, .csv-tag.opening > .csv-tag-name, .ui-menu-item').each(function () {
                var $this = $(this);
                var xpath = '.';
                if ($this.is('.xml-attr-name'))
                    xpath = '{' + ($this.parents('.xml-element:first').attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '/@' + $this.html().trim() + '}';
                else if($this.is('.ui-menu-item'))
                    xpath = '{' + ($this.attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '}';
                else
                    xpath = '{' + ($this.parent().parent().attr('title').replace(/^\/[^\/]+\/?/, '') || '.') + '}';
                
                $this.mouseover(function (e) {
                    $drag.val(xpath).offset({left: $this.offset().left - 2, top: $this.offset().top - 2}).width(_w = $this.width()).height($this.height() + 4);
                });
            }).eq(0).mouseover();
        }
        return this;
    };
    // tag preview
    $.fn.tag = function () {
        this.each(function () {
            var $tag = $(this);
            
            $tag.xml('dragable');
            
        });         
        return this;
    };
    /*$('#wpsi-nodes-preview-sticky').tag();*/
    //collapsed xml-tag
    $('.xml-expander').live('click', function() {
        var method;
        if ('-' == $(this).text()) {
            $(this).text('+');
            method = 'addClass';
        } else {
            $(this).text('-');
            method = 'removeClass';
        }
        // for nested representation based on div
        $(this).parent().find('> .xml-content')[method]('collapsed');
        // for nested representation based on tr
        var $tr = $(this).parent().parent().filter('tr.xml-element').next()[method]('collapsed');
    });

    $(".toggle-show").live('click', function(){
        if( $('.wpsi-nodes-preview-sticky').is(":visible") ){
            $('.wpsi-template-container').css('width','97%');
        } else {
            $('.wpsi-template-container').css('width','66%');
        }
        $('.wpsi-nodes-preview-sticky').toggle( 'slide');
        $("#toggle-show").toggle();
    });

    //Responsive Tooltip 
    var targets = $( '[rel~=tooltip]' ),
        target  = false,
        tooltip = false,
        title   = false;
 
    targets.bind( 'mouseenter', function()
    {
        target  = $( this );
        tip     = target.attr( 'title' );
        tooltip = $( '<div id="tooltip"></div>' );

        if( !tip || tip == '' )
            return false;
 
        target.removeAttr( 'title' );
        tooltip.css( 'opacity', 0 )
               .html( tip )
               .appendTo( 'body' );
 
        var init_tooltip = function()
        {
            if( $( window ).width() < tooltip.outerWidth() * 1.5 )
                tooltip.css( 'max-width', $( window ).width() / 2 );
            else
                tooltip.css( 'max-width', 340 );
 
            var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                pos_top  = target.offset().top - tooltip.outerHeight() - 20;
 
            if( pos_left < 0 )
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass( 'left' );
            }
            else
                tooltip.removeClass( 'left' );
 
            if( pos_left + tooltip.outerWidth() > $( window ).width() )
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass( 'right' );
            }
            else
                tooltip.removeClass( 'right' );
 
            if( pos_top < 0 )
            {
                var pos_top  = target.offset().top + target.outerHeight();
                tooltip.addClass( 'top' );
            }
            else
                tooltip.removeClass( 'top' );
 
            tooltip.css( { left: pos_left, top: pos_top } )
                   .animate( { top: '+=10', opacity: 1 }, 50 );
        };
 
        init_tooltip();
        $( window ).resize( init_tooltip );
 
        var remove_tooltip = function()
        {
            tooltip.animate( { top: '-=10', opacity: 0 }, 50, function()
            {
                $( this ).remove();
            });
 
            target.attr( 'title', tip );
        };
 
        target.bind( 'mouseleave', remove_tooltip );
        tooltip.bind( 'click', remove_tooltip );
    });

    // wpsi-Accordian
    $('#wpsi-accordion li.title').first().addClass('active-tab');
    $('#wpsi-accordion li.content').filter(':nth-child(n+4)').addClass('hide');
    $('ul#wpsi-accordion').on('click','li.title',function() {
        $('ul#wpsi-accordion').find('input[type=radio]').removeAttr('checked');
        $('#wpsi-accordion li').removeClass('active-tab');
        $(this).addClass('active-tab');
        $(this).next().slideDown(200).siblings('li.content').slideUp(200);
        $('.active-tab').next('.content').find('input[type=radio]').attr("checked", "checked");
    });
    //----- Model Model
     $('[data-popup-open]').on('click', function(e)  {
        var targeted_popup_class = jQuery(this).attr('data-popup-open');
        $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
        e.preventDefault();
    });
    //----- CLOSE Model
    $('[data-popup-close]').on('click', function(e)  {
        var targeted_popup_class = jQuery(this).attr('data-popup-close');
        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
        e.preventDefault();
    });
    arr = [];

    /*SHOW HIDE BLOCK*/
    $('.show_hide_radio').each(function(index, elem){
        if($(this).is(":checked")){
            $(this).parent('label').next(".slidingDiv").slideDown();
        } else {
            $(this).parent('label').next(".slidingDiv").slideUp()
        }
    });
    $('.show_hide_radio').change(function( e ){
        $('.wpsi-date-container').find(".slidingDiv").slideUp();
        if($(this).is(":checked")){
            $(this).parent('label').next(".slidingDiv").slideDown();
        }
    });
    $('.show_hide').each(function(index, elem){
        if($(this).is(":checked")){
            $(this).parent('label').next(".slidingDiv").slideDown();
        } else {
            $(this).parent('label').next(".slidingDiv").slideUp()
        }
    });
    $('.show_hide').click(function( e ){
        if ($(this).is(":checked")) {
            $(this).parent('label').next(".slidingDiv").slideDown();
        } else {
            $(this).parent('label').next(".slidingDiv").slideUp();
        }
    });
    $('.post_tax').click(function(e){
        var input = $(this).closest('.inner-content').find('.wpsi-cat-list');
        if(input.val() != ''){
            input.val(input.val() + ',' + $(this).text());
        } else{
            input.val($(this).text());
        }
    });
    $('.select_all').click(function(e){
        var arr = [];
        $(this).parent().find("a").each(function(index, elem){
            arr.push($(this).text());
        });
        arr.join(",");
        var input = $(this).closest('.inner-content').find('.wpsi-cat-list');
        if(input.val() != ''){
            input.val(input.val() + ',' + arr.join(","));
        } else{
            input.val(arr.join(","));
        }

    });
    
    $('.date-picker').datepicker({
        dateFormat: 'dd-M-yy',
        showButtonPanel: true,
        autoSize: true
    });
    // Start and End date Vaidatation
    $(".from-date").datepicker({
            dateFormat: 'dd-M-yy',
            defaultDate: new Date(),
            onSelect: function(dateStr) {         
                $(".to-date").datepicker("destroy");
                $(".to-date").val(dateStr);
                $(".to-date").datepicker({ minDate: new Date(dateStr),dateFormat: 'dd-M-yy',})
            }
    });
    $('.to-date').datepicker({
        dateFormat: 'dd-M-yy',
        defaultDate: new Date(),
    });

    // Add Fields
    var x=0;
   $("body").on("click",'.add_custom_field', function(e){
        e.preventDefault();
        var html = '';
        x++;
        html +='<tr>';
           html +='<td><input type="text" name="custom_field_name[]" class="wpsi-form-control"  ></td>';
           html +='<td><input type="text" name="custom_field_value[]" class="wpsi-form-control" ></td>';
           html +='<td> <a class="dashicons dashicons-trash remove-field"></a> </td>';
        html +='</tr>';
        $('.wpsi-custom-field-tab').append(html);
    });
   $("body").on("click",'.add_cat_group', function(e){
        e.preventDefault();
        var dname = $(this).prev('.temp-cat-name').val();
        var html = '';
        x++;

        html +='<tr>';
           html +='<td><input type="text" name="'+dname+'" class="wpsi-form-control" ></td>';
           html +='<td> <a class="dashicons dashicons-trash remove-field"></a> </td>';
        html +='</tr>';
        $('.wpsi-cat-group-tab').append(html);
    });
    $("body").on("click" ,'.remove-field', function(e) {
        e.preventDefault(); 
        var tbody = $(this).closest('tbody');
        var tr_len = tbody.find('tr').length;
        if(tr_len > 1) {
            x--;
            $(this).closest('tr').remove();
        } 
    });
    //show response message
    $("body").on("click" , '.alert_closebtn', function(e) {
        e.preventDefault(); 
        $(this).parent().fadeOut('slow');
    });

    //Checkbox checked counter
    $('.mycxk').click(function(){
        var length = $('body .mycxk').filter(':checked').length;
        if( length < 1 ){
            $('.target-disabled').addClass('disabled').attr('disabled', 'disabled');;
        } else {
             $('.target-disabled').removeClass('disabled').removeAttr("disabled");
        }
    });
    /*Button Tabs*/
    $('.button-tabPannel .wpsi-button').click(function(){
        $('.wpsi-post-container').css('display','none');
        $('#upload_msg').css('display','none');
        var tab_id = $(this).attr('data-tab');
        if(tab_id=='existing'){
            $('.willUse').hide().find('input').removeAttr('checked');
        } else {
            $('.willUse').show();
        }
        $('.button-tabPannel .wpsi-button').removeClass('active');
        $('.tab-content').removeClass('current');
        $(this).addClass('active');
        $("#"+tab_id).addClass('current');
    });
    $('#useFile').change(function(e) { // will use filename inputbox  hide/show  
        if ($(this).is(":checked")) {
           $('.willUse-data').slideDown().find( "input" ).eq( 0 ).attr('required','required');
        } else {
            $('.willUse-data').slideUp().find( "input" ).eq( 0 ).removeAttr('required');
        } 
    });
    $('.clear-radio').click(function(e) {
         $(this).parent('.clear-target').find('input[type=radio]').each(function(index, elem){
            $(this).removeAttr("checked");
        });
    });
});