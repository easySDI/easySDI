(: DESCIPTION: Deletes and inserts a node in a transform expression.  :)


	copy $x := <doc><el><node>this node is deleted</node></el></doc> 
	modify 
	(
		delete node $x/el/node, 
		insert node <node>this node is inserted</node> into $x/el
	)
	return $x/el
