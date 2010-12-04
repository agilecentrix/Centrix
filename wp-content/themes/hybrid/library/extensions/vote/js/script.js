jQuery(document).ready(function() {
    alert('load');
    jQuery('div.vote a.upcount').live('click', function(){
        alert('1');
    });
    
    jQuery('div.vote a.downcount').live('click', function(){
        alert('2');
    });
});