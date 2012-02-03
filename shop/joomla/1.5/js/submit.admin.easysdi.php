<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>
		
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			var text = '';
			var index = 0;
			if (pressbutton == "deleteBasemap")
			{
					var answer = confirm  ('<?php echo JText::_("SHOP_BASEMAP_MESSAGE_DELETE_CONTENT");?>' );
					if (answer)
						submitform( pressbutton );
					else
						return;
			}
			if (pressbutton == "deleteProperties")
			{
					var answer = confirm  ('<?php echo JText::_("SHOP_PROPERTY_MESSAGE_DELETE_VALUE");?>' );
					if (answer)
						submitform( pressbutton );
					else
						return;
			}
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
				if ( form.elements['available'].value == '0' && form.elements['surfacemin'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMIN");?>"; 
					index = 1;	
				}
				if (form.elements['available'].value == '0' && form.elements['surfacemax'].value == '')
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMAX");?>";
					index = 1;	
				}
				if (form.elements['available'].value == '1' && form.elements['productfile'].value == '' && form.elements['pathfile'].value == '' && form.elements['productFileName'].value == '' )
				{
					if(index != 0)text += ", ";
					text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_PRODUCT_FILE");?>";
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

		function enableScale(enableScale){
			if(enableScale){
				document.getElementById('minresolution').disabled = false;
				document.getElementById('maxresolution').disabled = false;
				document.getElementById('restrictedscales').disabled = false;
				document.getElementById('minresol').disabled = true;
				document.getElementById('maxresol').disabled = true;
				document.getElementById('restrictedresol').disabled = true;
				document.getElementById('minresol').value = null;
				document.getElementById('maxresol').value = null;
				document.getElementById('restrictedresol').value = null;
			}else{
				document.getElementById('minresolution').disabled = true;
				document.getElementById('maxresolution').disabled = true;
				document.getElementById('restrictedscales').disabled = true;
				document.getElementById('minresolution').value = null;
				document.getElementById('maxresolution').value = null;
				document.getElementById('restrictedscales').value = null;
				document.getElementById('minresol').disabled = false;
				document.getElementById('maxresol').disabled = false;
				document.getElementById('restrictedresol').disabled = false;
			}
		}
		
</script>