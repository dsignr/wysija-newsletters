jQuery(function(a){a('a.wp-first-item[href="admin.php?page=wysija_campaigns"]').html(wysijatrans.newsletters)});function trim(a){return a.replace(/^\s+/g,"").replace(/\s+$/g,"")}function addError(b){var a=new Array();a[0]=b;addMsg("error",a)}function addNotice(b){var a=new Array();a[0]=b;addMsg("update",a)}function addMsg(b,a){jQuery(".wysija-msg.ajax").html('<div class="allmsgs"></div>');if(!jQuery(".wysija-msg.ajax .allmsgs ."+b+" ul").length){jQuery(".wysija-msg.ajax .allmsgs").append('<div class="'+b+'"><ul></ul></div>')}jQuery.each(a,function(c,d){jQuery(".wysija-msg.ajax .allmsgs ."+b+" ul").append("<li>"+d+"</li>")})}jQuery(".showerrors").live("click",function(){jQuery(".xdetailed-errors").toggle()});jQuery(".shownotices").live("click",function(){jQuery(".xdetailed-updated").toggle()});