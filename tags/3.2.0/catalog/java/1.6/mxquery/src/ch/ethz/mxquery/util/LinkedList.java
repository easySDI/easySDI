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

package ch.ethz.mxquery.util;

import java.util.Enumeration;

/**
 * Self programmed Linked List (for J2ME support)
 * 
 * @author David Alexander Graf
 * 
 */
public class LinkedList {
	protected Entry head;
	protected Entry tail;

	public LinkedList() {
		this.head = new Entry(null, null, null);
		this.tail = new Entry(null, null, null);
		this.head.next = this.tail;
		this.tail.prev = this.head;
	}
	
	private static class LLIterator implements Enumeration {
		private LinkedList ll;
		private Entry current;
		private boolean traverse;
		
		public LLIterator(LinkedList ll) {
			this(ll, false);
			
		}
		
		public LLIterator(LinkedList ll, boolean traverse){
			this.ll = ll;
			this.traverse = traverse;
			if(!traverse){
				this.current = ll.head;
			}else{
				this.current = ll.tail;
			}
		}
		
		public boolean hasMoreElements() {
			if(!traverse){
				return this.current.next != ll.tail;
			}else{
				return this.current.prev != ll.head;
			}
		}

		public Object nextElement() {
			if (this.hasMoreElements()) {
				if(!traverse){
					this.current = this.current.next;
				}else{
					this.current = this.current.prev;
				}
			}
			return this.current.element;
		}

		public void remove() {
			throw new RuntimeException("Not implemented!");
			
		}
	}

	private static class Entry {
		Object element;
		Entry next;
		Entry prev;

		Entry(Object element, Entry prev, Entry next) {
			this.element = element;
			this.prev = prev;
			this.next = next;
		}
	}
	
	public boolean isEmpty() {
		return this.head.next == this.tail;
	}
	
	
	
	public boolean add(Object o) {
		return this.addLast(o);
	}

	public boolean addLast(Object o) {
		Entry e = new Entry(o, this.tail.prev, this.tail);
		this.tail.prev.next = e;
		this.tail.prev = e;
		return true;
	}
	
	public boolean addFirst(Object o) {
		Entry e = new Entry(o, this.head, this.head.next);
		this.head.next.prev = e;
		this.head.next = e;
		return true;
	}
	
	public void clear() {
		this.head.next = this.tail;
		this.tail.prev = this.head;
	}
	
	public Enumeration traverseList(){
		return new LLIterator(this, true);
	}
	
	public Enumeration elements() {
		return new LLIterator(this);
	}
	
	public boolean addAll(LinkedList ll) {
		if (ll == null){
			throw new NullPointerException();
		}
		if (!ll.isEmpty()) {
			Enumeration it = ll.elements();
			while (it.hasMoreElements()) {
				this.addLast(it.nextElement());
			}
		}
		return true;
	}
}
