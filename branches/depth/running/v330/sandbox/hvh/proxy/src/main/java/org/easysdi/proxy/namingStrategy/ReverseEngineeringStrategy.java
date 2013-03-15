package org.easysdi.proxy.namingStrategy;

import org.hibernate.cfg.reveng.DelegatingReverseEngineeringStrategy;
import org.hibernate.cfg.reveng.TableIdentifier;

public class ReverseEngineeringStrategy extends DelegatingReverseEngineeringStrategy {

	public ReverseEngineeringStrategy(org.hibernate.cfg.reveng.ReverseEngineeringStrategy delegate) {
		super(delegate);
	}

	@Override
	public String tableToClassName(TableIdentifier tableIdentifier) {
		String className = super.tableToClassName(tableIdentifier);
		
		return className.replace("Proxy", "");
	}
	
	
}
