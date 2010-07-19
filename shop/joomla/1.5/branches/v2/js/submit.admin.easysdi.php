<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>
		
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			var text = '';
			var index = 0;
			
			if (pressbutton == "saveBasemap")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				} 
				if (form.elements['projection'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_PROJECTION");?>"; 
					index = 1;	
				}
				if (form.elements['minresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MINRESOLUTION");?>"; 
					index = 1;	
				}
				if (form.elements['maxresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXRESOLUTION");?>";
					index = 1;	
				}
				if (form.elements['maxextent'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXEXTENT");?>";
					index = 1;	
				}
				if (form.elements['decimalprecision'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_PRECISION");?>";
					index = 1;	
				}
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}
				else
				{
					submitform( pressbutton );
				}
			}
			
			if (pressbutton == "saveBasemapContent")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				} 
				if (form.elements['projection'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_PROJECTION");?>"; 
					index = 1;	
				}
				if (form.elements['minresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MINRESOLUTION");?>"; 
					index = 1;	
				}
				if (form.elements['maxresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXRESOLUTION");?>";
					index = 1;	
				}
				if (form.elements['maxextent'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXEXTENT");?>";
					index = 1;	
				}
				if (form.elements['url'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URL");?>";
					index = 1;	
				}
				if (form.elements['layers'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_LAYERS");?>";
					index = 1;	
				}
				if (form.elements['imgformat'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_IMGFORMAT");?>";
					index = 1;	
				}	
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}	
				else
				{
					submitform( pressbutton );
				}	

				
			}
			if (pressbutton == "saveLocation")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				} 
				if (form.elements['urlwfs'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URLWFS");?>"; 
					index = 1;	
				}
				if (form.elements['featuretype'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FEATURETYPE");?>"; 
					index = 1;	
				}
				if (form.elements['fieldname'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDNAME");?>";
					index = 1;	
				}
				if (form.elements['fieldid'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDID");?>";
					index = 1;	
				}
				if (form.elements['maxfeatures'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXFEATURES");?>";
					index = 1;	
				}
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}	
				else
				{
					submitform( pressbutton );
				}	
			}
			if (pressbutton == "savePerimeter")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				} 
				if (form.elements['urlwfs'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URLWFS");?>"; 
					index = 1;	
				}
				if (form.elements['featuretype'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FEATURETYPE");?>"; 
					index = 1;	
				}
				if (form.elements['urlwms'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_URLWMS");?>"; 
					index = 1;	
				}
				if (form.elements['minresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MINRESOLUTION");?>"; 
					index = 1;	
				}
				if (form.elements['maxresolution'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_MAXRESOLUTION");?>";
					index = 1;	
				}
				if (form.elements['imgformat'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_IMGFORMAT");?>";
					index = 1;	
				}
				if (form.elements['layername'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_LAYERNAME");?>";
					index = 1;	
				}
				if (form.elements['fieldarea'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDAREA");?>";
					index = 1;	
				}
				if (form.elements['fieldname'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDNAME");?>";
					index = 1;	
				}
				if (form.elements['fieldid'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_FIELDID");?>";
					index = 1;	
				}
				if (form.elements['code'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_CODE");?>";
					index = 1;	
				}
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> :  "+text);
					return;
				}	
				else
				{
					submitform( pressbutton );
				}	
			}
			if (pressbutton == "saveProduct")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				} 
				if (form.elements['objectversion_id'].value == '0')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_VERSION");?>"; 
					index = 1;	
				}
				if (form.elements['diffusion_id'].value == '0')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_DIFFUSION");?>"; 
					index = 1;	
				}
				if (form.elements['surfacemin'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMIN");?>"; 
					index = 1;	
				}
				if (form.elements['surfacemax'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMAX");?>";
					index = 1;	
				}
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}
				else
				{
					submitform( pressbutton );
				}
			}
			if (pressbutton == "saveProperties" || pressbutton == "savePropertiesValues")
			{
				if (   form.elements['name'].value == '')
				{
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
					index = 1;			
				}
				if(index ==1)
				{
					text += ".";
					alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
					return;
				}
				else
				{
					submitform( pressbutton );
				}
			}
			
			submitform( pressbutton );
			
		}
		
</script>