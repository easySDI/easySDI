package ch.ethz.mxquery.sms.MMimpl;

import java.io.Serializable;
import java.util.Comparator;

class ItemIdCompare implements Comparator, Serializable{
	private static final long serialVersionUID = 7061616974352428251L;

	public ItemIdCompare(){}
	
	public int compare(Object o1, Object o2){
		Integer i1 = (Integer)o1;
		Integer i2 = (Integer)o2;
		
		return -(i1.compareTo(i2));
	}
}