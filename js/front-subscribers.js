jQuery(function(c){function b(){if(c(this).validationEngine("validate")){wysijaAJAX.task="save";wysijaAJAX.data=c(this).serializeArray();wysijaAJAX.formid=c(this).attr("id");jQuery.WYSIJA_SEND()}return false}c(document).ready(function(){c(".form-valid-sub").validationEngine("attach",{promptPosition:"centerRight",scroll:false,validationEventTrigger:"submit"});c(".form-valid-sub").submit(b);c('input[name="wysija[user][email]"]').blur(function(){c(this).val(a(c(this).val()))})});function a(d){return d.replace(/^\s+/g,"").replace(/\s+$/g,"")}jQuery.WYSIJA_SEND=function(){c("#msg-"+wysijaAJAX.formid).html('<div class="allmsgs"><blink>'+wysijaAJAX.loadingTrans+"</blink></div>");c("#"+wysijaAJAX.formid).fadeOut();wysijaAJAX._wpnonce=c("#wysijax").val();c.ajax({type:"POST",url:wysijaAJAX.ajaxurl,data:wysijaAJAX,success:function(d){c("#msg-"+wysijaAJAX.formid).html('<div class="allmsgs"></div>');if(d.result){c("#msg-"+wysijaAJAX.formid+" .allmsgs").html('<div class="updated">'+c("#"+wysijaAJAX.formid+' input[name="message_success"]').val()+"</div>")}else{c("#"+wysijaAJAX.formid).fadeIn();c.each(d.msgs,function(f,e){if(!c("#msg-"+wysijaAJAX.formid+" .allmsgs ."+f+" ul").length){c("#msg-"+wysijaAJAX.formid+" .allmsgs").append('<div class="'+f+'"><ul></ul></div>')}c.each(e,function(g,h){c("#msg-"+wysijaAJAX.formid+" .allmsgs ."+f+" ul").append("<li>"+h+"</li>")})})}},error:function(e,d){alert("Request error not JSON:"+e.responseText)},dataType:"json"})};jQuery(".showerrors").live("click",function(){jQuery(".xdetailed-errors").toggle()});jQuery(".shownotices").live("click",function(){jQuery(".xdetailed-updated").toggle()})});