/**
 * 
 */
package org.easysdi.monitor.biz.job;

import org.easysdi.monitor.dat.dao.LastLogDaoHelper;



/**
 * @author Thomas Bergstedt
 *
 */
public class OverviewLastQueryResult {

		private long lastQueryResultID;
		private String pictureUrl;
		private String xmlResult;
		private String textResult;
		private long queryid;

		public OverviewLastQueryResult() {
			// TODO Auto-generated constructor stub
		}
		
		/**
		 * @return the queryid
		 */
		public long getQueryid() {
			return queryid;
		}
		/**
		 * @param queryid the queryid to set
		 */
		public void setQueryid(long queryid) {
			this.queryid = queryid;
		}



		public long getLastQueryResultID() {
			return lastQueryResultID;
		}

		public void setLastQueryResultID(long lastQueryResultID) {
			this.lastQueryResultID = lastQueryResultID;
		}

//		/**
//		 * @return the deliveryTime
//		 */
//		public float getDeliveryTime() {
//			return deliveryTime;
//		}
//
//		/**
//		 * @param deliveryTime the deliveryTime to set
//		 */
//		public void setDeliveryTime(float deliveryTime) {
//			this.deliveryTime = deliveryTime;
//		}
//
//		/**
//		 * @return the normTime
//		 */
//		public float getNormTime() {
//			return normTime;
//		}
//
//		/**
//		 * @param normTime the normTime to set
//		 */
//		public void setNormTime(float normTime) {
//			this.normTime = normTime;
//		}
//
//		/**
//		 * @return the foundOutput
//		 */
//		public String getFoundOutput() {
//			return foundOutput;
//		}
//
//		/**
//		 * @param foundOutput the foundOutput to set
//		 */
//		public void setFoundOutput(String foundOutput) {
//			this.foundOutput = foundOutput;
//		}
//
//		/**
//		 * @return the normOutput
//		 */
//		public String getNormOutput() {
//			return normOutput;
//		}
//
//		/**
//		 * @param normOutput the normOutput to set
//		 */
//		public void setNormOutput(String normOutput) {
//			this.normOutput = normOutput;
//		}
//
//		/**
//		 * @return the size
//		 */
//		public int getSize() {
//			return size;
//		}
//
//		/**
//		 * @param size the size to set
//		 */
//		public void setSize(int size) {
//			this.size = size;
//		}
//
//		/**
//		 * @return the normSize
//		 */
//		public int getNormSize() {
//			return normSize;
//		}
//
//		/**
//		 * @param normSize the normSize to set
//		 */
//		public void setNormSize(int normSize) {
//			this.normSize = normSize;
//		}

		/**
		 * @return the image
		 */
		public String getPictureUrl() {
			return pictureUrl;
		}

		/**
		 * @param image the image to set
		 */
		public void setPictureUrl(String picture) {
			this.pictureUrl = picture;
		}

		/**
		 * @return the xmlResult
		 */
		public String getXmlResult() {
			return xmlResult;
		}

		/**
		 * @param xmlResult the xmlResult to set
		 */
		public void setXmlResult(String xmlResult) {
			this.xmlResult = xmlResult;
		}

		/**
		 * @return the textResult
		 */
		public String getTextResult() {
			return textResult;
		}

		/**
		 * @param textResult the textResult to set
		 */
		public void setTextResult(String textResult) {
			this.textResult = textResult;
		}
		
		/**
		* Create the newlastrequest in DB 
		* 
		* @return <code>true</code> if this overviewpage has successfully been saved
		*/
		public boolean createNewLastRequest()
		{
			return LastLogDaoHelper.getLastLogDao().create(this);
		}
		
		/**
		 *  Maybe noot needed
		 * @return
		 */
		public OverviewLastQueryResult createLastResult()
		{
			final OverviewLastQueryResult newLastQueryResult = new OverviewLastQueryResult();
			// Create const with this objects
			return newLastQueryResult;
		}
		
}
