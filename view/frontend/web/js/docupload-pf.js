define(['jquery'], function($) {
    'use strict';
    return function(){
        $(".checkbox-married").change(function() {
            if(this.checked) {
                $('.married-fields').show();
            } else {
                $('.married-fields').hide();
            }
        });
    };
});