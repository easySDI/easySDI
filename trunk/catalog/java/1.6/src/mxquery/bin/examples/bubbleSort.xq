(:
  Shows the while, block and assignment features of the Scripting Facility
  by implementing the bubblesort algorithm
:)

declare variable $data := (5,1,9,5,7,1,7,23,7,22,432,4,2,765,3);
declare variable $len := fn:count($data);
declare variable $changed := fn:true();
while($changed) {
	declare $i := 1;
	declare $data1;
	set $changed := fn:false();
	while ($i lt $len) {
		if ($data[$i] > $data[$i + 1]) then block {
			declare $cur := $data[$i+1];
			set $changed := fn:true();
			set $data1 := fn:insert-before($data,$i,$cur);
			set $data := fn:remove($data1,$i+2);
		} else
			();
		set $i := $i + 1;
	};
};
$data;
