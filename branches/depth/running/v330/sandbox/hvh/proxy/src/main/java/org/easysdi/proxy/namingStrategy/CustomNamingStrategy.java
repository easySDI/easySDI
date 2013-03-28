package org.easysdi.proxy.namingStrategy;

import org.hibernate.cfg.ImprovedNamingStrategy;
import org.hibernate.util.StringHelper;

public class CustomNamingStrategy extends ImprovedNamingStrategy {

    private static final long serialVersionUID = 1L;
    private String prefix;

    @Override
    public String classToTableName(final String className) {
        return this.addPrefix(super.classToTableName(className));
    }

    private String addPrefix(final String composedTableName) {
        return prefix + composedTableName;
    }

	public String getPrefix() {
		return prefix;
	}

	public void setPrefix(String prefix) {
		this.prefix = prefix;
	}
    
	@Override
	public String propertyToColumnName(String propertyName) {
		return StringHelper.unqualify(propertyName).toLowerCase() ;
	}
	
	@Override
	public String columnName(String columnName) {
		return columnName.toLowerCase();
	}

}