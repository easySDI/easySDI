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
package org.easysdi.proxy.wmts.v100;

import java.io.IOException;

import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;

/**
 * @author Depth SA
 *
 */
public class WMTSExceptionReport100 extends OWS200ExceptionReport{

	public static final String CODE_TILE_OUT_OF_RANGE 				= "TileOutOfRange";
	public static final String CODE_POINT_IJ_OUT_OF_RANGE 			= "PointIJOutOfRange";
	
}
