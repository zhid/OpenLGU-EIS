//controllers of the dashboard
var measure_btn;
var chart_btn;
//controllers of the dashboard

//for AJAX
var request;
//for AJAX

function init()
{
	measure_btn = document.getElementById('measure_list');
	measure_btn.addEventListener('change', showDimensions);
	
	chart_btn = document.getElementsByClassName('charts');
	for(var i=0; i<chart_btn.length; i++)
	{
		chart_btn[i].addEventListener('click', viewMode);
	}
	showDimensions();
}

function showDimensions()
{
	var form = document.forms.namedItem('getDimensionsForm');
	var action = form.getAttribute('action');
	var formData = new FormData(form);
	
	formData.append('isAjax', 1)
	
	if(window.XMLHttpRequest)
	{
		request = new XMLHttpRequest();
	}
	else if(window.ActiveXObject)
	{
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(exception) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(exception) {
			}
		}
	}
	
	if(!request)
	{
		alert('Sorry! The Browser cannot create an XMLHttp instance!');
		return false;
	}
	
	request.open("POST", action, true);
	request.onreadystatechange = changeDimensions;
	request.send(formData);
}

function changeDimensions()
{
	try {
		if(request.readyState === 4 && request.status === 200)
		{
			var dimensions = document.getElementById('dimensions');
			dimensions.innerHTML = request.responseText;
		}
	}
	catch(exception) {
		alert('Application Error:' + exception.description);
	}
}

function columnList(column)
{
	alert(column.checked);
}

function viewMode()
{
	//alert(this.getAttribute('type'));
	for(var i=0; i<chart_btn.length; i++)
	{
		chart_btn[i].style.border = '1px solid white';
		chart_btn[i].style.backgroundColor = 'white';
	}
	this.style.border = '1px solid black';
	this.style.backgroundColor = '#e0e0e0';
}

window.onload = function ()
{
	init();
}