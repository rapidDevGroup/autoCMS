$(function() {
    /* create exists function */
    jQuery.fn.exists = function () {
        return this.length > 0;
    };

    var $container = $('.container');

    if (!$container.exists()) {
        $('#side-menu').metisMenu();
    }

    //Loads the correct sidebar on window load,
    //collapses the sidebar on window resize.
    // Sets the min-height of #page-wrapper to window size
    $(window).bind("load resize", function () {
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function () {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    $('.navbar-default a').click(function () {
        if (!$(this).hasClass('open-close') && $('.navbar-toggle').is(':visible') && $('.navbar-collapse').hasClass('in')) $('.navbar-toggle').trigger('click');
    });

    if ($container.exists()) {
        $('textarea').summernote();

        $('.note-toolbar button').addClass('dirtyOK');

        $('.desc-edit').editable({'emptytext': 'no description'});
    }

    var $uploadButtons = $('.upload-button');
    if ($uploadButtons.exists()) {
        $uploadButtons.click(function (e) {
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
    $(':input').change(function () {
        if (!$(this).hasClass('dirtyOK')) {
            isDirty = true;
            $(window).on('beforeunload', function(){
                return 'You will lose unsaved changes.';
            });
        }
    });
    $('body').on('keydown', 'div.note-editable', function () {
        isDirty = true;
        $(window).on('beforeunload', function(){
            return 'You will lose unsaved changes.';
        });
    });

    $('a, button').click(function () {
        if (isDirty && !$(this).hasClass('dirtyOK')) {
            return (confirm("You will lose unsaved changes. Continue?"));
        }
        $(window).off('beforeunload');
        return true;
    });

    $(document).on('submit', '#change-pass-form', function() {
        $.ajax({
            url: '/admin/dash/change-pass/',
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                $('#change-pass').modal('hide');
                $('#change-pass-error').hide('slow');
                $('#change-pass-form').find('input').each(function() {$(this).val('')});
            },
            error: function(xhr, err) {
                $('#change-pass-error').show('slow');
                $('#change-pass-form').find('input').each(function() {$(this).val('')});
            }
        });
        return false;
    });

    var cp = $('.color-picker');
    if (cp.exists()) {
        cp.colorpicker();
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