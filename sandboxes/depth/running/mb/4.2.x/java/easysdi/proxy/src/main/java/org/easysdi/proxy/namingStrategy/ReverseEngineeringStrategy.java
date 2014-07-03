package org.easysdi.proxy.namingStrategy;

import org.apache.commons.lang.WordUtils; 
import org.hibernate.cfg.reveng.DelegatingReverseEngineeringStrategy;
import org.hibernate.cfg.reveng.TableIdentifier;

public class ReverseEngineeringStrategy extends DelegatingReverseEngineeringStrategy {

	public ReverseEngineeringStrategy(org.hibernate.cfg.reveng.ReverseEngineeringStrategy delegate) {
		super(delegate);
	}

	@Override
	public String tableToClassName(TableIdentifier tableIdentifier) {
		String className = super.tableToClassName(tableIdentifier);
		
		return className.replace("jos", "");
	}
	
	@Override
	public String columnToPropertyName(TableIdentifier table, String column) {
		return WordUtils.capitalize(column);
	}
	
}
