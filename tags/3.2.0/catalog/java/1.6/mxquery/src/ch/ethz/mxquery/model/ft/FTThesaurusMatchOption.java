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

package ch.ethz.mxquery.model.ft;

import java.util.Vector;

import ch.ethz.mxquery.util.ObjectObjectPair;
/**
 * FTThesaurus Match Option
 * @author jimhof
 *
 */

public class FTThesaurusMatchOption extends MatchOption {

	private String location = "";
	private String relationship = "";
	private Range range = null;
	private Vector ids = null;
	private boolean isRelationship = false;
	private boolean isRange = false;
	private boolean multipleIds = false;
	
	public FTThesaurusMatchOption(){
		super(MatchOption.MATCH_OPTION_TYPE_THESAURUS,false);
	}
	
	public FTThesaurusMatchOption(ObjectObjectPair locAndRelation) {
		super(MatchOption.MATCH_OPTION_TYPE_THESAURUS,true);
		this.location = (String)locAndRelation.getFirst();
		
		if (locAndRelation.getSecond() instanceof String){
			this.relationship = (String) locAndRelation.getSecond();
			isRelationship = true;
		}
		else if (locAndRelation.getSecond() instanceof Range){
			this.range = (Range) locAndRelation.getSecond();
			isRange = true;
		}
		else{
			this.ids = (Vector) locAndRelation.getSecond();
			this.multipleIds = true;
		}
	}
	
	public String getLocation(){
		return this.location;
	}

	public String getRelationship(){
		return this.relationship;
	}
	public Range getRange(){
		return this.range;
	}
	
	public Vector getIds(){
		return this.ids;
	}
	
	public boolean hasRange(){
		return this.isRange;
	}
	
	public boolean hasRelationship(){
		return this.isRelationship;
	}
	
	public boolean hasMultipleIds(){
		return this.multipleIds;
	}
	
	
}
