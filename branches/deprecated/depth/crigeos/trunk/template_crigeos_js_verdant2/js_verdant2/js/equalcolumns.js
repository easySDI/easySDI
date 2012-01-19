function equal_columns(element) {
	var columns = $$(element);
	var max_height = 0;

	columns.each(function(item) 
	{ max_height = Math.max(max_height, item.getSize().size.y ); });

	columns.setStyle('height', max_height);
}	