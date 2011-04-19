/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */
package ch.ethz.mxquery.opt.expr;

import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Utils;

/**
 * Represents a compare literal in a conjunction term
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class CompareLiteral extends Literal {
	
	public static final int DEPENDENCY_NO_NO = 1;
	public static final int DEPENDENCY_NO_START = 2;
	public static final int DEPENDENCY_NO_STARTEND = 3;
	public static final int DEPENDENCY_NO_END = 4;
	public static final int DEPENDENCY_START_START = 5;
	public static final int DEPENDENCY_START_END = 6;
	public static final int DEPENDENCY_START_STARTEND = 7;
	public static final int DEPENDENCY_END_END = 8;
	public static final int DEPENDENCY_END_STARTEND = 9;
	public static final  int DEPENDENCY_STARTEND_STARTEND = 10;
	
	//The left literal has always the lower dependency level after sorting
	private ValueLiteral leftLiteral;
	private ValueLiteral rightLiteral;
	
	private int comparator;
	
	private int compareType;
	
	private int compareDependency =  -1;

	public CompareLiteral(XDMIterator iter, int comparator, int compareType, ValueLiteral leftLiteral,  ValueLiteral rightLiteral) {
		super(iter);
		this.comparator = comparator;
		this.compareType = compareType;
		this.leftLiteral = leftLiteral;
		this.rightLiteral = rightLiteral;
		if(leftLiteral.compareTo(rightLiteral) > 0){
			ValueLiteral temp = leftLiteral;
			leftLiteral = rightLiteral;
			rightLiteral = temp;
		}
		addDependency(leftLiteral.dependency);
		addDependency(rightLiteral.dependency);
		compareDependency = checkCompareVariableDependency(this);
		if(getCompareDependency() == CompareLiteral.DEPENDENCY_START_END){
			setIndexable(INDEX_RECOMMEND);
		}else{
			setIndexable(leftLiteral.indexable);
			setIndexable(rightLiteral.indexable);
		}
	}

	/**
	 * Returns the comparator id (based on CompareIterator constants)
	 * @return a comparator	 type (see ch.ethz.mxquery.model.Constants)
	 */
	public int getComparator() {
		return comparator;
	}

	/**
	 * Returns the comparator type (value, general, node based on CompareIterator constants)
	 * @return a compare type (see ch.ethz.mxquery.model.Constants)
	 */
	public int getCompareType() {
		return compareType;
	}
	
	/**
	 * Returns the compare dependency
	 * @return one of the dependency types
	 */
	public int getCompareDependency(){
		return compareDependency;
	}

	/**
	 * Returns the left part of the comparison (this part has always a lower dependency than the right part
	 * @return the first/left ValueLiteral
	 */
	public ValueLiteral getLeftLiteral() {
		return leftLiteral;
	}

	/**
	 * Returns the left part of the comparison (this part has always a higher dependency than the right part
	 * @return the second/right ValueLiteral
	 */
	public ValueLiteral getRightLiteral() {
		return rightLiteral;
	}

	/**
	 * Needed to compare literals depending on their dependencies
	 */
	public int compareTo(Object o) {
		int result = super.compareTo(o);
		if(result == 0 && o instanceof CompareLiteral){
			CompareLiteral cL = (CompareLiteral) o;
			int compareType = Utils.compareComparator(this.compareType, cL.compareType);
			if(compareType == 0){
				int compareComparator = Utils.compareComparator(this.comparator, cL.comparator);
				return compareComparator;
			}
			return compareType;
		}
		return result;
	}
	
	
	
	
	public boolean equals(Object obj) {
		if (! (obj instanceof CompareLiteral))
			return false;
		else {
			CompareLiteral peer = (CompareLiteral)obj;
			return peer.leftLiteral.equals(leftLiteral) && 
				peer.rightLiteral.equals(rightLiteral);
		}
	}

	public int hashCode() {
		return leftLiteral.hashCode()+rightLiteral.hashCode();
	}

	/**
	 * Creates a deep copy!
	 */
	public Object clone() {
		CompareLiteral literal =  (CompareLiteral)super.clone ();
		/*literal.leftLiteral = (ValueLiteral)leftLiteral.clone();
		literal.rightLiteral = (ValueLiteral)rightLiteral.clone();*/
		return literal;
	}	
	
	/**
	 * Just for debugging
	 */
	public KXmlSerializer traverse(KXmlSerializer serializer){
		try{
			createStartTag(serializer);
			serializer.attribute(null, "comparator", Constants.getCompareString(comparator, compareType));
			leftLiteral.traverse(serializer);
			rightLiteral.traverse(serializer);
			createEndTag(serializer);
		}catch(Exception err){
			throw new RuntimeException(err);
		}
		return serializer;
	}
	
	public int reset(int level){
		leftLiteral.reset(level);
		rightLiteral.reset(level);
		return super.reset(level);
	}


	/**
	 * Tests the dependency
	 * @param term
	 * @return
	 */
	private static int checkCompareVariableDependency(CompareLiteral term){
		int l = term.leftLiteral.dependency;
		int r = term.rightLiteral.dependency;
		if(l == LogicalUnit.DEPENDENCY_NO && r == LogicalUnit.DEPENDENCY_NO){
			return CompareLiteral.DEPENDENCY_NO_NO;
		}
		if(l == LogicalUnit.DEPENDENCY_NO && r == LogicalUnit.DEPENDENCY_START){
			return CompareLiteral.DEPENDENCY_NO_START;
		}
		if(l == LogicalUnit.DEPENDENCY_NO && r == LogicalUnit.DEPENDENCY_END){
			return CompareLiteral.DEPENDENCY_NO_START;
		}
		if(l == LogicalUnit.DEPENDENCY_NO && r == LogicalUnit.DEPENDENCY_STARTEND){
			return CompareLiteral.DEPENDENCY_NO_STARTEND;
		}
		if(l == LogicalUnit.DEPENDENCY_START && r == LogicalUnit.DEPENDENCY_START){
			return CompareLiteral.DEPENDENCY_START_START;
		}
		if(l == LogicalUnit.DEPENDENCY_START && r == LogicalUnit.DEPENDENCY_END){
			return CompareLiteral.DEPENDENCY_START_END;
		}
		if(l == LogicalUnit.DEPENDENCY_START && r == LogicalUnit.DEPENDENCY_STARTEND){
			return CompareLiteral.DEPENDENCY_START_STARTEND;
		}
		if(l == LogicalUnit.DEPENDENCY_END && r == LogicalUnit.DEPENDENCY_END){
			return CompareLiteral.DEPENDENCY_END_END;
		}
		if(l == LogicalUnit.DEPENDENCY_END && r == LogicalUnit.DEPENDENCY_STARTEND){
			return CompareLiteral.DEPENDENCY_END_STARTEND;
		}
		if(l == LogicalUnit.DEPENDENCY_STARTEND && r == LogicalUnit.DEPENDENCY_STARTEND){
			return CompareLiteral.DEPENDENCY_STARTEND_STARTEND;
		}
		throw new RuntimeException("This should never happend because left and right are ordered!");
	}
	
	public static String getCompareDependencyName(int dep){
		switch(dep){
		case DEPENDENCY_NO_NO:
			return "DEPENDENCY_NO_NO";
		case DEPENDENCY_NO_START:
			return "DEPENDENCY_NO_START";
		case DEPENDENCY_NO_STARTEND:
			return "DEPENDENCY_NO_STARTEND";
		case DEPENDENCY_START_START:
			return "DEPENDENCY_START_START";
		case DEPENDENCY_START_END:
			return "DEPENDENCY_START_END";
		case DEPENDENCY_START_STARTEND:
			return "DEPENDENCY_START_STARTEND";
		case DEPENDENCY_END_END:
			return "DEPENDENCY_END_END";
		case DEPENDENCY_END_STARTEND:
			return "DEPENDENCY_END_STARTEND";
		case DEPENDENCY_STARTEND_STARTEND:
			return "DEPENDENCY_STARTEND_STARTEND";
		}	
		throw new RuntimeException("Unknown type! That should never happen!");
	}



}
