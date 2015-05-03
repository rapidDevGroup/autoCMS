$(function() {
    /* create exists function */
    jQuery.fn.exists = function(){return this.length>0;};

    $('#side-menu').metisMenu();

    //Loads the correct sidebar on window load,
    //collapses the sidebar on window resize.
    // Sets the min-height of #page-wrapper to window size
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    /*
    var editorConfig = {
        linkShowAdvancedTab: false,
        removePlugins: 'elementspath',
        resize_enabled: false,
        toolbar: [['Format','Font','FontSize'],['Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ],['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv', '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ],['Link','Unlink'],['Undo','Redo'],['Maximize','ShowBlocks'],['Source']]
    };

    $('textarea.editor').ckeditor(editorConfig);
    */

    $('.desc-edit').editable();

    var $uploadButtons = $('.upload-button');
    if ($uploadButtons.exists()) {
        $uploadButtons.click(function(e){
            $('#' + $(this).data('trigger')).trigger('click');
        });
    }

    var $carousel = $('.carousel');

    if ($carousel.exists()) {
        $carousel.on('slide.bs.carousel', function (e) {
            var nextH = $(e.relatedTarget).height();
            $(this).find('.active.item').parent().animate({height: nextH}, 800);
        });
    }

    var isDirty = false;
    $(':input').change(function(){
        isDirty = true;
    });
    for (var i in CKEDITOR.instances) {
        CKEDITOR.instances[i].on('change', function() {
            isDirty = true;
        });
    }

    $('a, button').click(function(){
        if (isDirty && !$(this).hasClass('dirtyOK')) {
            return (confirm("You will lose unsaved changes. Continue?"));
        }
        return true;
    });

    $('.pagination').on('click', 'li', function(){
        if (!$(this).hasClass('no-active')) {
            $(this).parent().find('li').removeClass('active');
            $(this).addClass('active');
        }
    });

    var duplicating = false;
    $('body').on('click','#repeat-duplicate',function(){
        var page = $(this).data('page');
        var key = $(this).attr('data-key');
        var $carousel = $('#carousel-repeat-' + key);
        var $carousel_active = $('#carousel-repeat-' + key + ' .item.active');
        var $carousel_paging = $('#carousel-paging-' + key + ' .active');
        var number_slides = $carousel.attr('data-number');
        var number_slides_new = $carousel.attr('data-number')/1 + 1;
        var current = $carousel_active.index('#carousel-repeat-' + key + ' .item');

        if (!duplicating) {
            duplicating = true;
            $.getJSON('/admin/page/' + page + '/repeat-dup/' + key + '/' + current + '/', function () {
                $carousel_active.clone().removeClass('active').appendTo('#carousel-repeat-' + key + ' .carousel-inner');
                $carousel.removeData().carousel({
                    interval: false
                }).carousel(current);

                $carousel_paging.clone().removeClass('active').attr('data-slide-to', number_slides).appendTo('#carousel-paging-' + key);
                $('#carousel-paging-' + key + ' [data-slide-to=' + number_slides + '] a').text(number_slides / 1 + 1);

                $('#repeat-delete').show();

                $carousel.attr('data-number', number_slides_new);
                duplicating =  false;
            });
        }
        return false;
    });

    var deleting = false;
    $('body').on('click','#repeat-delete',function(){
        var page = $(this).data('page');
        var key = $(this).attr('data-key');
        var $carousel = $('#carousel-repeat-' + key);
        var $carousel_active = $('#carousel-repeat-' + key + ' .item.active');
        var number_slides_new = $carousel.attr('data-number')/1 - 1;
        var current = $carousel_active.index('#carousel-repeat-' + key + ' .item');

        if (!deleting) {
            deleting = true;
            $.getJSON('/admin/page/' + page + '/repeat-del/' + key + '/' + current + '/', function () {
                $carousel_active.remove().appendTo('#carousel-repeat-' + key + ' .carousel-inner');
                $carousel.removeData().carousel({
                    interval: false
                }).carousel(current);

                $('#carousel-paging-' + key + ' li').removeClass('active');
                $('#carousel-paging-' + key + ' li:first-child').addClass('active');
                $('#carousel-paging-' + key + ' li:last-child').remove();

                if (number_slides_new <= 1) $('#repeat-delete').hide();

                $carousel.attr('data-number', number_slides_new);
                deleting = false;
            });
        }
        return false;
    });
});

function validateCreateAuth() {
    // TODO: validate the the password matches... other rules if needed

    return true;
}

function readURL(input, imgID) {
    if (input.files && input.files[0]) {
        $.each(input.files, function(key, element) {
            // if supported type
            var ext = input.files[key].name.split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif','png','jpg','jpeg']) != -1) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + imgID + '-image').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[key]);
            }
        });
    }
}