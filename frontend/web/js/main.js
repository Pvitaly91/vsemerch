$(window).load(function () {
    // $(".sk-fading-circle").fadeOut();
    // $('#loading-screen').delay(350).fadeOut('slow');
    // $('body').delay(350).css({'overflow': 'visible'});
});

$(document).ready(function () {

    var $top = $("#top");

    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $top.addClass('fix_top');
        } else if ($(this).scrollTop() <= 100) {
            $top.removeClass('fix_top');
        }
    });//scroll
});

/*$(document).ready(function () {

    $('.checkradios').checkradios();

    $('.add_to_cart').click(function () {
       
        if($(this).attr('disabled')) {
            return false;
        }
        var url = $(this).attr('href');
        var size_id = $(this).data('size_id');
        if(size_id) {
            url += '&size_id=' + size_id;
        }
        $.get(url, function (data) {
            reloadCart();
            $("#modalCart").modal({"show": true});
        });
        return false;
    });

    $('.basket').click(function () {
       
        $("#modalCart").modal({"show": true});
        return false;
    });

    $(document).on('pjax:success', function() {
        reloadCart();
    });

    var reloadCart = function () {
        $.get('/shop/cart/items-in-cart', function (data) {
            $('.basket').find('span').html(data);
        });
    }

    $('.size-box').click(function () {
        var price = $(this).data('price');
        var id = $(this).data('id');
        var code = $(this).data('code');
        var not_available = $(this).data('not_available');
        if(not_available == 1) {
            $('.available-text').removeClass('are_available').addClass('not_available');
            $('.available-text').text('Нет в наличии');
            $('.add_to_cart').attr('disabled', true);
        } else {
            $('.available-text').removeClass('not_available').addClass('are_available');
            $('.available-text').text('Есть в наличии');
            $('.add_to_cart').attr('disabled', false);
        }
        $('.old-price-text').remove();
        $('.size-box').removeClass('active-size');
        $(this).addClass('active-size');
        $('.price-text').removeClass('hidden');
        $('.price-text').children('span').text(price + ' ₴');
        $('.code-text').children('span').text(code);
        $('.add_to_cart').data('size_id', id);
        return false;
    });
});*/

function lookup(inputString) {
    if (inputString.length == 0) {
        $('#searchRes').fadeOut(); // Hide the suggestions box
    } else {
        $.get("/shop/catalog/search-ajax", {search_str: "" + inputString + ""}, function (data) { // Do an AJAX call
            if(!data.length) {
                return false;
            }
            $('#searchRes').fadeIn(); // Show the suggestions box
            $('#searchRes').html(data); // Fill the suggestions box

        });
    }
}

$('#btn-filters').click(function () {
    $('#leftsidebar').toggle();
    return false;
});