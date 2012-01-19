import module namespace my1 = "http://www.shapes.com" at "schema-mod.xq";

import schema namespace shapes =  "http://www.shapes.com" at "schema.xsd";
import schema namespace geom = "http://www.mygeomelements.com" at "schema2.xsd";

let $a as shapes:myInteger2 := 1
let $b as geom:point := <q/>
return 1 eq 1
