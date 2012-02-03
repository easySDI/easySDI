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
package org.easysdi.proxy.core;

/**
 * @author DEPTH SA
 *
 */
public class ProxyLayer {

    private String alias;
    private String name;
    private String prefix;
    private String prefixedName;
    private String aliasName;

    public ProxyLayer (String requestedLayer)
    {
	if(requestedLayer != null)
	{
	    setAliasName(requestedLayer);

	    if(requestedLayer.contains(":"))
	    {
		this.setPrefix(requestedLayer.substring(0, requestedLayer.indexOf(":")));
		requestedLayer = requestedLayer.substring(requestedLayer.indexOf(":",0)+1);
	    }
	    if(requestedLayer.contains("_"))
	    {
		this.setAlias(requestedLayer.substring(0, requestedLayer.indexOf("_")));
		this.setName(requestedLayer.substring(requestedLayer.indexOf("_",0)+1));
	    }
	    else
	    {
		this.setName(requestedLayer);
	    }
	    if(this.getPrefix() !=null ){
		this.setPrefixedName(this.getPrefix() +":" + this.getName());
	    }else{
		this.setPrefixedName(this.getName());
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
     * @param prefixe the prefixe to set
     */
    public void setPrefix(String prefix) {
	this.prefix = prefix;
    }

    /**
     * @return the prefixe
     */
    public String getPrefix() {
	return prefix;
    }

    /**
     * @param prefixedName the prefixedName to set
     */
    public void setPrefixedName(String prefixedName) {
	this.prefixedName = prefixedName;
    }

    /**
     * @return the prefixedName
     */
    public String getPrefixedName() {
	return prefixedName;
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
