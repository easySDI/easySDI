INSERT INTO `#__sdi_sys_propertytype` (ordering,state,value) 
VALUES 
(1,1,'list'),
(2,1,'multiplelist'),
(3,1,'checkbox'),
(4,1,'text'),
(5,1,'textarea'),
(6,1,'message')
;

INSERT INTO `#__sdi_sys_servicetype` (ordering,state,value) 
VALUES 
(1,1,'physical'),
(2,1,'virtual')
;

INSERT INTO `#__sdi_perimeter` (id,guid,alias,created_by,created, ordering, state, name, description, accessscope_id, perimetertype_id ) 
VALUES 
('1', '1a9f342c-bb1e-9bc4-dd19-38910dff0f59', 'freeperimeter', '356', '2013-07-23 09:16:11','1', '1', 'Free perimeter', '',1,1),
('2', '9adc6d4e-262a-d6e4-e152-6de437ba80ed', 'myperimeter', '356', '2013-07-23 09:16:11','2', '1', 'My perimeter', '',1,1)
;