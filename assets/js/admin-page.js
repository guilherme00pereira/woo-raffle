(function ($) {
    $(document).ready(function () {
        $('#selectProductforFilter').SumoSelect({
            placeholder:'Selecione',
            selectAll:true,
            locale: ['OK', 'Cancelar', 'Todos'],
            captionFormatAllSelected: 'Todos {0} selecionados!'
        });
    });

    $('#raffle-tabs a').click(function(event){
        event.preventDefault();
        $('#raffle-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tabs-content>div').hide();
        $('#' + $(this).data('tab') ).show();
    });
    $('#searchRaffle').click(function (e) {
        const loading = $('#loading');
        loading.addClass('is-active');
        const params = {
            action: ajaxobj.action_ajaxGetRaffleData,
            nonce: ajaxobj.nonce,
            pid: $('#selectProductforFilter').val(),
            cota1: $('#quotaNumber1').val(),
            cota2: $('#quotaNumber2').val(),
            cota3: $('#quotaNumber3').val()
        };
        $.get(ajaxobj.ajax_url, params, function (res) {
            console.log(res.data)
            if(res.data.raffled) {
                $('#raffle-data tbody').html(res.data.customerData);
            } else {
                $('#raffle-data tbody').html(
                    `<td colspan="5"><div class="notice notice-error">erro</div></td>`
                );
            }
            loading.removeClass('is-active');
        });
    });
    $('#upload_image_button').on('click', function( event ){
        event.preventDefault();
        var file_frame = null
        const wp_media_post_id = wp.media.model.settings.post.id;
        const set_to_post_id = ajaxobj.logo_export_attachment_post_id
        if ( file_frame ) {
            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            file_frame.open();
            return;
        } else {
            wp.media.model.settings.post.id = set_to_post_id;
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false 
        });
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();
            $( '#image-preview' ).attr( 'src', attachment.url );
            $( '#image_attachment_id' ).val( attachment.id );
            wp.media.model.settings.post.id = wp_media_post_id;
        });
        file_frame.open();
    });
    $('#submit_logo_exporter').on('click', function (e) {
        e.preventDefault();
        const params = {
            action: ajaxobj.action_ajaxSaveThumbLogo,
            nonce: ajaxobj.nonce,
            attachment_id: $('#image_attachment_id').val()
        };
        $.post(ajaxobj.ajax_url, params, function (res) {
            $('#logo-return').html(res.data);
        });
    })
    $('#remove_logo_exporter').on('click', function (e) {
        e.preventDefault();
        const params = {
            action: ajaxobj.action_ajaxSaveThumbLogo,
            nonce: ajaxobj.nonce,
        };
        $.post(ajaxobj.ajax_url, params, function (res) {
            $('#logo-return').html(res.data);
        });
    })
    // $('#exportRaffleExcel').on('click', function (e) {
    //     e.preventDefault();
    //     const params = {
    //         action: ajaxobj.action_ajaxExportRaffleExcel,
    //         nonce: ajaxobj.nonce,
    //         pid: $('#selectProductforFilter').val(),
    //         cota: $('#quotaNumber').val()
    //     };
    //     $.post(ajaxobj.ajax_url, params, function (res) {
    //         console.log(res.data)
    //         $('#raffle-data').html(res.data);
    //     });
    // });
    $('#exportRaffleExcel').on('click', function (e) {
        e.preventDefault();
        const product_id = $('#selectProductforExport').val();
        window.open(`/wp-admin/admin.php?page=woo-raffles-export&post=${product_id}`, '_blank');
        return false;
    });
    $('#exportRafflePdf').on('click', function (e) {
        e.preventDefault();
        const product_id = $('#selectProductforExport').val();
        window.open(`/wp-admin/admin.php?page=woo-raffles-export&post=${product_id}&file_type=pdf`, '_blank');
        return false;
    });
}(jQuery))