jQuery(function(e){function i(){switch(e("#restapipossible").hide(),e("#smtp-host").val()){case"smtp.gmail.com":""==b&&(e("#smtp-port").val("465"),e("#smtp-secure").val("ssl"),e("#smtp-login").val("your_username@gmail.com"));break;case"smtp.sendgrid.net":e("#restapipossible").show();break;case"":""==b&&(e("#smtp-port").val("25"),e("#smtp-secure").val("0"),e("#smtp-login").val(""))}""==b&&e("#smtp-secure").change()}function n(){"smtp"==e('input[name="wysija[config][sending_method]"]:checked').val()&&("none"!=e("#restapipossible").css("display")&&e("#smtp-rest").attr("checked")?e(".choice-no-restapi").hide():e(".choice-no-restapi").show())}function s(){1===parseInt(e('input[name="wysija[config][confirm_dbleoptin]"]:checked').attr("value"))?e(".confirmemail").fadeIn():e(".confirmemail").fadeOut()}function t(){e(".choice-frequency").hide(),-1!==e.inArray(e("#sending-emails-each").val(),["one_min","two_min","five_min","ten_min"])?e(".choice-under15").show():-1!==e.inArray(e("#sending-emails-each").val(),["fifteen_min"])||e(".choice-above15").show()}function a(){"gmail"==e('input[name="wysija[config][sending_method]"]:checked').val()?(e("#sending-emails-number").val("20"),e('select[name="wysija[config][sending_emails_each]"]').val("hourly"),e("#sending-emails-number").attr("readonly","readonly"),e('select[name="wysija[config][sending_emails_each]"]').attr("disabled","disabled")):(e('select[name="wysija[config][sending_emails_each]"]').removeAttr("disabled"),e("#sending-emails-number").removeAttr("readonly"))}function c(){return wysijaAJAX.task="send_test_mail",wysijaAJAX.data=e("form").serializeArray(),wysijaAJAX.popTitle=wysijatrans.testemail,wysijaAJAX.dataType="json",e.WYSIJA_SEND(),!1}function o(){return wysijaAJAX.task="bounce_connect",wysijaAJAX.data=e("form").serializeArray(),wysijaAJAX.popTitle=wysijatrans.bounceconnect,wysijaAJAX.dataType="json",wysijaAJAXcallback.onSuccess=function(i){var n="";return i.result.result&&(n='<a class="bounce-submit button-secondary" href2="admin.php?page=wysija_campaigns&action=test_bounce">'+wysijatrans.processbounce+"</a>"),displaychange?e(".allmsgs.ui-dialog-content.ui-widget-content").append(n):e("#bounce-connector").after(n),!0},e.WYSIJA_SEND(),!1}function r(){return wysijaAJAX.task="bounce_process",wysijaAJAX.data=e("form").serializeArray(),wysijaAJAX.popTitle=wysijatrans.processbounceT,wysijaAJAX.dataType="html",e(".allmsgs").dialog(),e.WYSIJA_SEND(),!1}function m(){"undefined"!=typeof this?e.each(e(".activateInput"),function(){d(this)}):d(this)}function d(i){e(i).attr("checked")?e("#"+e(i).attr("id")+"_linkname").show():e("#"+e(i).attr("id")+"_linkname").hide()}function h(){switch(e("#ms-restapipossible").hide(),e("#ms-smtp-host").val()){case"smtp.gmail.com":""==_&&(e("#ms-smtp-port").val("465"),e("#ms-smtp-secure").val("ssl"),e("#ms-smtp-login").val("your_username@gmail.com"));break;case"smtp.sendgrid.net":e("#ms-restapipossible").show();break;case"":""==_&&(e("#ms-smtp-port").val("25"),e("#ms-smtp-secure").val("0"),e("#ms-smtp-login").val(""))}""==_&&e("#ms-smtp-secure").change()}function l(){"smtp"==e('input[name="wysija[config][ms_sending_method]"]:checked').val()&&("none"!=e("#ms-restapipossible").css("display")&&e("#ms-smtp-rest").attr("checked")?e(".ms-choice-no-restapi").hide():e(".ms-choice-no-restapi").show())}function u(){-1!==e.inArray(e("#ms-sending-emails-each").val(),["one_min","two_min","five_min","ten_min"])?e(".ms-choice-under15").show():e(".ms-choice-under15").hide()}function p(){"one-for-all"==e('input[name="wysija[config][ms_sending_config]"]:checked').val()?e(".choice-one-for-all").show():e(".choice-one-for-all").hide()}function g(){e(".super-advanced, .hide-geeky-options").show(),e(".show-more-geeky-options").hide(),e.cookie("geeky_option",1)}function f(){e(".super-advanced, .hide-geeky-options").hide(),e(".show-more-geeky-options").show(),e.cookie("geeky_option",0)}function y(){geekyOption=e.cookie("geeky_option"),"undefined"!=typeof geekyOption&&geekyOption>0?g():f()}function w(){return wysijaAJAX.task="send_test_mail_ms",wysijaAJAX.data=e("form").serializeArray(),wysijaAJAX.popTitle=wysijatrans.testemail,wysijaAJAX.dataType="json",e.WYSIJA_SEND(),!1}var b=e("#smtp-login").val();e(".hidechoice").hide(),e(".choice-sending-method-"+e('input[name="wysija[config][sending_method]"]:checked').val()).show(),e('input[name="wysija[config][sending_method]"]').change(function(){e(".hidechoice").hide(),e(".choice-sending-method-"+this.value).show(),a()}),e("#sending-emails-each").change(function(){t()}),e("#linksendingmethod").click(function(){e("#tabs").tabs("select",e(this).attr("href"))}),e("#mainmenu li a").click(function(){e("#redirecttab").val(e(this).attr("href"))}),e('input[name="wysija[config][confirm_dbleoptin]"]').change(s),e("#confirm_dbleoptin-1").click(function(){return confirm(wysijatrans.doubleoptinon)}),e("#confirm_dbleoptin-0").click(function(){return confirm(wysijatrans.doubleoptinoff)}),e('input[name="wysija[config][sending_emails_site_method]"]').change(function(){"sendmail"==e('input[name="wysija[config][sending_emails_site_method]"]:checked').val()?e("#p-sending-emails-site-method-sendmail-path").show():e("#p-sending-emails-site-method-sendmail-path").hide()}),e('input[name="wysija[config][sending_emails_site_method]"]').change(),e("#smtp-host").keyup(i),e("#smtp-rest").change(n),e("#button-regenerate-dkim").click(function(){return e("#sending-emails-site-method-phpmail").attr("checked","checked"),e("#dkim_regenerate").val("regenerate"),e("#wysija-settings").submit(),!1}),e("#send-test-mail-phpmail").click(function(){return e("#sending-emails-site-method-phpmail").attr("checked","checked"),c(),!1}),e("#send-test-mail-sendmail").click(function(){return e("#sending-emails-site-method-sendmail").attr("checked","checked"),c(),!1}),e("#send-test-mail-smtp").click(function(){return c(),!1}),e("#bounce-connector").click(o),e(document).on("click",".bounce-submit",function(){return r(),e(".allmsgs").dialog("close"),tb_show(wysijatrans.processbounceT,e(this).attr("href2")+"&KeepThis=true&TB_iframe=true&height=400&width=600",null),tb_showIframe(),!1}),e(".forwardto").change(function(){e(this).attr("checked")?e("#"+e(this).attr("id")+"_input").show():e("#"+e(this).attr("id")+"_input").hide()}),e.each(e(".hideifnovalue"),function(){""==e(this).find("input").val()&&e(this).hide()}),e("#wysija-settings").submit(function(){var i=!1;return e(".bounce-forward-email").each(function(){var n=trim(e(this).val());""!==n&&n==e("#bounce_email").val()&&(e('#wysija-tabs a[href="#bounce"]').trigger("click"),e('#wysija-innertabs a[href="#actions"]').trigger("click"),e(this).css("border","1px solid #CC0000"),e("#bounce-msg-error").addClass("error"),e("#bounce-msg-error").html(wysijatrans.errorbounceforward),i=!0)}),i?!1:(e('select[name="wysija[config][sending_emails_each]"]').removeAttr("disabled"),void 0)}),e("#bounce-process-auto").attr("checked")?e("#bounce-frequency").show():e("#bounce-frequency").hide(),e("#bounce-process-auto").change(function(){e(this).attr("checked")?e("#bounce-frequency").show():e("#bounce-frequency").hide()}),e(".activateInput").change(m),e(document).on("click","#wysija-innertabs a",function(){return e("#wysija-innertabs a").removeClass("nav-tab-active"),e(this).addClass("nav-tab-active"),e(".wysija-innerpanel").hide(),e(e(this).attr("href")).length>0&&e(e(this).attr("href")).show(),e(this).blur(),!1}),e(document).on("click","#wysija-tabs a",function(){return e("#wysija-tabs a").removeClass("nav-tab-active"),e(this).addClass("nav-tab-active"),e(".wysija-panel").hide(),e(e(this).attr("href")).length>0&&(e(e(this).attr("href")).show(),window.location.hash="tab-"+e(this).attr("href").substring(1)),e(this).blur(),!1}),e(document).ready(function(){if(a(),t(),s(),m(),i(),n(),e(".wysija-panel").hide(),e(".wysija-innerpanel").hide(),window.location.hash.length>0){var c="#"+window.location.hash.substring(5);e('#wysija-tabs a[href="'+c+'"]').trigger("click")}else e("#wysija-tabs .nav-tab-active").trigger("click");e("#wysija-innertabs .nav-tab-active").trigger("click"),e("#analytics-0").is(":checked")&&e("#advanced .industry").hide(),e("#analytics-1").change(function(){"1"==e(this).val()&&e("#advanced .industry").show()}),e("#analytics-0").change(function(){"0"==e(this).val()&&e("#advanced .industry").hide()}),e("#"+getURLParameter("scroll_to")).length>0&&(e("html,body").animate({scrollTop:e("#"+getURLParameter("scroll_to")).offset().top-e("#wpadminbar").height()},500),e("#"+getURLParameter("scroll_to")).css({"background-color":"#f8fcff","font-size":"16px","font-weight":"bold"}))}),e("#dkimpub, #domainrecord").focus(function(){this.select()}),e("#dkimpub, #domainrecord").click(function(){this.select()}),e("#dkimpub, #domainrecord").mouseup(function(){this.select()});var _=e("#ms-smtp-login").val();e(".ms-hidechoice").hide(),e(".ms-choice-sending-method-"+e('input[name="wysija[config][ms_sending_method]"]:checked').val()).show(),e('input[name="wysija[config][ms_sending_method]"]').change(function(){e(".ms-hidechoice").hide(),e(".ms-choice-sending-method-"+this.value).show()}),e("#ms-sending-emails-each").change(function(){u()}),e('input[name="wysija[config][ms_sending_config]"]').change(function(){p()}),e('input[name="wysija[config][ms_sending_emails_site_method]"]').change(function(){"sendmail"==e('input[name="wysija[config][ms_sending_emails_site_method]"]:checked').val()?e("#ms_p-sending-emails-site-method-sendmail-path").show():e("#ms_p-sending-emails-site-method-sendmail-path").hide()}),e('input[name="wysija[config][ms_sending_emails_site_method]"]').change(),e("#ms-smtp-host").keyup(h),e("#ms-smtp-rest").change(l),e("#ms-send-test-mail-phpmail").click(function(){return e("#ms-sending-emails-site-method-phpmail").attr("checked","checked"),w(),!1}),e("#ms-send-test-mail-sendmail").click(function(){return e("#ms-sending-emails-site-method-sendmail").attr("checked","checked"),w(),!1}),e("#ms-send-test-mail-smtp").click(function(){return w(),!1}),e.each(e(".hideifnovalue"),function(){""==e(this).find("input").val()&&e(this).hide()}),e("table.capabilities_form .view_all").click(function(){e("table.capabilities_form tr.hidden").removeClass("hidden"),objTr=e(this).parents("tr")[0],e(objTr).remove()}),e(".show-more-geeky-options").click(g),e(".hide-geeky-options").click(f),e(document).ready(function(){p(),u(),h(),l(),y()})});