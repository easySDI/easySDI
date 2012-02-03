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

package ch.ethz.mxquery.model;

public class Constants {
	public static final int COMP_VALUE = 1;

	public static final int COMP_GENERAL = 2;

	public static final int COMP_NODE = 3;

	public static final int COMP_EQ = 1;

	public static final int COMP_LT = 2;
	
	public static final int COMP_GT = 3;

	public static final int COMP_LE = 4;

	public static final int COMP_GE = 5;
	
	public static final int COMP_NE = 6;

	public static String getCompareString(int comparator, int compareType){
		if(compareType == Constants.COMP_VALUE){
			switch (comparator) {
			case Constants.COMP_EQ:
				return "eq";
			case Constants.COMP_NE:
				return "ne";
			case Constants.COMP_LT:
				return "lt";
			case Constants.COMP_LE:
				return "le";
			case Constants.COMP_GT:
				return "gt";
			case Constants.COMP_GE:
				return "ge";
			}
		}
		if(compareType == Constants.COMP_GENERAL){
			switch (comparator) {
			case Constants.COMP_EQ:
				return "=";
			case Constants.COMP_NE:
				return "!=";
			case Constants.COMP_LT:
				return "<";
			case Constants.COMP_LE:
				return "<=";
			case Constants.COMP_GT:
				return ">";
			case Constants.COMP_GE:
				return ">=";
			}
		}
		if(compareType == Constants.COMP_NODE){
			switch (comparator) {
			case Constants.COMP_EQ:
				return "is";
			case Constants.COMP_LT:
				return "<<";
			case Constants.COMP_GT:
				return ">>";
			}
		}
		return "error";
	}
	
	
}
