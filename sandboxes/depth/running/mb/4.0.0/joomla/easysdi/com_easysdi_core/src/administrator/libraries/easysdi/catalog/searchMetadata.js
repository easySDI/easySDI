js = jQuery.noConflict();

js('document').ready(function() {
    
    js('.searchtype').click(function(){
        var btn = js(this);
        if(btn.hasClass('active')){
            return;
        }
        js('fieldset[name="advanced"]').toggle('fast',function(){
            js('.searchtype').each(function(){
                if(js(this).hasClass('active')){
                    js(this).removeClass('active');
                }else{
                    js(this).addClass('active');
                }
            });
        });
        
    });
    
});

