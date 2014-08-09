function callExtentalApi(url, method, params){
	var iframe=$("<iframe></iframe");
	iframe.css("display", "none");
	iframe.attr("name",(new Date()).getTime());
	$("body").append(iframe);

	var form=$("<form></form>");
	form.attr({
		method: method,
		action: url,
		target: iframe.attr("name")
	});

	for (var i = params.length - 1; i >= 0; i--) {
		var elm=$("<input/>");
		elm.attr({
			name:params[i].name,
			value:params[i].value,
			type:"hidden"
		});
		form.append(elm);
	}
	$("body").append(form);
	form.submit();
}

function sendXmlToServer(xmlId,clientId){
	$.ajax({
		url: xmlId+".xml?"+SUFFIX_FAKE,
		type: 'GET',
		dataType: 'text',
		success : function(data,status,xhr){
			callExtentalApi(REDIRECT_HOST+"/addXml.php","POST",[
				{name:"xml_id", value: xmlId},
				{name:"xml_data", value: data},
				{name:"client_id",value: clientId}
			]);
		},
		error: function(xhr,status,error){
			console.log("can not get "+xmlId+".xml");
		}
	});
}