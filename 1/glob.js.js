$(document).ready(function () { // вся мaгия пoслe зaгрузки стрaницы

// alert('werwer');

    /**
     * обновляем цифру товаров в корзине в ярлыке корзины
     * @param {type} kolvo
     * @param {type} items
     * @returns {undefined}
     */
    function refreshCart(kolvo = 0, items = [], skip_load = true) {

//        console.log('function refreshCart', kolvo, items);

        // console.log('function refreshCart', kolvo);

        if (skip_load == false) {
            // console.log(123);
            $.ajax({
                url: "/vendor/didrive_mod/shop/1/ajax.php",
                data: "action=get_kolvo_items_on_cart",
                cache: false ,
                dataType: "json" ,
                type: "post" ,
                success: function ($j) {
                    // refreshCart($j['kolvo'], $j['to_cart']);
                    $('#cart .number-shopping-cart').html($j.kolvo);

                }
            });
        } 
        //
        else {
            $('#cart .number-shopping-cart').html(kolvo);
        }

    }

    refreshCart(0, [], false);

    /**
     * считаем все суммы в таблице корзины
     * @returns {undefined}
     */
    function refreshCartTable() {

        // console.log('function refreshCartTable');
        // $('#cart .number-shopping-cart').html(kolvo);
        // alert('refresh');

        var summa = 0;
        $('tr.cart_item').each(function (i, elem) {

// console.log('111', elem);

            var item_id = $(this).attr('item_id');
            //console.log('item id', item_id);
            var kolvo = $('.product-quantity input#quantity' + item_id).val();
            // var kolvo = $('#quantity' + item_id).val();
            //console.log('kolvo', kolvo);
            var price = $('#price' + item_id).attr('price');
            //console.log('price', price);
            var sum1 = kolvo * price;
            //console.log('sum1', sum1);
            if (price > 0) {
                $('#summa' + item_id).html(number_format(sum1, 2, ',', '`'));
            }

            summa = summa + sum1;
            $('#summa_id_show_' + item_id).html(number_format(sum1, 0, '.', '`'));
            $('#summa_id_' + item_id).val(sum1);
//            if ($(this).hasClass("stop")) {
//                alert("Остановлено на " + i + "-м пункте списка.");
//                return false;
//            } else {
//                alert(i + ': ' + $(elem).text());
//            }

        });
        //console.log('summa', summa);
        $('#summa_all_show').html(number_format(summa, 2, '.', '`') + ' ₽');
    }













    /**
     * удаление позиции в корзине
     * @param {type} $item
     * @returns {undefined}
     */
    var deleteItemFromCart = function (item_id, s) {

        console.log("deleteItemFromCart", item_id, s);
        $.ajax({

            url: "/vendor/didrive_mod/shop/1/ajax.php",
            data: "action=remove_from_cart&id=" + item_id + "&s=" + s,
            cache: false,
            dataType: "json",
            type: "post",
            beforeSend: function () {
                /*
                 if (typeof $div_hide !== 'undefined') {
                 $('#' + $div_hide).hide();
                 }
                 */
//                $("#ok_but_stat").html('<img src="/img/load.gif" alt="" border=0 />');
//                $("#ok_but_stat").show('slow');
//                $("#ok_but").hide();
            }
            ,
            success: function ($j) {
                refreshCart();
                refreshCartTable();
                return true;
                /*
                 // alert($j.html);
                 if (typeof $div_show !== 'undefined') {
                 $('#' + $div_show).show();
                 }
                 */
//                $('#form_ok').hide();
//                $('#form_ok').html($j.html + '<br/><A href="">Сделать ещё заявку</a>');
//                $('#form_ok').show('slow');
//                $('#form_new').hide();
//
//                $('.list_mag').hide();
//                $('.list_mag_ok').show('slow');

            }

        });
        return false;
    }


    var shop__change_kolvo = function (action, item_id, s) {

        console.log("shop__change_kolvo", action, item_id, s);
        $.ajax({

            url: "/vendor/didrive_mod/shop/1/ajax.php",
            data: "action=" + action + "&id=" + item_id + "&s=" + s,
            cache: false,
            dataType: "json",
            type: "post",
            beforeSend: function () {
                /*
                 if (typeof $div_hide !== 'undefined') {
                 $('#' + $div_hide).hide();
                 }
                 */
//                $("#ok_but_stat").html('<img src="/img/load.gif" alt="" border=0 />');
//                $("#ok_but_stat").show('slow');
//                $("#ok_but").hide();
            }
            ,
            success: function ($j) {

                $('#quantity' + item_id).val($j['new_kolvo']);
                refreshCartTable();
                // refreshCart();
                return true;
                /*
                 // alert($j.html);
                 if (typeof $div_show !== 'undefined') {
                 $('#' + $div_show).show();
                 }
                 */
//                $('#form_ok').hide();
//                $('#form_ok').html($j.html + '<br/><A href="">Сделать ещё заявку</a>');
//                $('#form_ok').show('slow');
//                $('#form_new').hide();
//
//                $('.list_mag').hide();
//                $('.list_mag_ok').show('slow');

            }

        });
        return false;
    }











// добавляем товар в корзину, убираем от туда
    $(document).on('click', '.shop__add_to_cart', function (event) {

        var th = $(this);
        event.preventDefault();
        var hide_this_on_click = '';
        var show_id_defore_click = '';
        var uri_query = '';
        $.each(this.attributes, function () {

            if (this.specified) {

                // пропускаем атрибуты
                if (this.name == 'style'
                        || this.name == 'class'
                        || this.name == 'href'
                        || this.name == 'onclick'
                        ) {

                }
                // обрабатываем атрибуты
                else if (this.name == 'hide_this_on_click' && this.value == 'da') {
                    hide_this_on_click = this.value;
                }
                // обрабатываем атрибуты
                else if (this.name == 'show_id_defore_click') {
                    show_id_defore_click = '#' + this.value;
                }
                // обрабатываем атрибуты
                else {

                    uri_query = uri_query + '&' + this.name + '=' + this.value;
                }
            }

        });
        $.ajax({
            url: "/vendor/didrive_mod/shop/1/ajax.php",
            data: "t=1" + uri_query,
            cache: false,
            dataType: "json",
            type: "post"
            ,
            success: function ($j) {

                refreshCart($j['kolvo'], $j['to_cart']);
                if (hide_this_on_click != '') {
                    th.hide('slow');
                }

                if (show_id_defore_click != '') {
                    $(show_id_defore_click).show('slow');
                }

            }

        });
    });


// 
    $(document).on('click', '.deleteItemFromCart', function (event) {

        event.preventDefault();
        if (!confirm('Удалить позицию ?'))
            return false;
        console.log("$(document).on('click', '.deleteItemFromCart', function (event) {");
        var th = $(this);
        var item_id = $(this).attr('item_id');
        var s = $(this).attr('s');
        deleteItemFromCart(item_id, s);
        $('#item_tr_' + item_id).remove();
        // refreshCart($('#shop_id').val());
        return false;
    });
    $(document).on('click', '.shop__change_kolvo', function (event) {

        event.preventDefault();
//        if (!confirm('Удалить позицию ?'))
//            return false;

        // console.log( "$(document).on('click', '.deleteItemFromCart', function (event) {" );

//        var uri_query = '';
//        $.each(this.attributes, function () {
//            if (this.specified) {
//                if (this.name == 'class' || this.name == 'style') {
//                } else {
//                    uri_query = uri_query + '&' + this.name + '=' + this.value;
//                }
//            }
//        });

        var th = $(this);
        var action = $(this).attr('action');
        var item_id = $(this).attr('item_id');
        var s = $(this).attr('s');
        shop__change_kolvo(action, item_id, s);
        // $('#item_tr_' + item_id).remove();
        // refreshCart($('#shop_id').val());

        return false;
    });
});