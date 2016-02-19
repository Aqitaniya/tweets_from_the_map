jQuery(function(){
    jQuery("#search_tweets-search-input").change(function(){
        if(jQuery(this).val()==''){
            jQuery.ajax({
                type: "POST",
                url: location,
                data: {clear_search:'none'}
            });
        }
    });

    jQuery(".edit a").on('click',function(){
        var current_this = jQuery(this);
        var row_content = Array();
        var colls = jQuery(this).closest("tr").find('td').length;

        if(jQuery(this).html()=="Edit") {

            jQuery(this).html('Save');

            row_content[0]= jQuery(this).closest("tr").find('td:nth-child(' + 2 + ') div:nth-child(1)').text();
            jQuery(this).closest("tr").find('td:nth-child(' + 2 + ') div:nth-child(1)').empty().append('<input type="text" id="'+0+'" value="'+row_content[0]+'"/>');

            for(var i=1; i<colls; i++) {
                row_content[i] = jQuery(this).closest("tr").find('td:nth-child(' + (i+2) + ')').html();
                jQuery(this).closest("tr").find('td:nth-child(' + (i+2) + ')').empty().append('<input type="text" id="' + i + '" value=""/>');
                jQuery(this).closest("tr").find('td:nth-child(' + (i+2) + ')').find('#'+i+'').val(row_content[i]);
            }
            jQuery(this).closest("tr").find('td:nth-child(3) input').attr('disabled','disabled');

            jQuery.cookie('row_content_old', row_content.join(','));
        }
        else if(jQuery(this).html()=="Save"){

            for (var i = 0; i <colls; i++)
                row_content[i]=jQuery(this).closest("tr").find('td:nth-child(' + (i+2) + ')').find('#'+i+'').val();
            jQuery.ajax({
                type: "POST",
                url: location,
                data: {row_content:row_content},
                dataType: "text",
                success: function(data){
                    str=data.indexOf("Update table field was successful");
                    if(str!=-1){
                        current_this.closest("tr").find('td:nth-child(2) div:nth-child(1)').empty().append(row_content[0]);
                       for(var i=1; i<colls; i++) {
                           current_this.closest("tr").find('td:nth-child(' + (i+2) + ')').empty().append(row_content[i])
                       }
                        current_this.closest('td').find('.row-actions .edit a').html('Edit');
                    }
                    else{
                        row_content=jQuery.cookie('row_content_old').split(/,/);
                        current_this.closest("tr").find('td:nth-child(2) div:nth-child(1)').empty().append(row_content[0]);
                        for(var i=1; i<colls; i++)
                           current_this.closest("tr").find('td:nth-child(' + (i+2) + ')').empty().append(row_content[i])

                        current_this.closest('td').find('.row-actions .edit a').html('Edit');
                    }
                }

           });

        }
    });
    jQuery(".edit_row_fields").on('click',".close-edit-row",function(){
        jQuery(this).closest('td').find('.row-actions .edit a').html('Edit');
        jQuery(this).closest('div').empty();
    });
});

