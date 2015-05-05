$(function() {
    /* create exists function */
    jQuery.fn.exists = function(){return this.length>0;};

    $('#side-menu').metisMenu();

    //Loads the correct sidebar on window load,
    //collapses the sidebar on window resize.
    // Sets the min-height of #page-wrapper to window size
    $(window).bind("load resize", function() {
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    $('.navbar-default a').click(function(){
        if (!$(this).hasClass('open-close') && $('.navbar-toggle').is(':visible') && $('.navbar-collapse').hasClass('in')) $('.navbar-toggle').trigger('click');
    });

    bkLib.onDomLoaded(function() { nicEditors.allTextAreas({buttonList : ['bold','italic','underline','strikeThrough','removeformat','subscript','superscript','left','center','right','justified','ol','ul','subscript','superscript','indent','outdent','forecolor','bgcolor','link','unlink','fontFormat','fontFamily','fontSize','xhtml']}); });

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
    $('body').on('keydown', 'div.nicEdit-main', function(){
        isDirty = true;
    });

    $('a, button').click(function(){
        if (isDirty && !$(this).hasClass('dirtyOK')) {
            return (confirm("You will lose unsaved changes. Continue?"));
        }
        return true;
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