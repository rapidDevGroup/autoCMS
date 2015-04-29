$(function() {
    /* create exists function */
    jQuery.fn.exists = function(){return this.length>0;}


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

    bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });

    $('.desc-edit').editable();

    var $uploadBtns = $('.upload-button');
    if ($uploadBtns.exists()) {
        $uploadBtns.click(function(e){
            $('#' + $(this).data('trigger')).trigger('click');
        });
    }
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