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

import java.util.List;
import java.util.ArrayList;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.opt.index.IndexSchema;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * This is just a simple helper class to transfer all indexable compare literals to the index interface
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class CompareLiteralIndexSchema extends IndexSchema {

	private List compareLiterals = new ArrayList();
	
	/**
	 * Creates a new IndexSchema for compare literals
	 * @param indexNb Has to be a unique index number
	 */
	public CompareLiteralIndexSchema(int indexNb){
		super(indexNb);
		simpleValueIndex = true;
	}
	
	/**
	 * Registeres a new comparison at the index
	 * @param compareLiteral The compare literal
	 */
	public void registerValue(CompareLiteral compareLiteral){
		if(compareLiteral.getCompareType()  == Constants.COMP_GENERAL){
			simpleValueIndex = false;
		}
		if(compareLiteral.getCompareDependency() != CompareLiteral.DEPENDENCY_START_END){
			throw new RuntimeException("This kind of comparison shouldn't be indexed");
		}
		compareLiterals.add(compareLiteral);
	}
	
	public int size(){
		return compareLiterals.size();
	}
	
	public CompareLiteral getLiteral(int i){
		return (CompareLiteral) compareLiterals.get(i);
	}
	
	public String getColumnName(int i){
		return getLiteral(i).getLeftLiteral().getColumnName();
	}

	public int getComparator(int position) {
		return getLiteral(position).getComparator();
	}

	public int getCompareType(int position) {
		return getLiteral(position).getCompareType();
	}
	
	/**
	 * Allows to reset the expressions which depend an a start variable
	 *
	 */
	public void resetStartPart(){
		for(int i = 0; i < compareLiterals.size();i++){
			ValueLiteral literal = getLiteral(i).getLeftLiteral();
			literal.reset();
		}
	}
	
	/**
	 * Allows to reset the expressions which depend an a end variable
	 *
	 */
	public void resetEndPart(){
		for(int i = 0; i < compareLiterals.size();i++){
			ValueLiteral literal = getLiteral(i).getRightLiteral();
			literal.reset();
		}
	}
	
	public Token[] getStartTokens() throws MXQueryException{
		Token[] tokens = new Token[compareLiterals.size()];
		for(int i = 0; i < tokens.length;i++){
			ValueLiteral literal = getLiteral(i).getLeftLiteral();
			literal.evaluateExpectOneValue();
			tokens[i] = literal.getToken();
		}
		return tokens;
	}
	
	public Token[][] getStartValues() throws MXQueryException{
		Token[][] values = new Token[compareLiterals.size()][];
		for(int i = 0; i < compareLiterals.size();i++){
			ValueLiteral literal = getLiteral(i).getLeftLiteral();
			literal.evaluate(); 
			values[i] = literal.getValues();
		}
		return values;
	}

	public Token[] getEndTokens() throws MXQueryException{
		Token[] tokens = new Token[compareLiterals.size()];
		for(int i = 0; i < tokens.length;i++){
			ValueLiteral literal = getLiteral(i).getRightLiteral();
			literal.evaluateExpectOneValue();
			tokens[i] = literal.getToken();
		}
		return tokens;
	}
	
	public Token[][] getEndValues() throws MXQueryException{
		Token[][] values = new Token[compareLiterals.size()][];
		for(int i = 0; i < compareLiterals.size();i++){
			ValueLiteral literal = getLiteral(i).getRightLiteral();
			literal.evaluate(); 
			values[i] = literal.getValues();
		}
		return values;
	}
	
	public KXmlSerializer traverse(KXmlSerializer serializer) {
		try{
			serializer.startTag(null, "Index");
			serializer.attribute(null, "indexNb", ""+ getId());
			for(int i = 0; i< size(); i++){
				serializer.startTag(null, "value");
				serializer.attribute(null, "comparator", "" + getComparator(i));
				serializer.attribute(null, "compareTypes", "" + getCompareType(i));
				if(getColumnName(i) != null){
					serializer.attribute(null, "columnNames", "" + getColumnName(i));
				}else{
					serializer.attribute(null, "columnNames", "null");
				}
				serializer.endTag(null, "value");
			}
			serializer.endTag(null, "Index");
			return serializer;
		}catch(Exception err){
			throw new RuntimeException(err);
		}
	}
	
}
