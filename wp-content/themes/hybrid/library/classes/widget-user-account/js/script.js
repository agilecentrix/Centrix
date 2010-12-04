jQuery(document).ready(function() {
   
    //context
    var context = jQuery('div[role=user-account]');
   
    //display/hide labels
    context.find ( 'input[type=text], input[type=password]' ).live ( 'focus', function() {
        jQuery(this).parent().find('label').addClass('hidden');
    }).live ( 'blur', function() {
        if(jQuery(this).attr('value').length == 0) {
            jQuery(this).parent().find('label').removeClass('hidden');
        }
    }).live ( 'change', function() {
        if(jQuery(this).attr('value').length != 0) {
            jQuery(this).parent().find('label').addClass('hidden');
        }
    });
      
    jQuery('button[role=login]').live('click', function(event){
        event.preventDefault();
        load_page('login');
        return false;
    });

    jQuery('button[role=logout]').live('click', function(event){
        event.preventDefault();
        load_page('logout');
        return false;
    });

    jQuery('a[role=register]').live('click', function(event){
        event.preventDefault();
        load_page('register_form');
        return false;
    });

    jQuery('button[role=register]').live('click', function(event){
        event.preventDefault();
        load_page('register');
        return false;
    });

    jQuery('a[role=forgot]').live('click', function(event){
        event.preventDefault();
        load_page('forgot_form');
        return false;
    });

    jQuery('button[role=get_new_password]').live('click', function(event){
        event.preventDefault();
        load_page('forgot');
        return false;
    });

    function load_page(page)
    {
        var conf = {
            type: 'POST',
            url: document.location.href,
            success: success_callback,
            error: error_callback,
            data: 'action=user-account&callback='+page+'&'+jQuery('form[role='+page+']').serialize()
        };
        busy();
        jQuery.ajax(conf);
    }

    function success_callback(data, textStatus, request)
    {
        context.html(data);
        context.find ( 'input[type=text], input[type=password]' ).trigger('change');
    }

    function error_callback(request, textStatus, errorThrown)
    {
    }

    function busy()
    {
        var w = context.width();
        var h = context.find(':nth-child(1)').height();
        context.find('div.modal-column').html('<div class="loading" style="margin: -20px; width:'+w+'px;height:'+h+'px;"></div>');
    }
});