function preSubmit() {
	document.getElementById('sSuccess').style.display = 'none';
	document.getElementById('sFailure').style.display = 'none';
}

function postSubmit() {
	document.getElementById('sForm').remove();
}

function success() {
	document.getElementById('sSuccess').style.display = 'block';
}

function failure() {
	document.getElementById('sFailure').style.display = 'block';
}

function r() {
	preSubmit();
	$.ajax({ 
		type: "POST",
		dataType: "json",
		url: new URL("../api/v1/user/" + document.getElementById('tEmail').value + "?" +
				"password=" + document.getElementById('tPassword').value + "&" +
				"alias=" + document.getElementById('tAlias').value + "&" +
				"name_first=" + document.getElementById('tNameFirst').value + "&" +
				"name_last=" + document.getElementById('tNameLast').value + "&" +
				"organization=" + document.getElementById('tOrganization').value,
			document.baseURI
		).href,
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
