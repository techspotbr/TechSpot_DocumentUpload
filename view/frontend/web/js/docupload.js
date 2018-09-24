require(['jquery','docuploadPf','docuploadPj'], function($, docuploadPf, docuploadPj){
    'use strict';
    
    $(document).ready(
        function(){
            docuploadPf();
            docuploadPj();        
        }
    );
});