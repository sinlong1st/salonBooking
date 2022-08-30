import 'bootstrap';
import 'jquery';
import 'jquery-ui';
require('jquery-ui/ui/widgets/datepicker');
$(document).ready(function () {

    if ($('#booking').length > 0) {

        $(document).on('click', '.booking-shop', function (e) {
            e.preventDefault();
            let url = $(this).data('url');
            selectRadio(this);

            $.get(url, function (data) {
                $('#shop-products-column').html('');
                activeSubmitButton();
                $(data.shop).each(function (index, row) {

                    let html = '';
                    html += '<div class="card shop-service" data-url="' + data.url + '" data-shopid="' + row.shop_id + '" data-serviceid="' + row.service_id + '">';
                    html += '<div class="card-body">';
                    html += '<h5 class="card-title">' + row.name + '</h5>';
                    html += '<span>Price: $' + row.price + '</span>';
                    html += '<input type="radio" value="' + row.service_id + '" name="bookingservice" />';
                    html += '</div>';
                    html += '</div>';
                    $('#shop-products-column').append(html);
                });
                $('#work-calendar-column').data('shopid', data.id_shop);
                if (data.skipdate != 'undefined') {
                    $('#datepicker').datepicker("destroy");
                    $('#datepicker').datepicker({
                        minDate: 0,
                        maxDate: "1M",
                        beforeShowDay: function (date) {
                            let currentDay = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                            if ($.inArray(currentDay, data.skipdate) == -1) {
                                return [true, ""];
                            } else {
                                return [false, "", "Unavailable"];
                            }
                        },
                        onSelect: function (selected) {
                            let refreshUrl = $('#work-calendar-column').data('refresh-url');
                            let id_shop = $('#work-calendar-column').data('shopid');
                            let id_service = $('#work-calendar-column').data('serviceid');

                            if (refreshUrl != undefined) {
                                $('#time-available-column').html('');
                                activeSubmitButton();
                                $.get(refreshUrl, {id_shop: id_shop, id_service: id_service, booking_date: selected}, function (data) {

                                    $.each(data.timeavailable, function (key, name) {
                                        let html = '';
                                        html += '<div class="card shop-available-time" data-shopid="' + data.shop_id + '" data-serviceid="' + data.service_id + '">';
                                        html += '<div class="card-body">';
                                        html += '<h5 class="card-title">' + data.timeavailable[key] + '</h5>';
                                        html += '<input type="radio" name="bookingtime" value="' + key + '" />';
                                        html += '</div>';
                                        html += '</div>';
                                        $('#time-available-column').append(html);
                                    });
                                });

                            }
                        }

                    });
                    $('#datepicker').datepicker("refresh");
                    $('#datepicker').addClass('d-none');
                }
                $('#time-available-column').html('');
            });
        });

        $(document).on('click', '.shop-available-time', function (e) {
            e.preventDefault();
            $('#time-available-column input[name="bookingtime"]').removeAttr('checked');
            selectRadio(this);
            activeSubmitButton();

        });
        $(document).on('click', '.shop-service', function (e) {
            e.preventDefault();
            //let shop_id = $(this).data('shopid');
            let service_id = $(this).data('serviceid');
            $('#datepicker').removeClass('d-none');
            $('#work-calendar-column').data('serviceid', service_id);
            let currentSelected = $(this).find('input[type="radio"]').val();
            if (currentSelected === undefined) {
                $('#time-available-column').html('');
                $('#datepicker').val('');
            }
            selectRadio(this);
            activeSubmitButton();
        });

        function selectRadio(obj) {
            $(obj).closest('.booking-column-wrapper').find('input[type="radio"]').removeAttr('checked');
            $(obj).closest('.booking-column-wrapper').find('.card').removeClass('active');
            $(obj).find('input[type="radio"]').attr('checked', 'checked');
            $(obj).addClass('active');
        }
        function activeSubmitButton() {
            let bookingshop = $('input[name="bookingshop"]:checked').val();
            let bookingservice = $('input[name="bookingservice"]:checked').val();
            let bookingtime = $('input[name="bookingtime"]:checked').val();

            $('#action-buttons').addClass('d-none');
            if (bookingshop !== undefined && bookingservice !== undefined && bookingtime !== undefined) {
                $('#action-buttons').removeClass('d-none');
            }
        }
    }
});