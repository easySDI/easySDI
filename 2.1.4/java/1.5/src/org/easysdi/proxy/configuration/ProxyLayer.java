package org.easysdi.proxy.configuration;

public class ProxyLayer {
	
	private String alias;
	private String name;
	
	public ProxyLayer (String requestedLayer)
	{
		if(requestedLayer != null)
		{
			if(requestedLayer.contains("_"))
			{
				this.setAlias(requestedLayer.substring(0, requestedLayer.indexOf("_")));
				this.setName(requestedLayer.substring(requestedLayer.indexOf("_")+1));
			}
			else
			{
				this.setName(requestedLayer);
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

}
