<?php

Class Chart extends CFormModel
{
	public $image_map;

	function drawBarChart($im, $passthru, $shape, $row, $col, $x1, $y1, $x2, $y2)
	{
		# Title, also tool-tip text:
		$title = "Group $row, Bar $col";
		# Required alt-text:
		$alt = "Region for group $row, bar $col";
		# Link URL, for demonstration only:
		$onclick = "javascript:alert('($row, $col)')";
		# Convert coordinates to integers:
		$coords = sprintf("%d,%d,%d,%d", $x1, $y1, $x2, $y2);
		# Append the record for this data point shape to the image map string:
		$this->image_map .= "  <area shape=\"circ\" coords=\"$coords\""
				   .  " title=\"$title\" alt=\"$alt\" onclick=\"$onclick\">\n";
	}

	function get_label($value, $labels)
	{
    		if (isset($labels[(int)$value])) return $labels[(int)$value];
    		return $value;
	}
}