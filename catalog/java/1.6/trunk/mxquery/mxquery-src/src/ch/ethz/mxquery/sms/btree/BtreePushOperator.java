/**
 * Copyright 2006-2007 ETH Zurich, The iMeMex Project Team
 * see http://www.iMeMex.org for more information on this project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package ch.ethz.mxquery.sms.btree;

/**
 * This interface allows us to push integer results to a consumer. We use the
 * hard-coded primitive type for performance reasons (generics require
 * boxing/unboxing).
 * <p>
 * When a push operator is instantiated, it is assumed to be open
 * (open-on-instantiate semantics).
 * 
 * @author lukas / marcos
 */
public interface BtreePushOperator {

	/**
	 * Passes the next element to the consumer.
	 * 
	 * @param element
	 */
	public void pass(Object element);

	/**
	 * Announces end of stream to the consumer.
	 * (That's a great method name! ;-)  Jens)
	 */
	public void thatsallfolks();

}
