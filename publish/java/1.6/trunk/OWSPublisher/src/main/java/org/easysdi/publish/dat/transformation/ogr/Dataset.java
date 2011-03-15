package org.easysdi.publish.dat.transformation.ogr;

public class Dataset{
	private String name;
	private String geometry;

	public Dataset(){}
	
	public Dataset(String name, String geometry){
		this.name = name;
		this.geometry = geometry;
	}
	
	public String getName(){
		return this.name;
	}
	
	public String getGeometry(){
		return this.geometry;
	}

	public void setName(String name) {
		this.name = name;
	}

	public void setGeometry(String geometry) {
		this.geometry = geometry;
	}
}
