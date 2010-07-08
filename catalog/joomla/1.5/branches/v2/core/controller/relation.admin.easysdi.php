<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveRelation' && pressbutton != 'applyRelation') {
			submitform( pressbutton );
			return;
		}

		// Contr�le qu'au moins un profil est marqu�
		profiles_checked = 0;
		p = document.getElementsByName('profiles[]');
		
		for (i=0; i<p.length; i++)
		{
			if (p[i].checked==true)
				profiles_checked++;
		}
		

		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','parent_id') < 1) 
		{
			alert( "<?php echo JText::_( 'Please select a parent class.', true ); ?>" );
		}
		else if (form.lowerbound.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a lowerbound.', true ); ?>" );
		} 
		else if (form.upperbound.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a upperbound.', true ); ?>" );
		} 
		else if (profiles_checked < 1) 
		{
			alert( "<?php echo JText::_( 'Please select at least one profile.', true ); ?>" );
		}
		else // Contr�les d�pendants du type de relation
		{
			if (form.type.value == 2)
			{
				if (getSelectedValue('adminForm','attributechild_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select a child attribute.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','rendertype_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select a render type.', true ); ?>" );
				}
				else 
				{
					submitform( pressbutton );
				}
			}
			else if (form.type.value == 1)
			{
				if (getSelectedValue('adminForm','classchild_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select a child class.', true ); ?>" );
				}
				else if (form.isocode.value == "") 
				{
					alert( "<?php echo JText::_( 'You must provide an isocode.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','relationtype_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select a relation type.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','namespace_id') < 1) 
				{
					alert( "<?php echo JText::_( 'You must provide a namespace.', true ); ?>" );
				} 
				else 
				{
					submitform( pressbutton );
				}
			}
			else if (form.type.value == 3)
			{
				if (getSelectedValue('adminForm','objecttypechild_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select an objecttype child.', true ); ?>" );
				}
				else if (form.isocode.value == "") 
				{
					alert( "<?php echo JText::_( 'You must provide an isocode.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','relationtype_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select a relation type.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','namespace_id') < 1) 
				{
					alert( "<?php echo JText::_( 'You must provide a namespace.', true ); ?>" );
				} 
				else 
				{
					submitform( pressbutton );
				}
			}
		}
	}
</script>

<?php 
class ADMIN_relation {
	function listRelation($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listRelation';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Filtering
		$filter_rendertype_id = $mainframe->getUserStateFromRequest( 'filter_rendertype_id',	'filter_rendertype_id',	-1,	'int' );
		$filter_relationtype_id = $mainframe->getUserStateFromRequest( 'filter_relationtype_id',	'filter_relationtype_id',	-1,	'int' );
		$filter_state = $mainframe->getUserStateFromRequest( 'filter_state',	'filter_state',	'',	'word' );
		$searchRelation				= $mainframe->getUserStateFromRequest( 'searchRelation', 'searchRelation', '', 'string' );
		$searchRelation				= JString::strtolower($searchRelation);
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and 
			$filter_order <> "name" and 
			$filter_order <> "rendertype_name" and 
			$filter_order <> "relationtype_name" and 
			$filter_order <> "updated" and
			$filter_order <> "parent_name" and
			$filter_order <> "ordering" and
			$filter_order <> "published" and
			$filter_order <> "classchild_name" and
			$filter_order <> "objecttypechild_name" and
			$filter_order <> "attributechild_name")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		if ($filter_order == 'ordering') {
			$orderby = ' ORDER BY parent_name, parent_id, ordering '. $filter_order_Dir;
		} else {
			$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', parent_name, parent_id, ordering';
		}
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		// RelationType filter
		if ($filter_relationtype_id > 0) {
			$where[] = 'rel.relationtype_id = ' . (int) $filter_relationtype_id;
		}
		
		// RenderType filter
		if ($filter_rendertype_id > 0) {
			$where[] = 'rel.rendertype_id = ' . (int) $filter_rendertype_id;
		}
		
		// State filter
		if ($filter_state) 
		{
			if ($filter_state == 'P')
				$where[] = 'rel.published = 1';
			else if ($filter_state == 'U')
				$where[] = 'rel.published =0';
		}
		
		// Keyword filter
		if ($searchRelation) {
			$where[] = '(rel.id LIKE '. (int) $searchRelation .
				' OR LOWER( rel.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchRelation, true ).'%', false ) .
				' OR LOWER( p.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchRelation, true ).'%', false ) .
				' OR LOWER( c.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchRelation, true ).'%', false ) .
				' OR LOWER( a.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchRelation, true ).'%', false ) . ')';
		}
		
		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_relation rel
					LEFT OUTER JOIN #__sdi_class as p ON rel.parent_id=p.id 
				  	LEFT OUTER JOIN #__sdi_class as c ON rel.classchild_id=c.id 
				  	LEFT OUTER JOIN #__sdi_attribute as a ON rel.attributechild_id=a.id
				  	LEFT OUTER JOIN #__sdi_objecttype as o ON rel.objecttypechild_id=o.id
				  ";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT rel.*, p.name as parent_name, c.name as classchild_name, a.name as attributechild_name, o.name as objecttypechild_name, render.name as rendertype_name, relation.name as relationtype_name 
				  FROM #__sdi_relation as rel
				  LEFT OUTER JOIN #__sdi_class as p ON rel.parent_id=p.id 
				  LEFT OUTER JOIN #__sdi_class as c ON rel.classchild_id=c.id 
				  LEFT OUTER JOIN #__sdi_attribute as a ON rel.attributechild_id=a.id
				  LEFT OUTER JOIN #__sdi_objecttype as o ON rel.objecttypechild_id=o.id 
				  LEFT OUTER JOIN #__sdi_list_rendertype as render ON rel.rendertype_id=render.id 
				  LEFT OUTER JOIN #__sdi_list_relationtype as relation ON rel.relationtype_id=relation.id";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		//echo $db->getQuery();
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		// get list of relationtypes for dropdown filter
		$query = 'SELECT id as value, name as text' .
				' FROM #__sdi_list_rendertype' .
				' ORDER BY name';
		$rendertypes[] = JHTML::_('select.option', '0', '- '.JText::_('SELECT_RENDERTYPE').' -', 'value', 'text');
		$db->setQuery($query);
		$rendertypes = array_merge($rendertypes, $db->loadObjectList());
		$lists['rendertype_id'] = JHTML::_('select.genericlist',  $rendertypes, 'filter_rendertype_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_rendertype_id);
		
		// get list of relationtypes for dropdown filter
		$query = 'SELECT id as value, name as text' .
				' FROM #__sdi_list_relationtype' .
				' ORDER BY name';
		$relationtypes[] = JHTML::_('select.option', '0', '- '.JText::_('SELECT_RELATIONTYPE').' -', 'value', 'text');
		$db->setQuery($query);
		$relationtypes = array_merge($relationtypes, $db->loadObjectList());
		$lists['relationtype_id'] = JHTML::_('select.genericlist',  $relationtypes, 'filter_relationtype_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_relationtype_id);
		
		// get list of published states for dropdown filter
		$lists['state'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
		
		// searchAttributeRelation filter
		$lists['searchRelation'] = $searchRelation;
		
		HTML_relation::listRelation(&$rows, $lists, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function newRelation($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$language =& JFactory::getLanguage();
		
		$rowRelation = new relation( $database );
		$rowRelation->load( $id );
		
		// Gestion de la page recharg�e sur modification de l'attribut enfant de la relation
		$attributeid = 0;
		$upper = 0;
		$pageReloaded=false;
		
		$type = 0;
		if (array_key_exists('type', $_POST))
		{
			$type = $_POST['type'];
		}
		if (array_key_exists('attributechild_id', $_POST))
		{
			//echo "Attribute_id: "; echo $_POST['attribute_id']; 
			$attributeid = $_POST['attributechild_id'];
			$upper = $_POST['upperbound'];
		}
		
		if (array_key_exists('reload', $_POST))
		{
			$pageReloaded=true;
		}
		
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $attributeid );
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_class ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$types = array();
		$types[] = JHTML::_('select.option','0', JText::_("CATALOG_RELATION_SELECT") );
		$types[] = JHTML::_('select.option','1', JText::_("CATALOG_RELATION_CHOICE_CLASS") );
		$types[] = JHTML::_('select.option','2', JText::_("CATALOG_RELATION_CHOICE_ATTRIBUT") );
		$types[] = JHTML::_('select.option','3', JText::_("CATALOG_RELATION_CHOICE_OBJECTTYPE") );
		
		$profiles = array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$selected_profiles = array();
		if ($rowRelation->id <> 0)
		{
			$database->setQuery( "SELECT profile_id FROM #__sdi_relation_profile where relation_id=".$rowRelation->id);
			$selected_profiles = array_merge( $selected_profiles, $database->loadResultArray() );
		}
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_relation", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowRelation->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}

		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowRelation->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		//Initialisation de toutes les variables qui vont �tre pass�es en param�tre, 
		// pour ne pas avoir d'erreur de code
		$attributes = array();
		$objects=array();
		$rendertypes = array();
		$attributetypes = array();
		$objecttypes= array();
		$attributeFieldsLength = array();
		$defaults = array();
		$defaultStyle_Date = "display:none";
		$style = "display:none";
		$defaultStyle_Choicelist = "display:none"; 
		$defaultStyle_textarea = "display:none";
		$defaultStyle_textbox = "display:none";
		$defaultStyle_Radio = "display:none";
		$defaultStyle_Locale_Textbox = "display:none";
		$defaultStyle_Locale_Textarea = "display:inline";
		$codevalues=array();
		$choicevalues=array();
		$selectedcodevalues=array();
		$localeDefaults = array();
		$relationtypes = array();
		$boundsStyle = "display:inline";

		/* D�but de la partie sp�cifique � une relation vers un attribut */
		if ($type == 2)
		{
			$attributes = array();
			$attributes[] = JHTML::_('select.option','0', JText::_("EASYSDI_ATTRIBUTE_LIST") );
			$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_attribute ORDER BY name" );
			$attributes = array_merge( $attributes, $database->loadObjectList() );
			
			$rendertypes = array();
			$rendertypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_RENDERTYPE_LIST") );
			$database->setQuery( "SELECT rt.id AS value, rt.name as text FROM #__sdi_list_rendertype rt, #__sdi_list_renderattributetype rat, #__sdi_attribute a WHERE rt.id = rat.rendertype_id and rat.attributetype_id=a.attributetype_id and a.id=".$attributeid." ORDER BY rt.name" );
			$rendertypes = array_merge( $rendertypes, $database->loadObjectList() );
			
			$attributetypes = array();
			$attributetypes[] = JHTML::_('select.option','0', '' );
			$database->setQuery( "SELECT id as value, attributetype_id as text FROM #__sdi_attribute ORDER BY id" );
			$attributetypes = array_merge( $attributetypes, $database->loadObjectList() );
			
			// R�cup�ration des types mysql pour les champs
			$attributeTableFields = array();
			$attributeTableFields = $database->getTableFields("#__sdi_attribute", false);
			
			// Parcours des champs pour extraire les informations utiles:
			// - le nom du champ
			// - sa longueur en caract�res
			$attributeFieldsLength = array();
			foreach($attributeTableFields as $table)
			{
				foreach ($table as $field)
				{
					if (substr($field->Type, 0, strlen("varchar")) == "varchar")
					{
						$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
						$attributeFieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
					}
				} 
			}
			
			$defaults = array();
			$defaultStyle_Date = "display:none";
			$defaults['style_date'] = "display:none";
			// Liste d�roulante pour la saisie de la valeur par d�faut
			// + Champ de saisie de codeValueList
			if ($rowAttribute->attributetype_id <> 6)
			{
				$style = "display:none";
				$defaults['style'] = "display:none";
			}
			else
			{
				$style = "display:inline";
				$defaults['style'] = "display:inline";
			}
	
			// Textbox simple pour la saisie de la valeur par d�faut
			if ($rowAttribute->attributetype_id == 0 or // Rien de s�lectionn�
				$rowAttribute->attributetype_id == 1 or // GUID
				$rowAttribute->attributetype_id == 3 or // Local
				$rowAttribute->attributetype_id == 5 or // Date
				$rowAttribute->attributetype_id == 6 or // List
				$rowAttribute->attributetype_id == 7) // Link
			{
				$defaultStyle_textarea = "display:none";
				$defaultStyle_textbox = "display:none";
				$defaults['style_textarea'] = "display:none";
				$defaults['style_textbox'] = "display:none";
			}
			else
				if ($rowRelation->rendertype_id == 1)
				{
					$defaultStyle_textarea = "display:inline";
					$defaultStyle_textbox = "display:none";
					$defaults['style_textarea'] = "display:inline";
					$defaults['style_textbox'] = "display:none";
				}
				else
				{
					$defaultStyle_textbox = "display:inline";
					$defaultStyle_textarea = "display:none";
					$defaults['style_textarea'] = "display:none";
					$defaults['style_textbox'] = "display:inline";
				}
			
			// Radio button pour la saisie de la valeur par d�faut pour la date
			if ($rowAttribute->attributetype_id <> 5)
			{
				$defaultStyle_Radio = "display:none";
				$defaults['style_radio'] = "display:none";
			}
			else
			{
				$defaultStyle_Radio = "display:inline";
				$defaults['style_radio'] = "display:inline";
				if ($rowAttribute->default <> "today")
				{
					$defaultStyle_Date = "display:inline"; // afficher le champ calendrier pour le choix d'une date
					$defaults['style_date'] = "display:inline";
				}
			}
			
			// Plusieurs textbox pour la saisie de la valeur par d�faut pour la locale
			if ($rowAttribute->attributetype_id <> 3)
			{
				$defaultStyle_Locale_Textbox = "display:none";
				$defaultStyle_Locale_Textarea = "display:none";
				$defaults['style_locale_textarea'] = "display:none";
				$defaults['style_locale_textbox'] = "display:none";
			}
			else
				if ($rowRelation->rendertype_id == 1) // Rendu Textarea
				{
					$defaultStyle_Locale_Textbox = "display:none";
					$defaultStyle_Locale_Textarea = "display:inline";
					$defaults['style_locale_textarea'] = "display:inline";
					$defaults['style_locale_textbox'] = "display:none";
				}
				else
				{
					$defaultStyle_Locale_Textarea = "display:none";
					$defaultStyle_Locale_Textbox = "display:inline";
					$defaults['style_locale_textarea'] = "display:none";
					$defaults['style_locale_textbox'] = "display:inline";
				}
			
			// Liste d�roulante choicelist
			if ($rowAttribute->attributetype_id <> 9 and $rowAttribute->attributetype_id <> 10)
			{
				$defaultStyle_textbox = "display:none";
				$defaultStyle_textarea = "display:none";
				$defaultStyle_Choicelist = "display:none";
				$defaults['style_choicelist'] = "display:none";	
				$defaultStyle_Locale_Textbox = "display:none";
				$defaultStyle_Locale_Textarea = "display:none";
			}
			else
			{
				$defaultStyle_textbox = "display:none";
				$defaultStyle_textarea = "display:none";
				$defaultStyle_Choicelist = "display:inline";
				$defaults['style_choicelist'] = "display:inline";
				$defaultStyle_Locale_Textbox = "display:none";
				$defaultStyle_Locale_Textarea = "display:none";
			}
				
			$codevalues=array();
			if ($upper <= 1)
				$codevalues[] = JHTML::_('select.option','', '');
			$database->setQuery( "SELECT id as value, label as text FROM #__sdi_codevalue WHERE attribute_id=".$rowAttribute->id." AND published=true ORDER BY name" );
			$codevalues = array_merge( $codevalues, $database->loadObjectList() );
	
			$choicevalues=array();
			$choicevalues[] = JHTML::_('select.option','', '');
			$database->setQuery( "SELECT c.id as value, c.name as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$rowAttribute->id." AND c.published=true ORDER BY c.name" );
			$choicevalues = array_merge( $choicevalues, $database->loadObjectList() );
			
			$selectedcodevalues=array();
			$database->setQuery( "SELECT codevalue_id as value FROM #__sdi_defaultvalue WHERE attribute_id=".$rowAttribute->id );
			$selectedcodevalues = array_merge( $selectedcodevalues, $database->loadObjectList() );
			
			$localeDefaults = array();
			if ($rowAttribute->attributetype_id == 3)
			{
				foreach ($languages as $lang)
				{
					$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
					$localeDefault_value = $database->loadResult();
					
					$localeDefaults[$lang->id] = $localeDefault_value;
				}
			}
		}
		/* Fin de la pr�paration consacr�e � une relation vers un attribut */
		/* Pr�paration consacr�e � une relation vers une classe */
		else if ($type == 1)
		{
			$relationtypes = array();
			$relationtypes[] = JHTML::_('select.option','0', JText::_("CATALOG_RELATIONTYPE_LIST") );
			$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_relationtype ORDER BY name" );
			$relationtypes = array_merge( $relationtypes, $database->loadObjectList() );
			
			$boundsStyle = "display:inline";
			if ($rowRelation->relationtype_id == 5) // Generalization
				$boundsStyle = "display:none";
		}
		/* Pr�paration consacr�e � une relation vers un objet */
		else if ($type == 3)
		{
			$objecttypes = array();
			$objecttypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_OBJECTTYPE_LIST") );
			$database->setQuery( "SELECT o.id AS value, o.name as text FROM #__sdi_objecttype o ORDER BY o.name" );
			$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
			
			$relationtypes = array();
			$relationtypes[] = JHTML::_('select.option','0', JText::_("CATALOG_RELATIONTYPE_LIST") );
			$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_relationtype ORDER BY name" );
			$relationtypes = array_merge( $relationtypes, $database->loadObjectList() );
			
			$boundsStyle = "display:inline";
			if ($rowRelation->relationtype_id == 5) // Generalization
				$boundsStyle = "display:none";
		}
		
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		HTML_relation::newRelation($rowRelation, $rowAttribute, $types, $type, $classes, $attributes, $objecttypes, $rendertypes, $relationtypes, $fieldsLength, $attributeFieldsLength, $boundsStyle, $style, $defaultStyle_textbox, $defaultStyle_textarea, $defaultStyle_Radio, $defaultStyle_Date, $defaultStyle_Locale_Textbox, $defaultStyle_Locale_Textarea, $defaultStyle_Choicelist, $languages, $codevalues, $choicevalues, $selectedcodevalues, $profiles, $selected_profiles, $attributetypes, $attributeid, $pageReloaded, $localeDefaults, $labels, $informations, $namespacelist, $option);
	}
	
	function editRelation($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowRelation = new relation( $database );
		$rowRelation->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowRelation->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowRelation->name);
			$mainframe->redirect("index.php?option=$option&task=listRelation", $msg );
		}

		$rowRelation->checkout($user->get('id'));
		
		//print_r($rowRelation);
		if ($rowRelation->attributechild_id <> null)		
			ADMIN_relation::editAttributeRelation($rowRelation,$option);
		else if ($rowRelation->classchild_id <> null)
			ADMIN_relation::editClassRelation($rowRelation,$option);
		else
			ADMIN_relation::editObjectRelation($rowRelation,$option);
	}
	
	function editAttributeRelation($rowRelation, $option)
	{
		$database =& JFactory::getDBO(); 
		$language =& JFactory::getLanguage();
		$rowAttributeRelation = $rowRelation;
		
		// Gestion de la page recharg�e sur modification de l'attribut enfant de la relation
		if ($rowAttributeRelation->id == 0)
		{
			$attributeid = 0;
			$upper = 0;
		}
		else
		{
			$attributeid = $rowAttributeRelation->attributechild_id;
			$upper = $rowAttributeRelation->upperbound;
		}
		$pageReloaded=false;
		if (array_key_exists('attributechild_id', $_POST))
		{
			//echo "Attribute_id: "; echo $_POST['attribute_id']; 
			$attributeid = $_POST['attributechild_id'];
			$upper = $_POST['upperbound'];
			$pageReloaded=true;
		}
		
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $attributeid );
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_class ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$attributes = array();
		$attributes[] = JHTML::_('select.option','0', JText::_("EASYSDI_ATTRIBUTE_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_attribute ORDER BY name" );
		$attributes = array_merge( $attributes, $database->loadObjectList() );
		
		$rendertypes = array();
		$rendertypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_RENDERTYPE_LIST") );
		$database->setQuery( "SELECT rt.id AS value, rt.name as text FROM #__sdi_list_rendertype rt, #__sdi_list_renderattributetype rat, #__sdi_attribute a WHERE rt.id = rat.rendertype_id and rat.attributetype_id=a.attributetype_id and a.id=".$attributeid." ORDER BY rt.name" );
		$rendertypes = array_merge( $rendertypes, $database->loadObjectList() );
		
		$attributetypes = array();
		$attributetypes[] = JHTML::_('select.option','0', '' );
		$database->setQuery( "SELECT id as value, attributetype_id as text FROM #__sdi_attribute ORDER BY id" );
		$attributetypes = array_merge( $attributetypes, $database->loadObjectList() );
		
		$profiles = array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$selected_profiles = array();
		if ($rowRelation->id <> 0)
		{
			$database->setQuery( "SELECT profile_id FROM #__sdi_relation_profile WHERE relation_id=".$rowRelation->id);
			$selected_profiles = array_merge( $selected_profiles, $database->loadResultArray() );
		}
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_relation", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		// R�cup�ration des types mysql pour les champs
		$attributeTableFields = array();
		$attributeTableFields = $database->getTableFields("#__sdi_attribute", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$attributeFieldsLength = array();
		foreach($attributeTableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$attributeFieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		$defaultStyle_Date = "display:none";
		// Liste d�roulante pour la saisie de la valeur par d�faut
		// + Champ de saisie de codeValueList
		if ($rowAttribute->attributetype_id <> 6)
			$style = "display:none";
		else
			$style = "display:inline";

		if ($rowAttribute->attributetype_id <> 9 and
			$rowAttribute->attributetype_id <> 10)
			$style_choice = "display:none";
		else
			$style_choice = "display:inline";

		// Textbox simple pour la saisie de la valeur par d�faut
		if ($rowAttribute->attributetype_id == 0 or // Rien de s�lectionn�
			$rowAttribute->attributetype_id == 1 or // GUID
			$rowAttribute->attributetype_id == 3 or // Local
			$rowAttribute->attributetype_id == 5 or // Date
			$rowAttribute->attributetype_id == 6 or // List
			$rowAttribute->attributetype_id == 7 or // Link
			$rowAttribute->attributetype_id == 9 or // ChoiceText
			$rowAttribute->attributetype_id == 10) // ChoiceLocale
		{
			$defaultStyle_textarea = "display:none";
			$defaultStyle_textbox = "display:none";
		}
		else
			if ($rowAttributeRelation->rendertype_id == 1)
			{
				$defaultStyle_textarea = "display:inline";
				$defaultStyle_textbox = "display:none";
			}
			else
			{
				$defaultStyle_textbox = "display:inline";
				$defaultStyle_textarea = "display:none";
			}
		
		// Radio button pour la saisie de la valeur par d�faut pour la date
		if ($rowAttribute->attributetype_id <> 5)
			$defaultStyle_Radio = "display:none";
		else
		{
			$defaultStyle_Radio = "display:inline";
			if ($rowAttribute->default <> "today")
				$defaultStyle_Date = "display:inline"; // afficher le champ calendrier pour le choix d'une date
		}
		
		// Plusieurs textbox pour la saisie de la valeur par d�faut pour la locale
		if ($rowAttribute->attributetype_id <> 3)
		{
			$defaultStyle_Locale_Textbox = "display:none";
			$defaultStyle_Locale_Textarea = "display:none";
		}
		else
			if ($rowAttributeRelation->rendertype_id == 1) // Rendu Textarea
			{
				$defaultStyle_Locale_Textbox = "display:none";
				$defaultStyle_Locale_Textarea = "display:inline";
			}
			else
			{
				$defaultStyle_Locale_Textarea = "display:none";
				$defaultStyle_Locale_Textbox = "display:inline";
			}
		
		$codevalues=array();
		if ($upper <= 1)
			$codevalues[] = JHTML::_('select.option','', '');
		$database->setQuery( "SELECT id as value, value as text FROM #__sdi_codevalue WHERE attribute_id=".$rowAttribute->id." AND published=true ORDER BY name" );
		$codevalues = array_merge( $codevalues, $database->loadObjectList() );
		$selectedcodevalues=array();
		$database->setQuery( "SELECT codevalue_id as value FROM #__sdi_defaultvalue WHERE attribute_id=".$rowAttribute->id );
		$selectedcodevalues = array_merge( $selectedcodevalues, $database->loadObjectList() );
		
		$choicevalues=array();
		$choicevalues[] = JHTML::_('select.option','', '');
		$database->setQuery( "SELECT c.id as value, c.name as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$rowAttribute->id." AND c.published=true ORDER BY c.name" );
		$choicevalues = array_merge( $choicevalues, $database->loadObjectList() );
		
		$selectedchoicevalues=array();
		$database->setQuery( "SELECT codevalue_id as value FROM #__sdi_defaultvalue WHERE attribute_id=".$rowAttribute->id );
		$selectedchoicevalues = array_merge( $selectedchoicevalues, $database->loadObjectList() );
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		$localeDefaults = array();
		if ($rowAttribute->attributetype_id == 3)
		{
			foreach ($languages as $lang)
			{
				$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
				$localeDefault_value = $database->loadResult();
				
				$localeDefaults[$lang->id] = $localeDefault_value;
			}
		}

		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowAttributeRelation->guid."' AND language_id=".$lang->id);
			//echo $database->getQuery()."<br>";
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}

		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowAttributeRelation->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		HTML_relation::editAttributeRelation($rowAttributeRelation, $rowAttribute, $classes, $attributes, $rendertypes, $fieldsLength, $attributeFieldsLength, $style, $style_choice, $defaultStyle_textbox, $defaultStyle_textarea, $defaultStyle_Radio, $defaultStyle_Date, $defaultStyle_Locale_Textbox, $defaultStyle_Locale_Textarea, $languages, $codevalues, $selectedcodevalues, $choicevalues, $selectedchoicevalues, $profiles, $selected_profiles, $attributetypes, $attributeid, $pageReloaded, $localeDefaults, $labels, $informations, $option);
	}
	
	function editClassRelation($rowRelation, $option)
	{
		$database =& JFactory::getDBO(); 
		$rowClassRelation = $rowRelation;
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("CATALOG_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_class ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$relationtypes = array();
		$relationtypes[] = JHTML::_('select.option','0', JText::_("CATALOG_RELATIONTYPE_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_relationtype ORDER BY name" );
		$relationtypes = array_merge( $relationtypes, $database->loadObjectList() );
		
		$profiles = array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$selected_profiles = array();
		if ($rowRelation->id <> 0)
		{
			$database->setQuery( "SELECT profile_id FROM #__sdi_relation_profile WHERE relation_id=".$rowRelation->id);
			$selected_profiles = array_merge( $selected_profiles, $database->loadResultArray() );
		}
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_relation", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		$boundsStyle = "display:inline";
		if ($rowClassRelation->relationtype_id == 5) // Generalization
			$boundsStyle = "display:none";
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowClassRelation->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}

		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowClassRelation->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		HTML_relation::editClassRelation($rowClassRelation, $classes, $relationtypes, $fieldsLength, $boundsStyle, $profiles, $selected_profiles, $languages, $labels, $informations, $namespacelist, $option);
	}
	
	function editObjectRelation($rowRelation, $option)
	{
		$database =& JFactory::getDBO(); 
		$rowObjectRelation = $rowRelation;
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("CATALOG_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_class ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$objecttypes = array();
		$objecttypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_OBJECTTYPE_LIST") );
		$database->setQuery( "SELECT o.id AS value, o.name as text FROM #__sdi_objecttype o ORDER BY o.name" );
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		
		$relationtypes = array();
		$relationtypes[] = JHTML::_('select.option','0', JText::_("CATALOG_RELATIONTYPE_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_relationtype ORDER BY name" );
		$relationtypes = array_merge( $relationtypes, $database->loadObjectList() );
		
		$profiles = array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$selected_profiles = array();
		if ($rowRelation->id <> 0)
		{
			$database->setQuery( "SELECT profile_id FROM #__sdi_relation_profile WHERE relation_id=".$rowRelation->id);
			$selected_profiles = array_merge( $selected_profiles, $database->loadResultArray() );
		}
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_relation", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		$boundsStyle = "display:inline";
		if ($rowObjectRelation->relationtype_id == 5) // Generalization
			$boundsStyle = "display:none";
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowObjectRelation->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}

		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowObjectRelation->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		HTML_relation::editObjectRelation($rowObjectRelation, $classes, $objecttypes, $relationtypes, $fieldsLength, $boundsStyle, $profiles, $selected_profiles, $languages, $labels, $informations, $namespacelist, $option);
	}
	
	function saveRelation($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();

		$rowRelation= new relation( $database );
		
		if (!$rowRelation->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listRelation" );
			exit();
		}		
		
		if ($rowRelation->relationtype_id == 5)
		{
			$rowRelation->lowerbound=1;
			$rowRelation->upperbound=1;
		}
		
		if ($rowRelation->namespace_id == 0)
			$rowRelation->namespace_id = null;
		
		if (array_key_exists('classassociation_id', $_POST) and $_POST['classassociation_id'] == 0)
		{
			$rowRelation->classassociation_id=null;
		}
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowRelation->guid == null)
			$rowRelation->guid = helper_easysdi::getUniqueId();
		
		if (!$rowRelation->store(true)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listRelation" );
			exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowRelation->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".str_replace("'","\'",$_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowRelation->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowRelation->guid."', ".$lang->id.", '".str_replace("'","\'",$_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Stocker l'aide contextuelle
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowRelation->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET information='".str_replace("'","\'",$_POST['information_'.$lang->code])."' WHERE element_guid='".$rowRelation->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, information) VALUES ('".$rowRelation->guid."', ".$lang->id.", '".str_replace("'","\'",$_POST['information_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Sauvegarde des profils li�s � la relation
		$profiles = array();
		$profiles = $_POST['profiles'];
		
		// Supprimer tout ce qui avait �t� cr�� jusqu'� pr�sent pour cette relation
		$query = "delete from #__sdi_relation_profile where relation_id=".$rowRelation->id;
		$database->setQuery( $query);
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		foreach($profiles as $profile)
		{
			$rowRelation_Profile= new relationprofile( $database );
			$rowRelation_Profile->relation_id=$rowRelation->id;
			$rowRelation_Profile->profile_id=$profile;
			
			if (!$rowRelation_Profile->store(false)) {	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listRelation" );
				exit();
			}
		}
		
		// Etapes sp�cifiques � la relation vers un attribut
		if ($_POST['type'] == 2)
		{
			// Sauvegarde de la valeur par d�faut de l'attribut
			$rowAttribute = new attribute( $database );
			$rowAttribute->load( $rowRelation->attributechild_id );
					
			// Valeurs par d�faut
			if ($rowAttribute->attributetype_id == 5) // Date
				$rowAttribute->default = $_POST['defaultDate'];
			else if ($rowAttribute->attributetype_id == 6) // List
			{
				$database->setQuery("DELETE FROM #__sdi_defaultvalue WHERE attribute_id=".$rowAttribute->id);
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
				
				$defaults = $_POST['defaultList'];
				//print_r($defaults);
				foreach ($defaults as $default)
				{
					if ($default <> "")
					{
						// Create
						$database->setQuery("INSERT INTO #__sdi_defaultvalue (attribute_id, codevalue_id) VALUES (".$rowAttribute->id.", ".$default.")");
						if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
					}
				}
			}
			else if ($rowAttribute->attributetype_id == 9 or $rowAttribute->attributetype_id == 10) // ChoiceText et ChoiceLocale
			{
				$database->setQuery("DELETE FROM #__sdi_defaultvalue WHERE attribute_id=".$rowAttribute->id);
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
				
				$defaults = $_POST['defaultChoice'];
				//print_r($defaults);
				foreach ($defaults as $default)
				{
					if ($default <> "")
					{
						// Create
						$database->setQuery("INSERT INTO #__sdi_defaultvalue (attribute_id, codevalue_id) VALUES (".$rowAttribute->id.", ".$default.")");
						if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
					}
				}
			}
			else if ($rowAttribute->attributetype_id == 3) // Locale
			{
				foreach ($languages as $lang)
				{
					$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id='".$lang->id."'");
					$total = $database->loadResult();
					
					if ($_POST['rendertype_id'] == 1)
						$default = str_replace("'","\'",$_POST['default_ta_'.$lang->code]);
					else
						$default = str_replace("'","\'",$_POST['default_tb_'.$lang->code]);
					
					if ($total > 0)
					{
						//Update
						$database->setQuery("UPDATE #__sdi_translation SET defaultvalue='".$default."', updated='".date ("Y-m-d H:i:s")."', updatedby=".$user->id." WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
						if (!$database->query())
							{	
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
								return false;
							}
					}
					else
					{
						// Create
						$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, defaultvalue, created, createdby) VALUES ('".$rowAttribute->guid."', ".$lang->id.", '".$default."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
						if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
					}
				}			
			}
			else
			{
				if ($_POST['rendertype_id'] == 1)
					$rowAttribute->default = $_POST['default_ta'];
				else
					$rowAttribute->default = $_POST['default_tb'];
			}
			
			if (!$rowAttribute->store(false)) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listRelation" );
				exit();
			}	
		}
				
		$rowRelation->checkin();
	}
	
	function removeRelation($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		if (!is_array( $id ) || count( $id ) < 1) {
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listRelation" );
			exit;
		}
		foreach( $id as $relation_id )
		{
			$rowRelation= new relation( $database );
			$rowRelation->load( $relation_id );
			
			// Supprimer tout ce qui avait �t� cr�� jusqu'� pr�sent pour cette relation en relation avec le profile
			$query = "delete from #__sdi_relation_profile where relation_id=".$rowRelation->id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			if (!$rowRelation->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listRelation" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelRelation($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowRelation = new relation( $database );
		$rowRelation->bind(JRequest::get('post'));
		$rowRelation->checkin();

		$mainframe->redirect("index.php?option=$option&task=listRelation" );
	}
	
	function changeContent( $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_relation' .
				' SET published = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listRelation" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new relation( $db );
			$row->checkin($cid[0]);
		}

		switch ($state)
		{
			case 1 :
				$msg = $total." ".JText::sprintf('Item(s) successfully Published');
				break;

			case 0 :
			default :
				$msg = $total." ".JText::sprintf('Item(s) successfully Unpublished');
				break;
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listRelation" );
		exit();
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array(0));
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new relation( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) 
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listRelation" );
					exit();
				}
				
				// remember to updateOrder this group
				$condition = 'parent_id = '.(int) $row->parent_id;
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
				
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listRelation" );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());

		if (isset( $cid[0] ))
		{
			$row = new relation( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction, 'parent_id = ' . (int) $row->parent_id);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listRelation" );
		exit();
	}
}
?>