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
	
	loadHierarchyData();
	loadMeasureData();
	showDimensions();
	loadViewModeData();
}

function loadHierarchyData()
{
	if(window.sessionStorage)
	{	
		if(!sessionStorage.rowParentId)
		{
			sessionStorage.setItem('rowParentId', 0);
		}
		if(!sessionStorage.rowDistanceLevel)
		{
			sessionStorage.setItem('rowDistanceLevel', 0);
		}
		
		if(!sessionStorage.columnParentId)
		{
			sessionStorage.setItem('columnParentId', 0);
		}
		if(!sessionStorage.columnDistanceLevel)
		{
			sessionStorage.setItem('columnDistanceLevel', 0);
		}
	}
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
	var rows =  document.forms.row_dimension_form.elements;
	var columns =  document.forms.column_dimension_form.elements;
	
	if(window.sessionStorage)
	{
		if(sessionStorage.rowName)
		{
			rowName = sessionStorage.getItem('rowName').split(',');
			for(var i=0; i<rows.length; i++)
			{
				for(var j=0; j<rowName.length; j++)
				{
					if(rows[i].value == rowName[j])
					{
						rows[i].checked = true;
						break;
					}
				}
			}
		}
		
		if(sessionStorage.columnName)
		{
			columnName = sessionStorage.getItem('columnName').split(',');
			for(var i=0; i<columns.length; i++)
			{
				for(var j=0; j<columnName.length; j++)
				{
					if(columns[i].value == columnName[j])
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
	var rows =  document.forms.row_dimension_form.elements;
	var columns =  document.forms.column_dimension_form.elements;
	var row_name = new Array(), column_name = new Array();
	var column_filter = '', row_filter = '';
	var j;
	
	j = 0;
	for(var i=0; i<rows.length; i++)
	{
		if(rows[i].checked)
		{
			row_filter = row_filter+'<button onclick="rowFilter(this)">'+(rows[i].value)+'</button>';
			row_name[j++] = rows[i].value;
		}
	}
	if(window.sessionStorage)
	{
		sessionStorage.setItem('rowName', row_name);
	}
	
	j = 0;
	for(var i=0; i<columns.length; i++)
	{
		if(columns[i].checked)
		{
			column_filter = column_filter+'<button onclick="columnFilter(this)">'+(columns[i].value)+'</button>';
			column_name[j++] = columns[i].value
		}
	}
	if(window.sessionStorage)
	{
		sessionStorage.setItem('columnName', column_name);
	}
	
	var row_filter_container = document.getElementById('row-filter-buttons');
	var column_filter_container = document.getElementById('column-filter-buttons');
	
	row_filter_container.innerHTML = row_filter;
	column_filter_container.innerHTML = column_filter;
}

function rowFilter(rowname)
{
	var row_name = rowname.innerHTML.toLowerCase();
	while(row_name.search(/\s+/) != -1)
	{
		row_name = row_name.replace(/\s+/, "_");
	}
	var row_value = document.getElementById(row_name+'_container');
	row_value.style.display = "block";
}

function rowFilterButton(rowFilter)
{
	rowFilter.style.display = "none";
}

function showDimensions()
{
	var form = document.forms.namedItem('getDimensionsForm');
	var action = form.getAttribute('action');
	var formData = new FormData(form);
	
	formData.append('isAjax', 1);
	formData.append('rowParentId', sessionStorage.rowParentId);
	formData.append('rowDistanceLevel', sessionStorage.rowDistanceLevel);
	formData.append('columnParentId', sessionStorage.columnParentId);
	formData.append('columnDistanceLevel', sessionStorage.columnDistanceLevel);
	
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

function queryData()
{

}

window.onload = function ()
{
	init();
}