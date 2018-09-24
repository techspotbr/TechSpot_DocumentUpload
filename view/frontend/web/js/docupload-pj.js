define(['jquery'], function($) {
    'use strict';
    return function(){
        $(".checkbox-partners").change(function() {
            if(this.checked) {
                $('.partners-fields').show();
            } else {
                $('.partners-fields').hide();
            }
        });
    };
});