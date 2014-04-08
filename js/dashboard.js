//controllers of the dashboard
var measure_btn;
var chart_btn;
var request;

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
	loadViewModeData();
	showDimensions();
}

function loadHierarchyData()
{
	if(window.sessionStorage)
	{	
		if(!sessionStorage.rowParentId)
		{
			sessionStorage.setItem('rowParentId', 0);
		}
		if(!sessionStorage.rowParentValue)
		{
			sessionStorage.setItem('rowParentValue', 'none');
		}
		if(!sessionStorage.rowDistanceLevel)
		{
			sessionStorage.setItem('rowDistanceLevel', 0);
		}
		
		if(!sessionStorage.columnParentId)
		{
			sessionStorage.setItem('columnParentId', 0);
		}
		if(!sessionStorage.columnParentValue)
		{
			sessionStorage.setItem('columnParentValue', 'none');
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
		
		if(sessionStorage.chartCollapse)
		{
			var chartCollapse = sessionStorage.getItem('chartCollapse');
			setChartCollapse(chartCollapse);
		}
		else
		{
			getChartCollapse();
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
			
			column_name[j++] = columns[i].value;
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
	queryData();
}

function rowFilter(rowname)
{
	hideFilters();
	var row_name = rowname.innerHTML.toLowerCase();
	while(row_name.search(/\s+/) != -1)
	{
		row_name = row_name.replace(/\s+/, "_");
	}
	var row_value = document.getElementById(row_name+'_container');
	row_value.style.display = "block";
}

function columnFilter(columnname)
{
	hideFilters();
	var column_name = columnname.innerHTML.toLowerCase();
	while(column_name.search(/\s+/) != -1)
	{
		column_name = column_name.replace(/\s+/, "_");
	}
	var column_value = document.getElementById(column_name+'_container');
	column_value.style.display = "block";
}

function rowFilterButton(rowFilter)
{
	rowFilter.style.display = "none";
	queryData();
}

function columnFilterButton(columnFilter)
{
	columnFilter.style.display = "none";
	queryData();
}

function hideFilters()
{
	var filters = document.getElementsByClassName('filter_container');
	for(var i=0; i<filters.length; i++)
	{
		filters[i].style.display = "none";
	}
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
	
	formData.append('rowParentId', sessionStorage.getItem('rowParentId'));
	formData.append('rowParentValue', sessionStorage.getItem('rowParentValue'));
	
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
			if(request.responseText != 'no-row-drilldown' && request.responseText != 'no-column-drilldown')
			{
				dimensions.innerHTML = request.responseText;
				loadDimensionsData();
				showFilterButtons();
			}
		}
	}
	catch(exception) {
		alert(exception);
	}
}

function rollUp()
{
	var row_distance_level = parseInt(sessionStorage.getItem('rowDistanceLevel'));
	var row_parent_name = "";
	
	if(sessionStorage.rowParentName)
	{
		row_parent_name = sessionStorage.getItem('rowParentName');
	}
	
	if(row_distance_level != 0)
	{
		revertRow();
		showDimensions();
		if(sessionStorage.rowParentName)
		{
			sessionStorage.setItem('rowName', row_parent_name);
			sessionStorage.removeItem('rowParentName');
			loadDimensionsData();
		}
	}
	else
	{
		var message = document.getElementById('main-flash');
		message.innerHTML = "Row already at the top level";
		message.style.display = "block";
		
		$("#main-flash").animate({opacity: 1.0}, 2000).fadeOut("slow");
	}
}

function columnRollUp()
{
	var column_distance_level = parseInt(sessionStorage.getItem('columnDistanceLevel'));
	var column_parent_name = "";
	
	if(sessionStorage.columnParentName)
	{
		column_parent_name = sessionStorage.getItem('columnParentName');
	}
	
	if(column_distance_level != 0)
	{
		revertColumn();
		showDimensions();
		if(sessionStorage.columnParentName)
		{
			sessionStorage.setItem('columnName', column_parent_name);
			sessionStorage.removeItem('columnParentName');
			loadDimensionsData();
		}
	}
	else
	{
		var message = document.getElementById('main-flash');
		message.innerHTML = "Column already at the top level";
		message.style.display = "block";
		
		$("#main-flash").animate({opacity: 1.0}, 2000).fadeOut("slow");
	}
}

function revertRow()
{
	if(window.sessionStorage)
	{
		var row_id = sessionStorage.getItem('rowParentId');
		var row_parent_value = sessionStorage.getItem('rowParentValue');
		var row_distance_level = parseInt(sessionStorage.getItem('rowDistanceLevel'));
		
		row_id = row_id.split(';');
		row_parent_value = row_parent_value.split(';');
		var new_row_id = row_id[0];
		var new_row_parent_value = row_parent_value[0];
		
		for(var i=1; i<row_distance_level; i++)
		{
			new_row_id = new_row_id+';'+row_id[i];
			new_row_parent_value = new_row_parent_value+';'+row_parent_value[i];
		}
		sessionStorage.setItem('rowParentId', new_row_id);
		sessionStorage.setItem('rowParentValue', new_row_parent_value);
		sessionStorage.setItem('rowDistanceLevel', row_distance_level - 1);
	}
}

function revertColumn()
{
	if(window.sessionStorage)
	{
		var column_id = sessionStorage.getItem('columnParentId');
		var column_parent_value = sessionStorage.getItem('columnParentValue');
		var column_distance_level = parseInt(sessionStorage.getItem('columnDistanceLevel'));
		
		column_id = column_id.split(';');
		column_parent_value = column_parent_value.split(';');
		var new_column_id = column_id[0];
		var new_column_parent_value = column_parent_value[0];
		
		for(var i=1; i<column_distance_level; i++)
		{
			new_column_id = new_column_id+';'+column_id[i];
			new_column_parent_value = new_column_parent_value+';'+column_parent_value[i];
		}
		sessionStorage.setItem('columnParentId', new_column_id);
		sessionStorage.setItem('columnParentValue', new_column_parent_value);
		sessionStorage.setItem('columnDistanceLevel', column_distance_level - 1);
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
	queryData();
}

function getViewMode()
{
	for(var i=0; i<chart_btn.length; i++)
	{
		if(chart_btn[i].getAttribute('ischartselected') === 'true')
		{
			return i;
		}
	}
}

function queryData()
{
	var form = document.forms.namedItem('queryDataForm');
	var action = form.getAttribute('action');
	var formData = new FormData();
	var isColumnExist = true, isRowExist = true;
	
	if(sessionStorage.getItem('columnName') === "")
	{
		isColumnExist = false;
	}
	if(sessionStorage.getItem('rowName') === "")
	{
		isRowExist = false;
	}
	
	var chart_container = document.getElementById('chart-container');
	if(isColumnExist == false && isRowExist == true)
	{
		chart_container.innerHTML = '<div id="comment-on-data">Please Select a Column Dimension!</div>';
		return false;
	}
	else if(isColumnExist == true && isRowExist == false)
	{
		chart_container.innerHTML = '<div id="comment-on-data">Please Select a Row Dimension!</div>';
		return false;
	}
	else if(isColumnExist == false && isRowExist == false)
	{
		chart_container.innerHTML = '<div id="comment-on-data">Please Select a Row and Column Dimension!</div>';
		return false;
	}
	
	var rows = sessionStorage.getItem('rowName').toLowerCase();
	while(rows.search(/\s+/) != -1)
	{
		rows = rows.replace(/\s+/, "_");
	}
	rows = rows.split(',');
	
	var item_count, values = new Array(), item;
	var row_value = "";
	for(var i=0; i<rows.length; i++)
	{
		item_count = document.forms.namedItem(rows[i]+'_form').elements;
		var checked = 0;
		for(var j=0; j<item_count.length; j++)
		{
			item = document.forms.namedItem(rows[i]+'_form').elements[j];
			if(item.checked)
			{
				values[checked++] = item.value;
			}
		}
		
		formData.append(rows[i]+'_values', values);
		//row_value = row_value+'<input type="hidden" id="'+rows[i]+'_values" name="'+rows[i]+'_values" value="'+values+'"/>';
	}
	
	var columns = sessionStorage.getItem('columnName').toLowerCase();
	while(columns.search(/\s+/) != -1)
	{
		columns = columns.replace(/\s+/, "_");
	}
	columns = columns.split(',');
	
	var column_sort = "";
	for(var i=0; i<columns.length; i++)
	{
		var item = document.forms.namedItem(columns[i]+'_form').elements;
		var value;
		for(var j=0; j<item.length; j++)
		{
			if(item[j].checked === true)
			{
				value = item[j].value;
				break;
			}
		}
		
		formData.append(columns[i]+'_sort', value);
		//column_sort = column_sort+'<input type="hidden" id="'+columns[i]+'_sort" name="'+columns[i]+'_sort" value="'+value+'"/>';
	}
	
	formData.append('isAjax', 1);
	formData.append('chartCollapse', sessionStorage.getItem('chartCollapse'));
	formData.append('measureId', sessionStorage.getItem('measureId'));
	formData.append('columnName', sessionStorage.getItem('columnName'));
	formData.append('columnDistanceLevel', sessionStorage.getItem('columnDistanceLevel'));
	formData.append('columnParentId', sessionStorage.getItem('columnParentId'));
	formData.append('columnParentValue', sessionStorage.getItem('columnParentValue'));
	formData.append('rowName', sessionStorage.getItem('rowName'));
	formData.append('rowDistanceLevel', sessionStorage.getItem('rowDistanceLevel'));
	formData.append('rowParentId', sessionStorage.getItem('rowParentId'));
	formData.append('rowParentValue', sessionStorage.getItem('rowParentValue'));
	formData.append('viewMode', sessionStorage.getItem('viewMode'));
	
	/*document.getElementById('column_sort_container').innerHTML = column_sort;
	document.getElementById('row_values_container').innerHTML = row_value;
	document.getElementById('chartCollapse').value = sessionStorage.getItem('chartCollapse');
	document.getElementById('measureId').value = sessionStorage.getItem('measureId');
	document.getElementById('columnName').value = sessionStorage.getItem('columnName');
	document.getElementById('columnDistanceLevel').value = sessionStorage.getItem('columnDistanceLevel');
	document.getElementById('columnParentId').value = sessionStorage.getItem('columnParentId');
	document.getElementById('columnParentValue').value = sessionStorage.getItem('columnParentValue');
	document.getElementById('rowName').value = sessionStorage.getItem('rowName');
	document.getElementById('rowDistanceLevel').val = sessionStorage.getItem('rowDistanceLevel');
	document.getElementById('rowParentId').value = sessionStorage.getItem('rowParentId');
	document.getElementById('rowParentValue').value = sessionStorage.getItem('rowParentValue');
	document.getElementById('viewMode').value = sessionStorage.getItem('viewMode');*/
	
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
	request.onprogress = function(e) {showProgress(e)};
	request.onreadystatechange = showQueryData;
	request.send(formData);
}

function showProgress(e)
{
	var overlay_progress = document.getElementById('overlay-progress');
	if(e.lengthComputable)
	{
		overlay_progress.style.display = "block";
		overlay_progress.innerHTML = "Loading ...";
	}
}

function showQueryData()
{
	try {
		if(request.readyState === 4 && request.status === 200)
		{
			var chart_container = document.getElementById('chart-container');
			chart_container.innerHTML = request.responseText;
			
			var overlay_progress = document.getElementById('overlay-progress');
			overlay_progress.style.display = "none";
		}
	}
	catch(exception) {
		alert(exception);
	}
}

function rowDrillDown(row)
{
	if(window.sessionStorage)
	{
		var rowParentId = sessionStorage.getItem('rowParentId');
		var rowParentValue = sessionStorage.getItem('rowParentValue');
		var rowParentName = row.getAttribute('rowname');
		var rowIsTop = row.getAttribute('isTop');
		var rowIsBottom = row.getAttribute('isBottom');
		var rowDistance = row.getAttribute('distance');
		
		if(rowIsBottom != 1)
		{
			rowParentId = rowParentId+';'+row.getAttribute('rowId');
			if(row.getAttribute('type') == 'text')
			{
				rowParentValue = rowParentValue+";'"+row.getAttribute('rowdata')+"'";
			}
			else
			{
				rowParentValue = rowParentValue+";"+row.getAttribute('rowdata');
			}
			console.log('here');
			sessionStorage.setItem('rowParentId', rowParentId);
			sessionStorage.setItem('rowParentName', rowParentName);
			sessionStorage.setItem('rowParentValue', rowParentValue);
			sessionStorage.setItem('rowDistanceLevel', parseInt(row.getAttribute('distance'))+1);
			
			showDimensions();
		}
	}
}

function columnDrillDown(column)
{
	if(window.sessionStorage)
	{	
		var columnParentId = sessionStorage.getItem('columnParentId');
		var columnParentValue = sessionStorage.getItem('columnParentValue');
		var columnParentName = column.getAttribute('columnname');
		var columnIsTop = column.getAttribute('isTop');
		var columnIsBottom = column.getAttribute('isBottom');
		var columnDistance = column.getAttribute('distance');
		
		if(columnIsBottom != 1)
		{
			columnParentId = columnParentId+';'+column.getAttribute('columnId');
			if(column.getAttribute('type') == 'text')
			{
				columnParentValue = columnParentValue+";'"+column.getAttribute('columndata')+"'";
			}
			else
			{
				columnParentValue = columnParentValue+';'+column.getAttribute('columndata');
			}
			
			sessionStorage.setItem('columnParentId', columnParentId);
			sessionStorage.setItem('columnParentName', columnParentName);
			sessionStorage.setItem('columnParentValue', columnParentValue);
			sessionStorage.setItem('columnDistanceLevel', parseInt(column.getAttribute('distance'))+1);
			
			showDimensions();
		}
	}
	
}

function chartCollapse(chart)
{
	if(window.sessionStorage)
	{
		sessionStorage.setItem('chartCollapse', chart.value);
		queryData();
	}
}

function setChartCollapse(chartCollapse)
{
	var collapse = document.forms.chartCollapsable.elements.collapseType;
	
	for(var i=0; i<collapse.length; i++)
	{
		if(collapse[i].value == chartCollapse)
		{
			collapse[i].selected = true;
			break;
		}
	}
}

function getChartCollapse()
{
	var collapse = document.forms.chartCollapsable.elements.collapseType;
	
	for(var i=0; i<collapse.length; i++)
	{
		if(collapse[i].selected === true)
		{
			sessionStorage.setItem('chartCollapse', collapse[i].value);
			break;
		}
	}
}

window.onload = function ()
{
	init();
}