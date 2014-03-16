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
		chart_btn[i].addEventListener('click', function(){setViewMode(this)});
		if(chart_btn[i].getAttribute('ischartselected') === 'true')
		{
			chart_btn[i].setAttribute('ischartselected', 'true');
			chart_btn[i].style.border = '1px solid black';
			chart_btn[i].style.backgroundColor = '#e0e0e0';
		}
		else
		{
			chart_btn[i].setAttribute('ischartselected', 'false');
			chart_btn[i].style.border = '1px solid white';
			chart_btn[i].style.backgroundColor = 'white';
		}
	}
	if(window.sessionStorage)
	{
		if(sessionStorage.viewMode){}
		else
		{
			sessionStorage.setItem('viewMode', chart_btn[0].getAttribute('type'));
		}
	}
	
	loadMeasureData();
	showDimensions();
	loadViewModeData();
}

function loadMeasureData()
{
	var measureId;

	if(window.sessionStorage)
	{
		if(sessionStorage.measureId)
		{
			measureId = parseInt(sessionStorage.getItem('measureId'));
			setMeasureSelected(measureId);
		}
	}
}

function loadViewModeData()
{
	var viewMode;
	//var chart_btn = document.getElementsByClassName('charts');

	if(window.sessionStorage)
	{
		if(sessionStorage.viewMode)
		{
			viewMode = sessionStorage.getItem('viewMode');
			for(var i=0; i<chart_btn.length; i++)
			{
				if(chart_btn[i].getAttribute('type') === viewMode)
				{
					chart_btn[i].setAttribute('ischartselected', 'true');
					chart_btn[i].style.border = '1px solid black';
					chart_btn[i].style.backgroundColor = '#e0e0e0';
				}
				else
				{
					chart_btn[i].setAttribute('ischartselected', 'false');
					chart_btn[i].style.border = '1px solid white';
					chart_btn[i].style.backgroundColor = 'white';
				}
			}
		}
	}
}

function loadDimensionsData()
{
	var rowName, columnName;
	var rows =  document.forms.row_dimension_form.elements.rows;
	var columns =  document.forms.column_dimension_form.elements.columns;
	
	if(window.sessionStorage)
	{
		if(sessionStorage.rowName)
		{
			rowName = sessionStorage.getItem('rowName');
			if(rows.length !== undefined)
			{
				for(var i=0; i<rows.length; i++)
				{
					if(rows[i].value == rowName)
					{
						rows[i].checked = true;
						break;
					}
				}
			}
		}
		
		if(sessionStorage.columnName)
		{
			columnName = sessionStorage.getItem('columnName');
			if(columns.length !== undefined)
			{
				for(var i=0; i<columns.length; i++)
				{
					if(columns[i].value == columnName)
					{
						columns[i].checked = true;
						break;
					}
				}
			}
		}
	}
}

function getMeasureSelected()
{
	var measure = document.forms.getDimensionsForm.elements.measure_list;
	
	for(var i=0; i<measure.length; i++)
	{
		if(measure[i].selected === true)
		{
			return measure[i].value;
			break;
		}
	}
}

function setMeasureSelected(measureId)
{
	var measure = document.forms.getDimensionsForm.elements.measure_list;
	
	for(var i=0; i<measure.length; i++)
	{
		if(measure[i].value == measureId)
		{
			measure[i].selected = true;
			break;
		}
	}
}

function showFilterButtons()
{
	var rows =  document.forms.row_dimension_form.elements.rows;
	var columns =  document.forms.column_dimension_form.elements.columns;
	var row_name, column_name;
	
	if(rows.length === undefined)
	{
		row_name = document.forms.namedItem('row_dimension_form').rows.value;
	}
	else
	{
		for(var i=0; i<rows.length; i++)
		{
			if(rows[i].checked)
			{
				row_name = rows[i].value;
				break;
			}
		}
	}
	if(window.sessionStorage)
	{
		sessionStorage.setItem('rowName', row_name);
	}
	
	if(columns.length === undefined)
	{
		column_name = document.forms.namedItem('column_dimension_form').columns.value;
	}
	else
	{
		for(var i=0; i<columns.length; i++)
		{
			if(columns[i].checked)
			{
				column_name = columns[i].value;
				break;
			}
		}
	}
	if(window.sessionStorage)
	{
		sessionStorage.setItem('columnName', column_name);
	}
	

	var row_value = row_name.toLowerCase();
	var column_value = column_name.toLowerCase();
	
	var filter = '<button onclick="rowfilter(this,'+row_value.replace(/\s+/, '_')+')">'+row_name+'</button><button onclick="columnfilter(this, '+column_value.replace(/\s+/, '_')+')">'+column_name+'</button>';
	var filter_container = document.getElementById('dashboard-filter-buttons');
	filter_container.innerHTML = filter;
}

function showDimensions()
{
	var form = document.forms.namedItem('getDimensionsForm');
	var action = form.getAttribute('action');
	var formData = new FormData(form);
	
	formData.append('isAjax', 1)
	
	if(window.sessionStorage)
	{
		var measureId = getMeasureSelected();
	
		sessionStorage.setItem('measureId', measureId);
	}
	
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
			loadDimensionsData();
			showFilterButtons();
		}
	}
	catch(exception) {
		alert(exception);
	}
}

function columnList(column)
{
	showFilterButtons();
}

function rowList(row)
{
	showFilterButtons();
}

function setViewMode(viewmode)
{
	for(var i=0; i<chart_btn.length; i++)
	{
		chart_btn[i].setAttribute('ischartselected', 'false');
		chart_btn[i].style.border = '1px solid white';
		chart_btn[i].style.backgroundColor = 'white';
	}
	viewmode.setAttribute('ischartselected', 'true');
	viewmode.style.border = '1px solid black';
	viewmode.style.backgroundColor = '#e0e0e0';
	if(window.sessionStorage)
	{
		sessionStorage.setItem('viewMode', viewmode.getAttribute('type'));
	}
}

function getViewMode()
{
	for(var i=0; i<chart_btn.length; i++)
	{
		if(chart_btn[i].getAttribute('ischartselected') === 'true')
		{
			//return chart_btn[i].getAttribute('type');
			return i;
		}
	}
}

window.onload = function ()
{
	init();
}