function preSubmit() {
	document.getElementById('sSuccess').style.display = 'none';
	document.getElementById('sFailure').style.display = 'none';
}

function postSubmit() {
}

function success() {
	document.getElementById('sSuccess').style.display = 'block';
}

function failure() {
	document.getElementById('sFailure').style.display = 'block';
}

function v() {
	var a = new URL(window.location.href).searchParams.get("a");
	if (!a) {
		failure();
		return;
	}
	var u = jwtDecode(a)['target'];
	if (!u) {
		failure();
		return;
	}
	preSubmit();
	$.ajax({ 
		type: "POST",
		dataType: "json",
		url: new URL("../api/v1/user/" + u + "/status", document.baseURI).href,
		headers: {"Authorization": "Bearer " + a},
		success: function(data){
			success();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown + ": " + XMLHttpRequest.responseText);
			failure();
		}
	});
	postSubmit();
}

window.onload = v;

