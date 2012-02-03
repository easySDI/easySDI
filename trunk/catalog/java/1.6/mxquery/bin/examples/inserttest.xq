(: Insert a node with the current date/time into an existing file
 Demo for the update facility and the file updates :)

insert node <NEW>{fn:current-dateTime()}</NEW> into doc('inserttest.xml')/doc
