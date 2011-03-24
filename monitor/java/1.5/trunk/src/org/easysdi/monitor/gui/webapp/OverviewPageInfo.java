/**
 * 
 */
package org.easysdi.monitor.gui.webapp;

import java.util.Map;

import org.deegree.framework.util.BooleanUtil;
import org.easysdi.monitor.biz.job.Overview;

/*import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;

import org.apache.commons.lang.StringUtils;
import org.apache.log4j.Logger;
import org.deegree.framework.util.DateUtil;
import org.deegree.framework.util.StringTools;
import org.easysdi.monitor.biz.job.AbstractValueObject;
import org.easysdi.monitor.biz.job.HttpMethod;
import org.easysdi.monitor.biz.job.Job;
import org.easysdi.monitor.biz.job.JobConfiguration;
import org.easysdi.monitor.biz.job.ServiceType;
*/
/**
 * @author BERG3428
 *
 */
public class OverviewPageInfo {

	 private String  url;
	 private Boolean usePassword;

	/**
    * 
	*/
	public OverviewPageInfo() {
	}
	/**
	 * @return the url
	 */
	private String getUrl() {
		return url;
	}

	/**
	 * @param url the url to set
	 */
	private void setUrl(String url) {
		this.url = url;
	}

	/**
	 * @return the usePassword
	 */
	private Boolean getUsePassword() {
		return usePassword;
	}

	/**
	 * @param usePassword the usePassword to set
	 */
	private void setUsePassword(String usePassword) {
		this.usePassword = BooleanUtil.parseBooleanStringWithNull(usePassword);
	}




	
	
	
	/**
     * Creates a overviewpage info object from a servlet request's parameters.
     * 
     * @param   requestParams       a map containing the request parameters
     * @param   enforceMandatory    <code>true</code> to fail if a mandatory
     *                              property isn't set
     *                              <p>
     *                              Basically, you should set this parameter 
     *                              to <code>true</code> if you intend to create
     *                              a new job and to <code>false</code> to 
     *                              modify an existing one.
     * @return                      <ul>
     *                              <li>the job info if it has been successfully
     *                              created</li>
     *                              <li><code>null</code> otherwise</li>
     *                              </ul>
     * @throws  MandatoryParameterException a null value was assigned to a 
     *                                      mandatory parameter 
     */
    public static OverviewPageInfo createFromParametersMap(
            Map<String, String> requestParams, Boolean enforceMandatory) 
        throws MandatoryParameterException {
        
        final OverviewPageInfo newOverviewPageInfo = new OverviewPageInfo();
        
        newOverviewPageInfo.setUrl(requestParams.get("url"));
        newOverviewPageInfo.setUsePassword(requestParams.get("usePassword"));
        
        return newOverviewPageInfo;
    }
    
    /**
     * Creates a new overview from this object's information.
     * 
     * @return  <ul>
     *          <li>the overviewpage if it has been created successfully</li>
     *          <li><code>null</code> otherwise</li>
     *          </ul>
     * @throws  MandatoryParameterException a null value was assigned to a 
     *                                      mandatory parameter 
     */
    public Overview createOverview() throws MandatoryParameterException {
    	final Overview newOverview = Overview.createDefault(this.getUrl(),this.getUsePassword());
    	if(newOverview.createNewOverview())
    	{
    		return newOverview;
    	}
    	return null;
    }
    
}
