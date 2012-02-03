(: DESCIPTION: Iterates with a FLWOR expression through 'user_tuple' nodes of an imported XML-file and searches for each user its items. :)

for $user in doc('users.xml')/users/user_tuple
where $user/rating eq 'B'
return 
	let $items := doc('items.xml')/items/item_tuple[offered_by eq $user/userid]
	return
		<user>
			{$user/name}
			<items>{$items/description}</items>
		</user>