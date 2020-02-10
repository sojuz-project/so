jQuery(document).ready(function() {
    //selectpicker global jquery
    // jQuery('select').selectpicker();
    //tooltip
    // tippy('[data-toggle="tooltip"]');
    //Datepicker Jquery
    // jQuery('#from-date').datepicker({
    //     dateFormat: 'yy-mm-dd',
    // }).on('change', function(){
    //     $('.datepicker').hide();
    // });
    // jQuery('#to-date').datepicker({
    //     dateFormat: 'yy-mm-dd',
    // }).on('change', function(){
    //     $('.datepicker').hide();
    // });
    // jQuery('input[data-type="date"]')
    //     .datepicker({
    //         dateFormat: 'dd/mm/yyyy'
    //     })
    //     .on('change', function() {
    //         $('.datepicker').hide();
    //     });

    //Icheck Jquery
    // jQuery('input[type=radio]:not(".noicheck"), input[type=checkbox]:not(".noicheck,.ios-switch")').iCheck({
    //     checkboxClass: 'icheckbox_square-green',
    //     radioClass: 'iradio_square-green',
    //     increaseArea: '20%' // optional
    // });

    // Setting Page Slide Menu jQuery
    jQuery('.setting-tab-list').click(function() {
        jQuery(this)
            .siblings()
            .removeClass('active');
        jQuery(this).addClass('active');
        var data = jQuery(this).data('setting');
        jQuery('.' + data)
            .siblings()
            .removeClass('active');
        jQuery('.' + data).addClass('active');
    });
    jQuery('.custom-fields-tab-list').click(function() {
        jQuery(this)
            .siblings()
            .removeClass('active');
        jQuery(this).addClass('active');
        var data = jQuery(this).data('tab');
        jQuery('.' + data)
            .siblings()
            .removeClass('active');
        jQuery('.' + data).addClass('active');
    });
    jQuery('.browse-btn').click(function() {
        alert('hai');
        jQuery('.drop_file').trigger('click');
    });

    // jQuery('.advanced-filter input[type="checkbox"]').on('change', function(){
    //     alert('');
    // });

    jQuery('.advanced-filter input[type="checkbox"]').on(
        'ifChecked',
        function() {
            jQuery(this)
                .parent()
                .parent()
                .siblings('.row')
                .slideDown();
        }
    );
    jQuery('.advanced-filter input[type="checkbox"]').on(
        'ifUnchecked',
        function() {
            jQuery(this)
                .parent()
                .parent()
                .siblings('.row')
                .slideUp();
        }
    );
    jQuery('.split-record').on('ifChecked', function() {
        jQuery(this)
            .parent()
            .parent()
            .siblings('input')
            .show();
    });
    jQuery('.split-record').on('ifUnchecked', function() {
        jQuery(this)
            .parent()
            .parent()
            .siblings('input')
            .hide();
    });

    jQuery('.custom-size input[type="checkbox"]')
        .on('ifChecked', function() {
            jQuery('.custom-image-sizes').slideDown();
        })
        .on('ifUnchecked', function() {
            jQuery('.custom-image-sizes').slideUp();
        });

    jQuery('.btn-add-size').on('click', function() {
        var clone_row = jQuery(
            'table.media-handle-image-size tbody tr#original-row'
        ).clone();
        jQuery(clone_row).removeAttr('id');
        jQuery(clone_row)
            .children()
            .children('.form-control')
            .removeAttr('value');
        jQuery(clone_row).appendTo('table.media-handle-image-size tbody');

        jQuery('table.media-handle-image-size tbody tr td.delete').on(
            'click',
            function() {
                var row_length = jQuery(
                    'table.media-handle-image-size tbody tr'
                ).length;
                if (row_length > 1) {
                    jQuery(this)
                        .parent()
                        .remove();
                } else {
                    return;
                }
            }
        );
    });

    jQuery('#media-handle').on('change', function() {
        if (jQuery(this).is(':checked')) {
            jQuery('.media-fields').addClass('active');
        } else {
            jQuery('.media-fields').removeClass('active');
        }
    });

    jQuery('.table-mapping .action-icon').on('click', function() {
        jQuery('.manipulation-screen').removeClass('active');
        jQuery(this)
            .children('.manipulation-screen')
            .addClass('active');
        // jQuery(this).children('.manipulation-screen').show();
    });

    jQuery('.manipulation-screen .close').on('click', function() {
        // console.log('clicked');
        jQuery(this)
            .parent()
            .removeClass('active');
        // console.log('here');
    });

    // open calender when click icon
    jQuery('.input-icon').on('click', function() {
        jQuery(this)
            .siblings('.form-control')
            .focus();
    });

    dragableDroppable();
});

// mapping accordon jQuery
function toggle_func(id) {
    jQuery('#' + id + '-body').slideToggle('slow');
    //jQuery('#icon'+id).toggleClass("icon-circle-down").toggleClass("icon-circle-up");
    jQuery('#' + id).toggleClass('bg-white active');
    jQuery('#' + id + ' span').toggleClass('active');
}

// Dragable JS  (Advance Mapping Page)

var dragableDroppable = function() {
    jQuery('.draggable').draggable({
        //revert: true,
        helper: 'clone',
        containment: 'document',
        helper: function() {
            return jQuery(this)
                .clone()
                .appendTo('body')
                .css({
                    zIndex: 5
                });
        },
        start: function(event, ui) {
            jQuery(this).fadeTo('fast', 0.5);
        },
        stop: function(event, ui) {
            jQuery(this).fadeTo(0, 1);
        }
    });
    jQuery('.droppable').droppable({
        hoverClass: 'active',
        drop: function(event, ui) {
            this.value += '{' + jQuery(ui.draggable).text() + '}';
        }
    });
};
