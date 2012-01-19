<?php
 /**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community 
 * For more information : www.easysdi.org
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
 
require_once( JPATH_COMPONENT.DS.'lang'.DS.'lang.php' );

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<div id="tabsContainer"></div>
<table id="graphTable" style="width: 100%">
 <tr>                                   
  <td style="width: 50%">
   <div id="container1" style="width: 100%"></div>
  </td>
  <td style="width: 50%">
   <div id="container2" style="width: 100%"></div>
  </td>
 </tr>
 <tr>
  <td style="width: 50%">
   <div id="container3" style="width: 100%"></div>
  </td>
  <td style="width: 50%">
   &nbsp;
  </td>
 </tr>
</table>
