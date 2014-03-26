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

package ch.ethz.mxquery.sms.MMimpl;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.sms.interfaces.IndexUpdate;

public abstract class InPlaceStore implements IndexUpdate {
	protected WindowBuffer cont;
	public int id;
	
	public InPlaceStore(int id, WindowBuffer container){
		this.id = id;
		cont = container;
	}
	
	public int getMyId(){
		return id;
	}
	public Window getIterator(Context ctx) throws MXQueryException{
		Window wnd =  cont.getNewWindowIterator(1, Window.END_OF_STREAM_POSITION);
		wnd.setContext(ctx, false);
		return wnd;
	}
	public int compare(Source store) {
		// TODO Auto-generated method stub
		return 0;
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		// TODO Auto-generated method stub
		return null;
	}

	public String getURI() {
		// TODO Auto-generated method stub
		return null;
	}	
}
