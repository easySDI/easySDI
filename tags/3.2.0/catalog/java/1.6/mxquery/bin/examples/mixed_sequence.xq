(: Relaxed update compatibility rules in Scripting
  the "regular" update facility forbids combining updating and simple expressions
  with scripting, this is now possible
:)

declare variable $data := doc('inserttest.xml');

insert node <bla/> into $data/doc,
"Hello" 

