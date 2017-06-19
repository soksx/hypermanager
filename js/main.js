String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
};

$(document).on({
    ajaxStart: function() { -1==document.cookie.indexOf("hmssid")&&location.reload(); }
    //     ajaxStop: function() { $("body").removeClass("loading"); }    
});

$(document).ready(function() {
    // var acceptCookies = readCookie("a_cookie");
    $("#logout").click(logOut);
    $("#add-server-btn").click(function (){
        var addServerData = $("#add-server").serialize();
        if (addServerData.split('&')[0].split('=')[1] && addServerData.split('&')[1].split('=')[1] && addServerData.split('&')[2].split('=')[1] || _sid != null)
            addServer(addServerData, _sid);
        else
            showAlert(0, "Empty fields detected");
    });
    $("#close-add-server-btn").click(function(){
		$(".active").removeAttr("class");
    });
    $("#refresh-time-btn").click(function(){
        var serverId = document.querySelector('a[serverid="' + _sid + '"]').getAttribute("serverid");
        var rTime = $("#refresh-time").serialize();
        if (parseInt(rTime.split('=')[1]) >= 5){
            addServerUserInfo($("#refresh-time").serialize(), serverId);
            updateInfoInterval(parseInt(rTime.split('=')[1]));
            $("#refresh-time-btn").prop("disabled", true);
            $("#stop-time-btn").prop("disabled", false);
            $("#refresh-server-btn").prop("disabled", true);
        }else if($("#rtime").val().length <= 0){
            showUserInfoAlert(0, "Para inicializar el auto-refresco es necesario un tiempo (seg)");
        }
        else{
            showUserInfoAlert(0, "El timepo de refresco no puede ser menor de 5");
        }
    });
    $("#stop-time-btn").click(function(){
        var serverId = document.querySelector('a[serverid="' + _sid + '"]').getAttribute("serverid");
        addServerUserInfo("rtime=" + 0, serverId);
        updateInfoInterval(0);
        $("#rtime").val('');
        $("#refresh-time-btn").prop("disabled", false);
        $("#stop-time-btn").prop("disabled", true);
        $("#refresh-server-btn").prop("disabled", false);
            //showUserInfoAlert(1, "Cancelado el auto refresco.");
    });
    $("#refresh-server-btn").click(function(){
        var serverId = document.querySelector('a[serverid="' + _sid + '"]');
        showServerInfo(serverId);
            //showUserInfoAlert(1, "Cancelado el auto refresco.");
    });
    $("#how-notification-btn").click(function(){
        var serverId = document.querySelector('a[serverid="' + _sid + '"]').getAttribute("serverid");
        var hNot = $("#how-notification").serialize();
                if($("#hdd").val() > 100 || $("#ram").val() > 100 || $("#proc").val() > 100){
        	showUserInfoAlert(0, "El porcentaje de notificación no puede ser mayor de 100");
        }
        else{
        	$("#hdd").val(($("#hdd").val() == 0) ? null : $("#hdd").val())
        	$("#ram").val(($("#ram").val() == 0) ? null : $("#ram").val())
        	$("#proc").val(($("#proc").val() == 0) ? null : $("#proc").val())
        	addServerUserInfo(hNot, serverId);
        }
        // $.each($('#how-notification input[type=text]')
        //     .filter(function(idx){
        //         return $(this).val().length === 0
        //     }),
        //     function(idx, el){
        //         // attach matched element names to the formData with a chosen value.
                
        //         hNot += '&' + $(el).attr('name') + '=' + 0;
        //     }
        // );
        // var rTime = $("#refresh-time").serialize();
        // if(parseInt(rTime.split('=')[1]) >= 5 || parseInt(rTime.split('=')[1]) == 0)
        // 	hNot += "&rtime=" + parseInt(rTime.split('=')[1]);
    });
    $("#where-notification-btn").click(function(){
        var serverId = document.querySelector('a[serverid="' + _sid + '"]').getAttribute("serverid");
        var wNot = $("#where-notification").serialize();
        $.each($('#where-notification input[type=checkbox]')
            .filter(function(idx){
                return $(this).prop('checked') === false
            }),
            function(idx, el){
                // attach matched element names to the formData with a chosen value.
                
                wNot += '&' + $(el).attr('name') + '=' + 0;
            }
        );
        //var rTime = $("#refresh-time").serialize();
        //if(parseInt(rTime.split('=')[1]) >= 5 || parseInt(rTime.split('=')[1]) == 0)
        //	wNot += "&rtime=" + parseInt(rTime.split('=')[1]);
        addServerUserInfo(wNot, serverId);
            //updateInfoInterval(rTime.split('=')[1]);
    });

    $("#hdd-select-info").change(function(){
    	var total = $("#hdd-select-info > option:nth-child(" + $("#hdd-select-info").val() + ")").attr("totalhdd");
    	var used = $("#hdd-select-info > option:nth-child(" + $("#hdd-select-info").val() + ")").attr("usedhdd");
    	loadMoreHddInfo(total, used);
    });

    $("#a-add-server").click(function(){
        _sid = null;
        $("#add-edit-server").html("Añadir servidor");
        clearForm("add-server");
    });

    if( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        if (!Notification) {
            alert('Desktop notifications not available in your browser. Try Chromium.'); 
            return;
        }

        if (Notification.permission !== "granted")
            Notification.requestPermission();
    }

    // if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    //     $("#nav-accordion > li.mt.dcjq-parent-li").attr("hidden", "true"); //Disable add server
    // }
});
/*
* Crea una cookie
*
* @author <StackOverFlow>
* @param <name> Nombre de la cookie
* @param <value> Valor de la cookie
* @param <days> Duracción de la cookie 
*
*/
function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";               

    document.cookie = name + "=" + value + expires + "; path=/";
}
/*
* Lee una cookie
*
* @author <StackOverFlow>
* @param <name> Nombre de la cookie
* @return cookie/null devuelve el valor de la cookie o null
*
*/
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
/*
* Elimina una cookie
*
* @author <StackOverFlow>
* @param <name> Nombre de la cookie
*
*/
function eraseCookie(name) {
    createCookie(name, "", -1);
}
/*
* Cierra la sesión
*
* @author IDC
*
*/
function logOut(){
    eraseCookie("hmssid");
    location.reload();
}
/*
* Cambia el estilo y atributos a un div para usarlo como alerta
*
* @author IDC
* @param <status> Por defecto 0 = incorrecto o 1 correcto
* @param <msg> mensaje de la alerta
*
*/
function showAlert(status = 0, msg){
    var responseDiv = $("#server-response");
    responseDiv.css("display", "flex");
    responseDiv.css("height", "30px");
    responseDiv.removeAttr("hidden");
    responseDiv.css("background-color", (status != 0) ? "#5cb85c" : "#d9534f");
    responseDiv.html(msg);
    responseDiv.fadeIn(500).delay(1000).fadeOut(1500);
    // if(status != 0){
    //     location.reload();
    // }
    setTimeout(function() {
    	if(status)
        	$("#close-add-server-btn").click(); //Colapsa el menu de añadir servidor.
        responseDiv.css("height", "30px");
        responseDiv.attr("hidden", "true");
        responseDiv.css("display", "none");
    }, 1500);
}
/*
* Cambia el estilo y atributos a un div para usarlo como alerta
*
* @author IDC
* @param <status> Por defecto 0 = incorrecto o 1 correcto
* @param <msg> mensaje de la alerta
*
*/
function showUserInfoAlert(status = 0, msg){
    var responseDiv = $("#userinfo-alert");
    responseDiv.removeAttr("hidden");
    responseDiv.css("background-color", (parseInt(status) != 0) ? "#5cb85c" : "#d9534f");
    if(parseInt(status) == 2)
        responseDiv.css("background-color", "#DAB94F");
    responseDiv.html(msg);
    responseDiv.fadeIn(500).delay(2000).fadeOut(2500);
    // if(status != 0){
    //     location.reload();
    // }
    responseDiv.attr("hidden", "true");
}
/*
* Añade un servidor a la base de datos
*
* @author IDC
* @param <fData> Información serializada de un formulario.
*
*/
function addServer(fData, serverid = null){
    var formData = fData;
    if(serverid != null){
        formData += "&serverid=" + serverid;
    }
    $.ajax({
        type: "POST",
        url: "lib/crud/modifyserver",
        data: formData,
        datatype: "text",
        success: function(data, status){
            var respnse = $.parseJSON(data);
            showAlert(respnse[0], respnse[1]);
            if(serverid == null){
                var aliasOrName = (fData.split('&')[3].split('=')[1].length > 0) ? fData.split('&')[3].split('=')[1] : fData.split('&')[0].split('=')[1];
            // console.log(aliasOrName);
            // console.log(fData);
                var html = '<li class="dcjq-parent-li" id="serverid_' + respnse[2] +'"><a onclick="showServerInfo(this);" href="javascript:;" serverid="' + respnse[2] + '"><i class="fa fa-desktop"></i><span>' + aliasOrName.toUpperCase() + '</span></a> <i onclick="editServer(\'serverid_' + respnse[2] +'\');" class="fa fa-pencil edit-btn"></i><i onclick="deleteServer(\'serverid_' + respnse[2] +'\');" class="fa fa-times delete-btn"></i></li>';
                if(respnse[0] == 1){
                    $("#nav-accordion").append(html);
                }
            }
            clearForm("add-server");
            //add function to show <new server> (all)
        }
    });
}
/*
* Elimina un servidor a la base de datos
*
* @author IDC
* @param <serverid> Id del sevidor.
*
*/
function deleteServer(serverid = null){
    if(serverid){
        $.ajax({
            type: "POST",
            url: "lib/crud/deleteserver",
            data: "serverid=" + serverid.split('_')[1],
            datatype: "text",
            success: function(data, status){
                var respnse = $.parseJSON(data);
                //showAlert(respnse[0], respnse[1]);
                if(respnse[0] == 1){
                    $("#" + serverid).remove();
                }
                //add function to show <new server> (all)
            }
        });
    }
}

function showServerInfo(obj, doclick = true){
	updateInfoInterval(0);
     //console.log(obj.childNodes);
    var serverid = obj.getAttribute("serverid");
    _sid = serverid;
    _snme = obj.children[1].innerHTML;
    $(document).prop('title', "Hypermanager - " + obj.children[1].innerHTML);
    $.ajax({
            type: "POST",
            url: "lib/info/serverinfo",
            data: "serverid=" + serverid,
            datatype: "text",
            beforeSend: function(){
     			if(doclick)
     				$("body").addClass("loading");
   			},
            success: function(data, status){
                $("#landing-page").css("display", "none");
                $("#server-show").css("display", "block");
            	if(!data)
            		location.reload();
            	if(doclick)
     				$("body").removeClass("loading");
                var response = [];
                try{
                	response = $.parseJSON(data);
                }catch(ex){
					response[0] = "offline";
                }
                //console.log(response);
                var barChart = '<div id="month-{0}" class="value tooltips"  data-original-title="{1}" data-toggle="tooltip" data-placement="top">{2}%</div>'; 
                //General Info
                $("#server-status").html("{0}");
                $("#more-server-status").html("El servidor esta {0}");
                $("#ram-status").html("{0}{1} / {2}{3}");
                $("#more-ram-status").html("{0}{1} de {2}{3} ram usada del servidor");
                $("#hdd-status").html("{0}{1} / {2}{3}");
                $("#more-hdd-status").html("Usados de HDD {0}{1} de {2}{3}");
                //Specific info
                $("#ram-chart-used").html("<i class='fa fa-microchip'></i> {0} {1}");
                $("#ram-chart-perc").html("{0}% En uso");
                $("#hdd-chart-used").html("<i class='fa fa-hdd-o'></i> {0} {1}");
                $("#hdd-chart-perc").html("{0}% En uso");
                $("#proc-chart-used").html("<i class='fa fa-clock-o'></i> {0} GHz");
                $("#proc-chart-perc").html("{0}% En uso");
                //REMOVE BARS
                $(".value").remove();
                //REMOVE Doughnut CHARTS
                $("#serverstatus01").remove();
                $("#serverstatus02").remove();
                $("#serverstatus03").remove();
                //CREATE Doughnut CHARTS
                $("#ss01 > div:nth-child(1)").after('<canvas class="server-chart" id="serverstatus01" height="120" width="120"></canvas>');
                $("#ss02 > div:nth-child(1)").after('<canvas class="server-chart" id="serverstatus02" height="120" width="120"></canvas>');
                $("#ss03 > div:nth-child(1)").after('<canvas class="server-chart" id="serverstatus03" height="120" width="120"></canvas>');
                //REMOVE more-hdd-info
                $("#ss03").off("click");
                
                if (response[0] != "offline"){
                    /*SERVER*/
                    $("#server-status").html($("#server-status").html().format('EN LÍNEA'));
                    $("#server-status").css('color', 'green');
                    $("#more-server-status").html($("#more-server-status").html().format('EN LÍNEA'));
                    /*RAM*/
                    var ramValues = convertToHuman(response[0],response[1]);
                    $("#ram-status").html($("#ram-status").html().format(ramValues[1], ramValues[2], ramValues[0], ramValues[2]));
                    $("#more-ram-status").html($("#more-ram-status").html().format(ramValues[1], ramValues[2], ramValues[0], ramValues[2]));
                    var ramPercentage = (calculatePercentages(ramValues[0], ramValues[1])) ? calculatePercentages(ramValues[0], ramValues[1]) : 0;
                    var ramDoughnutData = [{value: ramPercentage, color: calculateColor(ramPercentage)}, {value: 100-ramPercentage, color:"#444c57"}];
                    var ramDoughnut = new Chart(document.getElementById("serverstatus02").getContext("2d")).Doughnut(ramDoughnutData);
                    $("#ram-chart-today").html(new Date().toLocaleString());
                    $("#ram-chart-used").html($("#ram-chart-used").html().format(ramValues[1], ramValues[2]));
                    $("#ram-chart-perc").html($("#ram-chart-perc").html().format(ramPercentage));
                    /*HDD*/
                    var hddValues = convertToHuman(response[3],response[4]);
                    $("#hdd-status").html($("#hdd-status").html().format(hddValues[1], hddValues[2], hddValues[0], hddValues[2]));
                    $("#more-hdd-status").html($("#more-hdd-status").html().format(hddValues[1], hddValues[2], hddValues[0], hddValues[2]));
                    var hddPercentage = (calculatePercentages(hddValues[0], hddValues[1]) ? calculatePercentages(hddValues[0], hddValues[1]) : 0);
                    var hddDoughnutData = [{value: hddPercentage, color: calculateColor(hddPercentage)}, {value: 100-hddPercentage, color:"#444c57"}];
                    var hddDoughnut = new Chart(document.getElementById("serverstatus03").getContext("2d")).Doughnut(hddDoughnutData);
                    $("#hdd-chart-today").html(new Date().toLocaleString());
                    $("#hdd-chart-used").html($("#hdd-chart-used").html().format(hddValues[1], hddValues[2]));
                    $("#hdd-chart-perc").html($("#hdd-chart-perc").html().format(hddPercentage));
                    /*PROCESSOR*/
                    var procPercentage = Math.round(response[6]);
                    var procDoughnutData = [{value: procPercentage, color: calculateColor(procPercentage)}, {value: 100-procPercentage, color:"#444c57"}];
                    var procDoughnut = new Chart(document.getElementById("serverstatus01").getContext("2d")).Doughnut(procDoughnutData);
                    $("#proc-chart-today").html(new Date().toLocaleString());
                    $("#proc-chart-used").html($("#proc-chart-used").html().format(parseFloat(response[7]/1000).toFixed(2)));
                    $("#proc-chart-perc").html($("#proc-chart-perc").html().format(procPercentage));
                    /*CHART BAR*/
                    generateMaxColumn(response, 8, 14);
                    var maxBar = parseInt($("#chart-bar-col-values > li:nth-child(1) > span").html());
                    $("#bar0").append(barChart.format("zero", response[8], calculatePercentages(maxBar, response[8])));
                    $("#bar1").append(barChart.format("one", response[9], calculatePercentages(maxBar, response[9])));
                    $("#bar2").append(barChart.format("two", response[10], calculatePercentages(maxBar, response[10])));
                    $("#bar3").append(barChart.format("three", response[11], calculatePercentages(maxBar, response[11])));
                    $("#bar4").append(barChart.format("four", response[12], calculatePercentages(maxBar, response[12])));
                    $("#bar5").append(barChart.format("five", response[13], calculatePercentages(maxBar, response[13])));
                    $("#bar6").append(barChart.format("six", response[14], calculatePercentages(maxBar, response[14])));
                    $(".tooltip").remove();
                    $('.popovers').remove();
                    $('.tooltips').tooltip();
                    $('.popovers').popover();
                    if ($(".custom-bar-chart")) {
                        $(".bar").each(function () {
                            var i = $(this).find(".value").html();
                            $(this).find(".value").html("");
                            $(this).find(".value").animate({
                                height: i
                            }, 2000)
                        })
                    }
                    /*USER SETTINGS*/
                    $('#rtime').val((response[15] == "" || response[15] == "0") ? null : response[15]);
                    if ($('#rtime').val()){
                        $("#refresh-time-btn").prop("disabled", true);
                        $("#stop-time-btn").prop("disabled", false);
                        $("#refresh-server-btn").prop("disabled", true);
                    }else{
                        $("#refresh-time-btn").prop("disabled", false);
                        $("#stop-time-btn").prop("disabled", true);
                        $("#refresh-server-btn").prop("disabled", false);
                    }
                    updateInfoInterval((response[15] == "") ? 0 : parseInt(response[15]));
                    $('#hdd').val((response[16] == "" || response[16] == "0") ? null : response[16]);
                    $('#ram').val((response[17] == "" || response[17] == "0") ? null : response[17]);
                    $('#proc').val((response[18] == "" || response[18] == "0") ? null : response[18]);
                    // checkIfIsTrue($('#hdd'), response[16]);
                    // checkIfIsTrue($('#ram'), response[17]);
                    // checkIfIsTrue($('#proc'), response[18]);
                    checkIfIsTrue($('#web'), response[19]);
                    $('#useremail').val((response[20] == "") ? null : response[20]);
                    $('#tgusername').val((response[21] == "") ? null : response[21]);
                    //NOTIFICATIONS

                    if (procPercentage >= parseInt($('#proc').val()))
                        createProcNotification(procPercentage);
                    if (ramPercentage >= parseInt($('#ram').val()))
                        createRamNotification(ramPercentage);
                    if (hddPercentage >= parseInt($('#hdd').val()))
                        createHddNotification(hddPercentage);
                    //SEPARATED HDD INFO
                    $("#ss03").click(function(){
				    	if($("#hdd-chart-perc") && $("#hdd-chart-perc").html != "-% En uso"){
				    		$("#hdd-select-info").html('');
						$("#hdd-moreinfo-modal").modal("show");
				    		//LOAD INFO INTO SELECT BOX
				    		var moreHddData = response[22];
				    		for (var i = 0; i < moreHddData.length; i++){
				    			// totalHddPartition[i] = moreHddData[i].split('-')[0].split('x')[0];
				    			// usedHddPartition[i] = moreHddData[i].split('-')[0].split('x')[0].split('x')[1];
				    			// hddPartitionName[i]	= moreHddData[i].split('-')[1];
				    			$("#hdd-select-info").append("<option value='" + (+i+1) + "' totalhdd='" + moreHddData[i].split('-')[0].split('x')[0] + "' usedhdd='" + moreHddData[i].split('-')[0].split('x')[1] + "'>" + moreHddData[i].split('-')[1] + "</option>");
				    		}
				    		loadMoreHddInfo(moreHddData[0].split('-')[0].split('x')[0], moreHddData[0].split('-')[0].split('x')[1]);
				    	}
				    });
                }else{
                    /*SERVER*/
                    $("#server-status").html($("#server-status").html().format('DESCONECTADO'));
                    $("#server-status").css('color', 'red');
                    $("#more-server-status").html($("#more-server-status").html().format('DESCONECTADO'));
                    /*RAM*/
                    $("#ram-status").html($("#ram-status").html().format('-', 'MiB', '-', 'MiB'));
                    $("#more-ram-status").html($("#more-ram-status").html().format('-', 'MiB', '-', 'MiB'));
                    var ramDoughnutData = [{value: 0, color:"#68dff0"}, {value: 100-0, color:"#444c57"}];
                    var ramDoughnut = new Chart(document.getElementById("serverstatus02").getContext("2d")).Doughnut(ramDoughnutData);
                    $("#ram-chart-today").html(new Date().toLocaleString());
                    $("#ram-chart-used").html($("#ram-chart-used").html().format('-', 'MiB'));
                    $("#ram-chart-perc").html($("#ram-chart-perc").html().format('-'));
                    /*HDD*/
                    $("#hdd-status").html($("#hdd-status").html().format('-', 'MiB', '-', 'MiB'));
                    $("#more-hdd-status").html($("#more-hdd-status").html().format('-', 'MiB', '-', 'MiB'));
                    var hddDoughnutData = [{value: 0, color:"#68dff0"}, {value: 100-0, color:"#444c57"}];
                    var hddDoughnut = new Chart(document.getElementById("serverstatus03").getContext("2d")).Doughnut(hddDoughnutData);
                    $("#hdd-chart-today").html(new Date().toLocaleString());
                    $("#hdd-chart-used").html($("#hdd-chart-used").html().format('-', 'MiB'));
                    $("#hdd-chart-perc").html($("#hdd-chart-perc").html().format('-', 'MiB'));
                    /*PROCESSOR*/
                    var procPercentage = 0;
                    var procDoughnutData = [{value: procPercentage, color:"#68dff0"}, {value: 100-procPercentage, color:"#444c57"}];
                    var procDoughnut = new Chart(document.getElementById("serverstatus01").getContext("2d")).Doughnut(procDoughnutData);
                    $("#proc-chart-today").html(new Date().toLocaleString());
                    $("#proc-chart-used").html($("#proc-chart-used").html().format('-'));
                    $("#proc-chart-perc").html($("#proc-chart-perc").html().format('-'));

                    /*USER SETTINGS*/
                    $('#rtime').val(null);
                    $('#hdd').val(null);
                    $('#ram').val(null);
                    $('#proc').val(null);
                    checkIfIsTrue($('#web'), null);
                    $('#useremail').val(null);
                    $('#tgusername').val(null);
                    $("#refresh-time-btn").prop("disabled", true);
                    $("#stop-time-btn").prop("disabled", true);
                    $("#refresh-server-btn").prop("disabled", true);
                    /*CHART BAR*/
                    // $("#bar0").append(barChart.format("zero", 0, 0));
                    // $("#bar1").append(barChart.format("zero", 0, 0));
                    // $("#bar2").append(barChart.format("zero", 0, 0));
                    // $("#bar3").append(barChart.format("zero", 0, 0));
                    // $("#bar4").append(barChart.format("zero", 0, 0));
                    // $("#bar5").append(barChart.format("zero", 0, 0));
                    // $("#bar6").append(barChart.format("zero", 0, 0));
                    // $('.tooltips').tooltip();
                    // $('.popovers').popover();
                    // if ($(".custom-bar-chart")) {
                    //     $(".bar").each(function () {
                    //         var i = $(this).find(".value").html();
                    //         $(this).find(".value").html("");
                    //         $(this).find(".value").animate({
                    //             height: i
                    //         }, 2000)
                    //     })
                    // }
                }
                // $("#serverstatus01").width(120);
                // $("#serverstatus01").height(120);
                // $("#serverstatus02").width(120);
                // $("#serverstatus02").height(120);
                // $("#serverstatus03").width(120);
                // $("#serverstatus03").height(120);
            }
        });
    if(doclick){
    	if ($("#nav-accordion").attr("style")){
			if($("#nav-accordion").attr("style").indexOf("block") != -1){
				$("#container > header > div.sidebar-toggle-box > div.fa.fa-bars.tooltips").click(); //Oculta la barra de navegación de la izq
			}
    	}else{
    		$("#container > header > div.sidebar-toggle-box > div.fa.fa-bars.tooltips").click(); //Oculta la barra de navegación de la izq
    	}
    }
}
/*
* Calcula el porcentaje entre dos cantidades
*
* @author IDC
* @param <totalV> Valor total.
* @param <currentV> Valor actual.
*
*/
function calculatePercentages(totalV, currentV){
    return Math.round((currentV / totalV) * 100);
}

/*
* Deveulve el color correspondiente dependiendo del uso
*
* @author IDC
* @param <valuePercent> Porcentaje .
*
*/
function calculateColor(valuePercent){
    return ((valuePercent <= 50) ? "#68dff0" : ((valuePercent > 50 && valuePercent <= 75) ? "#F0B567" : "#F07167"));
}
/*
* Crea un intervalo para actualizar la información del servidor automaticamente
*
* @author IDC
* @param <time> Timepo de obtencion de datos .
*
*/
var _upd, _snme, _sid = null;
function updateInfoInterval(time){
    if (_upd)
        window.clearInterval(_upd);
    var activeObj = document.querySelector('a[serverid="' + _sid + '"]');
    if (activeObj && time >= 5){
        _upd = setInterval(function(){ 
            showServerInfo(activeObj, false);   
        }, time*1000);  
    }
}
/*
* Añade o modifica en la base de datos la 
*
* @author IDC
* @param <time> Timepo de obtencion de datos .
*
*/
function addServerUserInfo(fData, serverId){
    $.ajax({
        type: "POST",
        url: "lib/crud/modifyprops",
        data: "serverid=" + serverId + "&" + fData,
        datatype: "text",
        success: function(data, status){
            var respnse = $.parseJSON(data);
            showUserInfoAlert(respnse[0], respnse[1]);
        }
    });
}

function checkIfIsTrue(obj, value){
    if (value == 1){
        obj.attr("checked", "true");
    }else{
        obj.removeAttr("checked", "true");
    }
}
function createProcNotification(procValue){
    var webProcNot = '<li class="notfications-li"> <a href="#"> <span class="photo"><img alt="avatar" src="img/{0}-proc.jpg"></span> <span class="subject"> <span class="from">{1}</span> </span> <span class="message">Uso muy alto del proc ({3}%).</span> <span class="time">{2}</span> </a> </li>';
    var serverName = _snme;
    if($("#proc").val()){
        if($("#web").is(":checked")){
            createNotification("SERVER - {0}".format(serverName), "img/{0}-proc.jpg".format((procValue >= 75) ? "ui-red" : "ui-orange"), "Uso muy alto del proc ({0}%)".format(procValue));
            if($(".notfications-li").length == 4){
                $(".notfications-li").last().remove(); 
                $("#notification-dropdown > li:nth-child(2)").after(webProcNot.format((procValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], procValue));
            }else{
                $("#notification-dropdown > li:nth-child(2)").after(webProcNot.format((procValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], procValue));
            }
            //Set badges and number values
            $("#badge-count").html($(".notfications-li").length);
            $("#notifications-count").html("Tienes " + $(".notfications-li").length +" notificaciones sin leer");
        }
        if($("#useremail").val().length > 0){
            var tgProcNot = "Servidor - {0}:Uso muy alto del proc ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=email",
                data: "serverid=" + serverid + "&msg=" + tgProcNot.format(serverName, procValue),
                datatype: "text",
                success: function(data, status){
                    
                    //add function to show <new server> (all)
                }
            });
        }
        if($("#tgusername").val().length > 0){
            var tgProcNot = "Servidor - {0}: Uso muy alto del proc ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=tg",
                data: "serverid=" + serverid + "&msg=" + tgProcNot.format(serverName, procValue),
                datatype: "text",
                success: function(data, status){
                    var response = $.parseJSON(data);
                    if(parseInt(response[0]) == 2){
                        showUserInfoAlert(response[0], response[1]);
                    }
                    //add function to show <new server> (all)
                }
            });
        }
    }
}

function createRamNotification(ramValue){
    var webRamNot = '<li class="notfications-li"> <a href="#"> <span class="photo"><img alt="avatar" src="img/{0}-ram.jpg"></span> <span class="subject"> <span class="from">{1}</span> </span> <span class="message">Uso muy alto de la ram ({3}%).</span> <span class="time">{2}</span> </a> </li>';
    var serverName = _snme;
    if($("#ram").val()){
        if($("#web").is(":checked")){
            createNotification("SERVER - {0}".format(serverName), "img/{0}-ram.jpg".format((ramValue >= 75) ? "ui-red" : "ui-orange"), "Uso muy alto de la ram ({0}%)".format(ramValue));
            if($(".notfications-li").length == 4){
               $(".notfications-li").last().remove(); 
                $("#notification-dropdown > li:nth-child(2)").after(webRamNot.format((ramValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], ramValue));
            }else{
                $("#notification-dropdown > li:nth-child(2)").after(webRamNot.format((ramValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], ramValue));
            }
            //Set badges and number values
            $("#badge-count").html($(".notfications-li").length);
            $("#notifications-count").html("Tienes " + $(".notfications-li").length +" notificaciones sin leer");
        }
        if($("#useremail").val().length > 0){
            var tgRamNot = "Servidor - {0}:Uso muy alto de la ram ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=email",
                data: "serverid=" + serverid + "&msg=" + tgRamNot.format(serverName, ramValue),
                datatype: "text",
                success: function(data, status){
                    
                    //add function to show <new server> (all)
                }
            });
        }
        if($("#tgusername").val().length > 0){
            var tgRamNot = "Servidor - {0}: Uso muy alto de la ram ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=tg",
                data: "serverid=" + serverid + "&msg=" + tgRamNot.format(serverName, ramValue),
                datatype: "text",
                success: function(data, status){
                    var response = $.parseJSON(data);
                    if(parseInt(response[0]) == 2){
                        showUserInfoAlert(response[0], response[1]);
                    }
                    //add function to show <new server> (all)
                }
            });
        }
    }
}

function createHddNotification(hddValue){
    var webHddNot = '<li class="notfications-li"> <a href="#"> <span class="photo"><img alt="avatar" src="img/{0}-hdd.jpg"></span> <span class="subject"> <span class="from">{1}</span> </span> <span class="message">Uso muy alto del disco duro ({3}%).</span> <span class="time">{2}</span> </a> </li>';
    var serverName = _snme;
    if($("#hdd").val()){
        if($("#web").is(":checked")){
            createNotification("SERVER - {0}".format(serverName), "img/{0}-hdd.jpg".format((hddValue >= 75) ? "ui-red" : "ui-orange"), "Uso muy alto del hdd ({0}%)".format(hddValue));
            if($(".notfications-li").length == 4){
               $(".notfications-li").last().remove(); 
                $("#notification-dropdown > li:nth-child(2)").after(webHddNot.format((hddValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], hddValue));
            }else{
                $("#notification-dropdown > li:nth-child(2)").after(webHddNot.format((hddValue >= 75) ? "ui-red" : "ui-orange", "SERVER - " + serverName, new Date().toISOString().split('T')[1].split('.')[0], hddValue));
            }
            //Set badges and number values
            $("#badge-count").html($(".notfications-li").length);
            $("#notifications-count").html("Tienes " + $(".notfications-li").length +" notificaciones sin leer");
        }
        if($("#useremail").val().length > 0){
            var tgHddNot = "Servidor - {0}:Uso muy alto de la ram ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=email",
                data: "serverid=" + serverid + "&msg=" + tgHddNot.format(serverName, hddValue),
                datatype: "text",
                success: function(data, status){
                    
                    //add function to show <new server> (all)
                }
            });
        }
        if($("#tgusername").val().length > 0){
            var tgHddNot = "Servidor - {0}: Uso muy alto de la ram ({1}%).";
            var serverid = $('.active').attr("serverid");
            $.ajax({
                type: "POST",
                url: "lib/info/notify?mode=tg",
                data: "serverid=" + serverid + "&msg=" + tgHddNot.format(serverName, hddValue),
                datatype: "text",
                success: function(data, status){
                    var response = $.parseJSON(data);
                    if(parseInt(response[0]) == 2){
                        showUserInfoAlert(response[0], response[1]);
                    }
                    //add function to show <new server> (all)
                }
            });
        }
    }
}
/*
* Limpia los campos de un formulario
*
* @author IDC
* @param <fomrId> Id del formulario a limpiar
*
*/
function clearForm(fomrId){
	for(var i = 0; i < $("#" + fomrId + " > input").length; i++){
		$("#" + fomrId + " > input:nth-child(" + i + ")").val('');
	}
    $("#alias").val('');
}

function loadMoreHddInfo(totalHdd, usedHdd){
	$("#hdd-more-chart-used").html(" {0}/{1} {2}");
    $("#hdd-more-chart-perc").html("{0}% En uso");
	var total = totalHdd;
	var used = usedHdd;
	if($("#morehddstatus")){
		$("#morehddstatus").remove();
	}
	$("#hdd-select-info").after('<canvas class="server-chart" id="morehddstatus" height="120" width="120"></canvas>');
    var hddValues = convertToHuman(totalHdd,usedHdd);
    var hddPercentage = (calculatePercentages(hddValues[0], hddValues[1])) ? calculatePercentages(hddValues[0], hddValues[1]) : 0;
    var hddDoughnutData = [{value: hddPercentage, color: calculateColor(hddPercentage)}, {value: 100-hddPercentage, color:"#444c57"}];
    var hddDoughnut = new Chart(document.getElementById("morehddstatus").getContext("2d")).Doughnut(hddDoughnutData);
    $("#hdd-more-chart-used").html($("#hdd-more-chart-used").html().format(hddValues[1],hddValues[0], hddValues[2]));
    $("#hdd-more-chart-perc").html($("#hdd-more-chart-perc").html().format(hddPercentage));
}

function createNotification(title, icon, msg) {
    if( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
         if (Notification.permission !== "granted")
            Notification.requestPermission();
        else {
            var notification = new Notification(title, {
                icon: icon,
                body: msg,
            });
            setTimeout(function() {
                notification.close();
            }, 3000);
        }
    }
}

function convertToHuman(valueTotal, valueUsed){
    var valTot = valueTotal, valUse = valueUsed, pos = 0, valMin = Math.min(valueTotal, valueUsed);

    var units = ["KiB", "MiB", "GiB", "TiB"];
    do {
        if(valMin >= 1024){
            valMin = (valMin / 1024);
            pos++;
        }
    }while(Math.round(valMin).toString().length >= 4 && Math.round(valMin) >= 1024);
    for(var i = 0; i < pos; i++){
        valTot = (valTot / 1024);
        valUse = (valUse / 1024);
    }
    return [Math.round(valTot), Math.round(valUse), units[pos]];
}

function editServer(serverId){
    var sid = serverId.split('_')[1];
    _sid = sid;
    $(".active").removeClass("active");
    $("a[serverid=" + sid + "]").addClass("active");
    $("#add-edit-server").html("Editar servidor");
    clearForm("add-server");
    $.ajax({
        type: "POST",
        url: "lib/crud/getserver",
        data: "serverid=" + sid,
        datatype: "text",
        success: function(data, status){

            var respnse = $.parseJSON(data);
            if(respnse[0])
                showAlert(respnse[0], respnse[1]);
            $("#ipaddress").val(respnse["ip"]);
            $("#username").val(respnse["user"]);
            $("#alias").val(respnse["sname"]);
            //add function to show <new server> (all)
            $("#add-server-modal").modal('show');
        }
    });

}

function generateMaxColumn(array, startPos, finishPos){
    //Limpiar columnas del grafico de barras
    $("#chart-bar-col-values").html('');
    var auxI = 0, auxV = [];
    for(var i = 8; i <= finishPos; i++)
        auxV[auxV.length] = array[i];
    var graphMax = maxValue = Math.max(...auxV);
    for(var i = 0; i <= Number.MAX_VALUE; i++){
        graphMax += 1;
        if(graphMax % 50 == 0){
            break;
        }
    }
    auxV = Math.round(graphMax/6);
    for(var i = 0; i < 6; i++){
        $("#chart-bar-col-values").append("<li><span>" + graphMax + "</span></li>");
        graphMax -= auxV;
    }
    /*for(var i = graphMax; i >= 0; i-=50){
        auxI++;
        if(auxI <= 6)
            $("#chart-bar-col-values").append("<li><span>" + i + "</span></li>");
    }*/
}
