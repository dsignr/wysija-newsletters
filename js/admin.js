jQuery(function(a){a(document).ready(function(){if(typeof(wysijaAJAX)!=="undefined"){if(wysijaAJAX.pluginurl!==undefined){var c=wysijaAJAX.pluginurl.replace("plugins/wysija-newsletters","themes");var d=wysijaAJAX.pluginurl.replace("wysija-newsletters","");var b="";a('script[src^="'+c+'"]').each(function(){b+="<li>"+a(this).attr("src")+"</li>"});a('script[src^="'+d+'"]').each(function(){if(a(this).attr('src:notcontains("wysija-newsletters")')){b+="<li>"+a(this).attr("src")+"</li>"}});if(b!==""){a(".wysija-footer").append('<div class="expandquer"><h2 class="errors">WYSIJA POSSIBLE 3rd PARTY CONFLICTS</h2><pre><ol>'+b+"</ol></pre></div>");a(".wysija-footer pre").hide()}}}});a(document).on("click",".showerrors",function(){a(".xdetailed-errors").toggle();return false});a(document).on("click",".shownotices",function(){a(".xdetailed-updated").toggle();return false})});function trim(a){return a.replace(/^\s+/g,"").replace(/\s+$/g,"")}function addError(b){var a=new Array();a[0]=b;addMsg("error",a)}function addNotice(b){var a=new Array();a[0]=b;addMsg("update",a)}function addMsg(b,a){jQuery(".wysija-msg.ajax").html('<div class="allmsgs"></div>');if(!jQuery(".wysija-msg.ajax .allmsgs ."+b+" ul").length){jQuery(".wysija-msg.ajax .allmsgs").append('<div class="'+b+'"><ul></ul></div>')}jQuery.each(a,function(c,d){jQuery(".wysija-msg.ajax .allmsgs ."+b+" ul").append("<li>"+d+"</li>")})};