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
package org.easysdi.proxy.wmts;

/**
 * @author DEPTH SA
 *
 */
public class WMTSProxyTileMatrixSet {

    /**
     * 
     */
    private String alias;
    /**
     * 
     */
    private String name;
    /**
     * 
     */
    private String aliasName;
    
    public WMTSProxyTileMatrixSet (String requestedTileMatrixSet)
    {
	if(requestedTileMatrixSet != null)
	{
	    setAliasName(requestedTileMatrixSet);
	    if(requestedTileMatrixSet.contains("_"))
	    {
		this.setAlias(requestedTileMatrixSet.substring(0, requestedTileMatrixSet.indexOf("_")));
		this.setName(requestedTileMatrixSet.substring(requestedTileMatrixSet.indexOf("_",0)+1));
	    }
	    else
	    {
		this.setName(requestedTileMatrixSet);
	    }
	}
    }

    /**
     * @param alias the alias to set
     */
    public void setAlias(String alias) {
	this.alias = alias;
    }

    /**
     * @return the alias
     */
    public String getAlias() {
	return alias;
    }

    /**
     * @param name the name to set
     */
    public void setName(String name) {
	this.name = name;
    }

    /**
     * @return the name
     */
    public String getName() {
	return name;
    }

    /**
     * @param aliasName the aliasName to set
     */
    public void setAliasName(String aliasName) {
	this.aliasName = aliasName;
    }

    /**
     * @return the aliasName
     */
    public String getAliasName() {
	return aliasName;
    }

}
