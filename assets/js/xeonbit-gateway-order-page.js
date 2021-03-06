/*
 * Copyright (c) 2018, Ryo Currency Project
*/
function xeonbit_showNotification(message, type='success') {
    var toast = jQuery('<div class="' + type + '"><span>' + message + '</span></div>');
    jQuery('#xeonbit_toast').append(toast);
    toast.animate({ "right": "12px" }, "fast");
    setInterval(function() {
        toast.animate({ "right": "-400px" }, "fast", function() {
            toast.remove();
        });
    }, 2500)
}
function xeonbit_showQR(show=true) {
    jQuery('#xeonbit_qr_code_container').toggle(show);
}
function xeonbit_fetchDetails() {
    var data = {
        '_': jQuery.now(),
        'order_id': xeonbit_details.order_id
    };
    jQuery.get(xeonbit_ajax_url, data, function(response) {
        if (typeof response.error !== 'undefined') {
            console.log(response.error);
        } else {
            xeonbit_details = response;
            xeonbit_updateDetails();
        }
    });
}

function xeonbit_updateDetails() {

    var details = xeonbit_details;

    jQuery('#xeonbit_payment_messages').children().hide();
    switch(details.status) {
        case 'unpaid':
            jQuery('.xeonbit_payment_unpaid').show();
            jQuery('.xeonbit_payment_expire_time').html(details.order_expires);
            break;
        case 'partial':
            jQuery('.xeonbit_payment_partial').show();
            jQuery('.xeonbit_payment_expire_time').html(details.order_expires);
            break;
        case 'paid':
            jQuery('.xeonbit_payment_paid').show();
            jQuery('.xeonbit_confirm_time').html(details.time_to_confirm);
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'confirmed':
            jQuery('.xeonbit_payment_confirmed').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired':
            jQuery('.xeonbit_payment_expired').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired_partial':
            jQuery('.xeonbit_payment_expired_partial').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
    }

    jQuery('#xeonbit_exchange_rate').html('1 XNB = '+details.rate_formatted+' '+details.currency);
    jQuery('#xeonbit_total_amount').html(details.amount_total_formatted);
    jQuery('#xeonbit_total_paid').html(details.amount_paid_formatted);
    jQuery('#xeonbit_total_due').html(details.amount_due_formatted);

    jQuery('#xeonbit_integrated_address').html(details.integrated_address);

    if(xeonbit_show_qr) {
        var qr = jQuery('#xeonbit_qr_code').html('');
        new QRCode(qr.get(0), details.qrcode_uri);
    }

    if(details.txs.length) {
        jQuery('#xeonbit_tx_table').show();
        jQuery('#xeonbit_tx_none').hide();
        jQuery('#xeonbit_tx_table tbody').html('');
        for(var i=0; i < details.txs.length; i++) {
            var tx = details.txs[i];
            var height = tx.height == 0 ? 'N/A' : tx.height;
            var row = ''+
                '<tr>'+
                '<td style="word-break: break-all">'+
                '<a href="'+xeonbit_explorer_url+'/tx/'+tx.txid+'" target="_blank">'+tx.txid+'</a>'+
                '</td>'+
                '<td>'+height+'</td>'+
                '<td>'+tx.amount_formatted+' Xeonbit</td>'+
                '</tr>';

            jQuery('#xeonbit_tx_table tbody').append(row);
        }
    } else {
        jQuery('#xeonbit_tx_table').hide();
        jQuery('#xeonbit_tx_none').show();
    }

    // Show state change notifications
    var new_txs = details.txs;
    var old_txs = xeonbit_order_state.txs;
    if(new_txs.length != old_txs.length) {
        for(var i = 0; i < new_txs.length; i++) {
            var is_new_tx = true;
            for(var j = 0; j < old_txs.length; j++) {
                if(new_txs[i].txid == old_txs[j].txid && new_txs[i].amount == old_txs[j].amount) {
                    is_new_tx = false;
                    break;
                }
            }
            if(is_new_tx) {
                xeonbit_showNotification('Transaction received for '+new_txs[i].amount_formatted+' Xeonbit');
            }
        }
    }

    if(details.status != xeonbit_order_state.status) {
        switch(details.status) {
            case 'paid':
                xeonbit_showNotification('Your order has been paid in full');
                break;
            case 'confirmed':
                xeonbit_showNotification('Your order has been confirmed');
                break;
            case 'expired':
            case 'expired_partial':
                xeonbit_showNotification('Your order has expired', 'error');
                break;
        }
    }

    xeonbit_order_state = {
        status: xeonbit_details.status,
        txs: xeonbit_details.txs
    };

}
jQuery(document).ready(function($) {
    if (typeof xeonbit_details !== 'undefined') {
        xeonbit_order_state = {
            status: xeonbit_details.status,
            txs: xeonbit_details.txs
        };
        setInterval(xeonbit_fetchDetails, 30000);
        xeonbit_updateDetails();
        new ClipboardJS('.clipboard').on('success', function(e) {
            e.clearSelection();
            if(e.trigger.disabled) return;
            switch(e.trigger.getAttribute('data-clipboard-target')) {
                case '#xeonbit_integrated_address':
                    xeonbit_showNotification('Copied destination address!');
                    break;
                case '#xeonbit_total_due':
                    xeonbit_showNotification('Copied total amount due!');
                    break;
            }
            e.clearSelection();
        });
    }
});