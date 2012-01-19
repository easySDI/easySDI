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

package ch.ethz.mxquery.sms.ftstore;

import java.util.Vector;

/**
 * Implementation of a Node in a Trie
 * @author jimhof
 */

public class TrieNode {
	
	public TrieNode[] children;
	public boolean abbrevation = false;
	
	// pos trie
	public Vector pos;
	public boolean word = false;
	
	public TrieNode(){
		children = new TrieNode[52];
	};
	
	public void setBool(){
		abbrevation = true;
	}

	public void addPos(String pos){
		if (this.pos == null){
			this.pos = new Vector();
		}
		this.pos.addElement(pos);
	}
	
	public Vector getPos(){
		return this.pos;
	}
	
	public void setWord(){
		this.word = true;
	}
	
	
	public TrieNode[] getChildren(){
		return children;
	}
	
	public TrieNode buildTrie(char letter){
		
		switch (letter){
		case 'a':
			if (children[0]== null){
				return children[0] = new TrieNode();
			}
			return children[0];
		case 'b':
			if (children[1]== null){
				return children[1] = new TrieNode();
			}
			return children[1];
		
		case 'c':
			if (children[2]== null){
				return children[2] = new TrieNode();
			}
			return children[2];
		case 'd':
			if (children[3]== null){
				return children[3] = new TrieNode();
			}
			return children[3];
		case 'e':
			if (children[4]== null){
				return children[4] = new TrieNode();
			}
			return children[4];
		case 'f':
			if (children[5]== null){
				return children[5] = new TrieNode();
			}
			return children[5];
		case 'g':
			if (children[6]== null){
				return children[6] = new TrieNode();
			}
			return children[6];
		case 'h':
			if (children[7]== null){
				return children[7] = new TrieNode();
			}
			return children[7];
		case 'i':
			if (children[8]== null){
				return children[8] = new TrieNode();
			}
			return children[8];
		case 'j':
			if (children[9]== null){
				return children[9] = new TrieNode();
			}
			return children[9];
		case 'k':
			if (children[10]== null){
				return children[10] = new TrieNode();
			}
			return children[10];
		case 'l':
			if (children[11]== null){
				return children[11] = new TrieNode();
			}
			return children[11];
		case 'm':
			if (children[12]== null){
				return children[12] = new TrieNode();
			}
			return children[12];
		case 'n':
			if (children[13]== null){
				return children[13] = new TrieNode();
			}
			return children[13];
		case 'o':
			if (children[14]== null){
				return children[14] = new TrieNode();
			}
			return children[14];
		case 'p':
			if (children[15]== null){
				return children[15] = new TrieNode();
			}
			return children[15];
		case 'q':
			if (children[16]== null){
				return children[16] = new TrieNode();
			}
			return children[16];
		case 'r':
			if (children[17]== null){
				return children[17] = new TrieNode();
			}
			return children[17];
		case 's':
			if (children[18]== null){
				return children[18] = new TrieNode();
			}
			return children[18];
		case 't':
			if (children[19]== null){
				return children[19] = new TrieNode();
			}
			return children[19];
		case 'u':
			if (children[20]== null){
				return children[20] = new TrieNode();
			}
			return children[20];
		case 'v':
			if (children[21]== null){
				return children[21] = new TrieNode();
			}
			return children[21];
		case 'w':
			if (children[22]== null){
				return children[22] = new TrieNode();
			}
			return children[22];
		case 'x':
			if (children[23]== null){
				return children[23] = new TrieNode();
			}
			return children[23];
		case 'y':
			if (children[24]== null){
				return children[24] = new TrieNode();
			}
			return children[24];
		case 'z':
			if (children[25]== null){
				return children[25] = new TrieNode();
			}
			return children[25];	
		case 'A':
			if (children[26]== null){
				return children[26] = new TrieNode();
			}
			return children[26];
		case 'B':
			if (children[27]== null){
				return children[27] = new TrieNode();
			}
			return children[27];
		
		case 'C':
			if (children[28]== null){
				return children[28] = new TrieNode();
			}
			return children[28];
		case 'D':
			if (children[29]== null){
				return children[29] = new TrieNode();
			}
			return children[29];
		case 'E':
			if (children[30]== null){
				return children[30] = new TrieNode();
			}
			return children[30];
		case 'F':
			if (children[31]== null){
				return children[31] = new TrieNode();
			}
			return children[31];
		case 'G':
			if (children[32]== null){
				return children[32] = new TrieNode();
			}
			return children[32];
		case 'H':
			if (children[33]== null){
				return children[33] = new TrieNode();
			}
			return children[33];
		case 'I':
			if (children[34]== null){
				return children[34] = new TrieNode();
			}
			return children[34];
		case 'J':
			if (children[35]== null){
				return children[35] = new TrieNode();
			}
			return children[35];
		case 'K':
			if (children[36]== null){
				return children[36] = new TrieNode();
			}
			return children[36];
		case 'L':
			if (children[37]== null){
				return children[37] = new TrieNode();
			}
			return children[37];
		case 'M':
			if (children[38]== null){
				return children[38] = new TrieNode();
			}
			return children[38];
		case 'N':
			if (children[39]== null){
				return children[39] = new TrieNode();
			}
			return children[39];
		case 'O':
			if (children[40]== null){
				return children[40] = new TrieNode();
			}
			return children[40];
		case 'P':
			if (children[41]== null){
				return children[41] = new TrieNode();
			}
			return children[41];
		case 'Q':
			if (children[42]== null){
				return children[42] = new TrieNode();
			}
			return children[42];
		case 'R':
			if (children[43]== null){
				return children[43] = new TrieNode();
			}
			return children[43];
		case 'S':
			if (children[44]== null){
				return children[44] = new TrieNode();
			}
			return children[44];
		case 'T':
			if (children[45]== null){
				return children[45] = new TrieNode();
			}
			return children[45];
		case 'U':
			if (children[46]== null){
				return children[46] = new TrieNode();
			}
			return children[46];
		case 'V':
			if (children[47]== null){
				return children[47] = new TrieNode();
			}
			return children[47];
		case 'W':
			if (children[48]== null){
				return children[48] = new TrieNode();
			}
			return children[48];
		case 'X':
			if (children[49]== null){
				return children[49] = new TrieNode();
			}
			return children[49];
		case 'Y':
			if (children[50]== null){
				return children[50] = new TrieNode();
			}
			return children[50];
		case 'Z':
			if (children[51]== null){
				return children[51] = new TrieNode();
			}
			return children[51];	
		}
		

		return null;
	}
	public TrieNode getChild(char letter){
		
		switch (letter){
		case 'a':
			if (children[0]== null){
				return null;
			}
			return children[0];
		case 'b':
			if (children[1]== null){
				return null;
			}
			return children[1];
		
		case 'c':
			if (children[2]== null){
				return null;
			}
			return children[2];
		case 'd':
			if (children[3]== null){
				return null;
			}
			return children[3];
		case 'e':
			if (children[4]== null){
				return null;
			}
			return children[4];
		case 'f':
			if (children[5]== null){
				return null;
			}
			return children[5];
		case 'g':
			if (children[6]== null){
				return null;
			}
			return children[6];
		case 'h':
			if (children[7]== null){
				return null;
			}
			return children[7];
		case 'i':
			if (children[8]== null){
				return null;
			}
			return children[8];
		case 'j':
			if (children[9]== null){
				return null;
			}
			return children[9];
		case 'k':
			if (children[10]== null){
				return null;
			}
			return children[10];
		case 'l':
			if (children[11]== null){
				return null;
			}
			return children[11];
		case 'm':
			if (children[12]== null){
				return null;
			}
			return children[12];
		case 'n':
			if (children[13]== null){
				return null;
			}
			return children[13];
		case 'o':
			if (children[14]== null){
				return null;
			}
			return children[14];
		case 'p':
			if (children[15]== null){
				return null;
			}
			return children[15];
		case 'q':
			if (children[16]== null){
				return null;
			}
			return children[16];
		case 'r':
			if (children[17]== null){
				return null;
			}
			return children[17];
		case 's':
			if (children[18]== null){
				return null;
			}
			return children[18];
		case 't':
			if (children[19]== null){
				return null;
			}
			return children[19];
		case 'u':
			if (children[20]== null){
				return null;
			}
			return children[20];
		case 'v':
			if (children[21]== null){
				return null;
			}
			return children[21];
		case 'w':
			if (children[22]== null){
				return null;
			}
			return children[22];
		case 'x':
			if (children[23]== null){
				return null;
			}
			return children[23];
		case 'y':
			if (children[24]== null){
				return null;
			}
			return children[24];
		case 'z':
			if (children[25]== null){
				return null;
			}
			return children[25];	
		case 'A':
			if (children[26]== null){
				return null;
			}
			return children[26];
		case 'B':
			if (children[27]== null){
				return null;
			}
			return children[27];
		
		case 'C':
			if (children[28]== null){
				return null;
			}
			return children[28];
		case 'D':
			if (children[29]== null){
				return null;
			}
			return children[29];
		case 'E':
			if (children[30]== null){
				return null;
			}
			return children[30];
		case 'F':
			if (children[31]== null){
				return null;
			}
			return children[31];
		case 'G':
			if (children[32]== null){
				return null;
			}
			return children[32];
		case 'H':
			if (children[33]== null){
				return null;
			}
			return children[33];
		case 'I':
			if (children[34]== null){
				return null;
			}
			return children[34];
		case 'J':
			if (children[35]== null){
				return null;
			}
			return children[35];
		case 'K':
			if (children[36]== null){
				return null;
			}
			return children[36];
		case 'L':
			if (children[37]== null){
				return null;
			}
			return children[37];
		case 'M':
			if (children[38]== null){
				return null;
			}
			return children[38];
		case 'N':
			if (children[39]== null){
				return null;
			}
			return children[39];
		case 'O':
			if (children[40]== null){
				return null;
			}
			return children[40];
		case 'P':
			if (children[41]== null){
				return null;
			}
			return children[41];
		case 'Q':
			if (children[42]== null){
				return null;
			}
			return children[42];
		case 'R':
			if (children[43]== null){
				return null;
			}
			return children[43];
		case 'S':
			if (children[44]== null){
				return null;
			}
			return children[44];
		case 'T':
			if (children[45]== null){
				return null;
			}
			return children[45];
		case 'U':
			if (children[46]== null){
				return null;
			}
			return children[46];
		case 'V':
			if (children[47]== null){
				return null;
			}
			return children[47];
		case 'W':
			if (children[48]== null){
				return null;
			}
			return children[48];
		case 'X':
			if (children[49]== null){
				return null;
			}
			return children[49];
		case 'Y':
			if (children[50]== null){
				return null;
			}
			return children[50];
		case 'Z':
			if (children[51]== null){
				return null;
			}
			return children[51];	
		}
		return null;
	}
}
