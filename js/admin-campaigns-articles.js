var allposts={};jQuery(function(c){c("#gallery-form").submit(function(){d();return false});function d(){var e="<ul>";e+="<li><img title='Loading' alt='loading' src='../wp-content/plugins/wysija-newsletters/img/wpspin_light.gif' /></li>";e+="</ul>";c("#search-results").html(e);wysijaAJAX.task="getarticles";wysijaAJAX.search=c("#search-box").val();wysijaAJAX._wpnonce=c("#wysijax").val();jQuery.ajax({type:"POST",url:wysijaAJAX.ajaxurl,data:wysijaAJAX,success:a,error:b,dataType:"json"})}function a(e){if(e.result["result"]){allposts=e.result["posts"];var f="<ul>";var h="";var g="";jQuery.each(e.result["posts"],function(j,i){if(i.post_firstimage==null){h="";g="";titleimg="No Picture"}else{h=i.post_firstimage["src"];g=i.post_title;titleimg=i.post_title}f+="<li onClick='return selectArticle("+j+")'><img height='36px' title='"+titleimg+"' src='"+h+"' alt='"+g+"' /> &nbsp;&nbsp;<h3>"+i.post_title+"</h3></li>"});f+="</ul>"}else{var f="<ul>";f+="<li><strong>"+e.result["msg"]+"</strong></li>";f+="</ul>"}c("#search-results").html(f)}function b(f,e){alert("Request error not JSON:"+f.responseText)}wysijaAJAX.task="getarticles";wysijaAJAX._wpnonce=jQuery("#wysijax").val();jQuery.ajax({type:"POST",url:wysijaAJAX.ajaxurl,data:wysijaAJAX,success:a,error:b,dataType:"json"})});function selectArticle(a){window.parent.WysijaPopup.getInstance().callback(allposts[a]["html"]);window.parent.WysijaPopup.close()};