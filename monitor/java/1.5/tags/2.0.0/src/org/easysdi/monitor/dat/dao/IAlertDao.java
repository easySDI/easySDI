package org.easysdi.monitor.dat.dao;

import java.util.List;

import org.easysdi.monitor.biz.alert.Alert;

/**
 * Provides alert persistance operations.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public interface IAlertDao {
    
    /**
     * Saves an alert.
     * 
     * @param   alert   the alert
     */
    void persist(Alert alert);



    /**
     * Fetches the alerts that a given job triggered.
     * 
     * @param   jobId   the long identifying the job
     * @param   onlyRss <code>true</code> to fetch only the alerts marked for
     *                  RSS exposure
     * @return          a list containing the found alerts
     */
    List<Alert> getAlertsForJob(long jobId, boolean onlyRss);

}
