package org.easysdi.monitor.biz.job;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.deegree.framework.util.StringTools;
import org.deegree.portal.owswatch.ServiceConfiguration;
import org.deegree.portal.owswatch.ServiceInvoker;
import org.easysdi.monitor.biz.job.Status.StatusValue;
import org.easysdi.monitor.biz.logging.MonitorServiceLog;
import org.easysdi.monitor.dat.dao.JobDaoHelper;
import org.easysdi.monitor.dat.dao.QueryDaoHelper;

/**
 * Represents a web service method to be tested.
 * <p>
 * A query is bound to a job (representing the web service itself) and specifies
 * which method should be tested and with which parameters. The timeout, test
 * interval, HTTP method and other such configuration is set for the job and
 * applies to all its queries.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 * @see Job
 * @see QueryParam
 */
public class Query {
    
    private final Logger logger = Logger.getLogger(Query.class);

    private QueryConfiguration   config;
    private ServiceInvoker       owsInvoker;
    private ServiceConfiguration owsConfig;
    private MonitorServiceLog    owsLogger;
    private long                 queryId;
    private Status               status;



    /**
     * No-argument constructor, used by the persistance mechanism.
     */
    private Query() {

        try {
            this.setOwsLogger(new MonitorServiceLog());
        } catch (IOException e) {
            this.logger.error("Unable to set the Monitor service logger.", e);
            this.setOwsLogger(null);
        }
    }



    /**
     * Creates a new query.
     * 
     * @param   parentJob   the job that this query is attached to
     * @param   queryName   the name of the query. (This must be unique among 
     *                      all queries for a given job.)
     * @param   methodName  the name of the method to be tested
     */
    public Query(Job parentJob, String queryName, String methodName) {
        this();
        this.setStatus(StatusValue.NOT_TESTED);
        final QueryConfiguration newConfig 
            = new QueryConfiguration(parentJob, this, queryName, methodName);
        this.setConfig(newConfig);
    }



    /**
     * Creates a new query.
     * 
     * @param   parentJob   the job that this query is attached to
     * @param   queryName   the name of the query. (This must be unique among 
     *                      all queries for a given job.)
     * @param   method      the service method object
     */
    public Query(Job parentJob, String queryName, ServiceMethod method) {
        this();
        this.setStatus(StatusValue.NOT_TESTED);
        final QueryConfiguration newConfig 
            = new QueryConfiguration(parentJob, this, queryName, method);
        this.setConfig(newConfig);
    }



    /**
     * Defines this query's configuration object.
     * 
     * @param   newConfig   the query configuration object
     */
    private void setConfig(QueryConfiguration newConfig) {

        this.config = newConfig;

    }



    /**
     * Gets this query's configuration object.
     * 
     * @return the config this query's configuration
     */
    public QueryConfiguration getConfig() {

        return this.config;

    }



    /**
     * Defines this query's owsWatch-compatible configuration object.
     * 
     * @param   newOwsConfig    the owsWatch service configuration object 
     *                          corresponding to this query
     * @see     ServiceConfiguration
     */
    private void setOwsConfig(ServiceConfiguration newOwsConfig) {
        this.owsConfig = newOwsConfig;
    }



    /**
     * Gets this query's owsWatch-compatible configuration object.
     * 
     * @return  the owsWatch service configuration object
     * @see     ServiceConfiguration
     */
    private ServiceConfiguration getOwsConfig() {
        this.setOwsConfig(this.getConfig().toOwsConfig());

        return this.owsConfig;
    }



    /**
     * Defines the owsWatch service invoker for this query.
     * 
     * @param   newOwsInvoker   the owsWatch service invoker
     * @see     ServiceInvoker
     */
    private void setOwsInvoker(ServiceInvoker newOwsInvoker) {
        this.owsInvoker = newOwsInvoker;
    }



    /**
     * Gets the owsWatch service invoker for this query.
     * 
     * @return  the owsWatch service invoker
     * @see     ServiceInvoker
     */
    private ServiceInvoker getOwsInvoker() {
        return this.owsInvoker;
    }



    /**
     * Defines the owsWatch-compatible result logger.
     * 
     * @param   newOwsLogger    the owsWatch-compatible result logger
     * @see     MonitorServiceLog
     * @see     QueryResult
     */
    private void setOwsLogger(MonitorServiceLog newOwsLogger) {
        this.owsLogger = newOwsLogger;
    }



    /**
     * Gets the owsWatch-compatible result logger.
     * 
     * @return  the owsWatch-compatible result logger
     * @see     MonitorServiceLog
     * @see     QueryResult
     */
    private MonitorServiceLog getOwsLogger() {
        return this.owsLogger;
    }



    /**
     * Defines this query's identifier.
     * <p>
     * <i><b>Note:</b> This method is intended for internal use and shouldn't be
     * called directly. The identifier is usually assigned through the
     * persistance mechanism.</i>
     * 
     * @param   newQueryId  a long uniquely identifying the query
     */
    @SuppressWarnings("unused")
    private void setQueryId(long newQueryId) {

        if (1 > newQueryId) {
            throw new IllegalArgumentException("Invalid query identifier.");
        }

        this.queryId = newQueryId;
    }



    /**
     * Gets this query's identifier.
     * 
     * @return  the long uniquely identifying the query
     */
    public long getQueryId() {
        return this.queryId;
    }



    /**
     * Defines the current status of this query.
     * <p>
     * <i><b>Note:</b> This method is intended for internal use and shouldn't be
     * called directly. The query status is usually defined when this query is
     * executed.</i>
     * 
     * @param   newStatusValue  the status value for this query
     */
    private void setStatus(Status.StatusValue newStatusValue) {

        if (null == newStatusValue) {
            throw new IllegalArgumentException("Status can't be null");
        }

        final Status newStatus = Status.getStatusObject(newStatusValue);

        if (null == newStatus) {
            throw new IllegalArgumentException(String.format(
                    "Unknown status '%1$s'.", newStatusValue.name()));
        }

        this.status = newStatus;
    }



    /**
     * Defines the current status of this query.
     * <p>
     * <i><b>Note:</b> This method is intended for internal use and shouldn't be
     * called directly. The query status is usually defined when this query is
     * executed.</i>
     * 
     * @param   newStatus   the status for this query
     */
    @SuppressWarnings("unused")
    private void setStatus(Status newStatus) {

        if (null == newStatus) {
            throw new IllegalArgumentException("Status can't be null.");
        }

        this.status = newStatus;
    }



    /**
     * Gets this query's current status.
     * 
     * @return this query's current status
     */
    public Status getStatus() {
        return this.status;
    }

    /**
     * Gets this query's current status value.
     * <p>
     * This is the status that resulted from the last automatic execution.
     * 
     * @return  this query's status value
     */
    public StatusValue getStatusValue() {
        return ((this.status != null) ? this.status.getStatusValue() : null);
    }

    /**
     * Checks this query's validity.
     * <p>
     * A query is valid if:
     * <ol>
     * <li>its status isn't null</li>
     * <li>a valid configuration object is set</li>
     * </ol>
     * 
     * @return  <code>true</code> if the query is valid
     * @see     QueryConfiguration#isValid()
     */
    public boolean isValid() {
        final QueryConfiguration queryConfig = this.getConfig();
        final boolean isStatusValid = (null != this.status);
        final boolean isConfigValid 
            = (null != queryConfig && queryConfig.isValid());

        return (isStatusValid && isConfigValid);
    }



    /**
     * Polls this query.
     * 
     * @param   resultLogging   <code>true</code> if the result of this polling 
     *                          should be kept in the logs
     * @return                  the result of this query's polling
     */
    public QueryResult execute(boolean resultLogging) {
        final MonitorServiceLog thisOwsLogger = this.getOwsLogger();
        thisOwsLogger.setResultLogged(resultLogging);
        this.setOwsInvoker(new ServiceInvoker(this.getOwsConfig(), 
                                              thisOwsLogger));
        this.getOwsInvoker().executeTest();

        final QueryResult result = thisOwsLogger.getLastResult();
        this.setStatus(result.getStatusValue());

        return result;
    }



    /**
     * Saves this query.
     * 
     * @return <code>true</code> if this query's has been successfully saved
     */
    public boolean persist() {
        return QueryDaoHelper.getQueryDao().persistQuery(this);
    }



    /**
     * Retrieves a query from identifying strings.
     * 
     * @param   jobIdString     a string containing either the parent job's 
     *                          identifier or its name
     * @param   queryIdString   a string containing either the sought query's 
     *                          identifier or its name
     * @return                  the query if it has been found or<br>
     *                          <code>null</code> otherwise
     */
    public static Query getFromIdStrings(String jobIdString,
                                         String queryIdString) {

        if (StringTools.isNullOrEmpty(jobIdString)) {
            throw new IllegalArgumentException(
                    "Job identifier string can't be null or empty");
        }

        if (StringTools.isNullOrEmpty(queryIdString)) {
            throw new IllegalArgumentException(
                    "Query identifier string can't be null or empty");
        }

        final Job parentJob 
            = JobDaoHelper.getJobDao().getJobFromIdString(jobIdString);

        if (null == parentJob) {
            return null;
        }

        return parentJob.getQueryFromIdString(queryIdString);
    }



    /**
     * Erases this query from the database.
     * 
     * @return  <code>true</code> if this query has been successfully deleted
     */
    public boolean delete() {

        if (QueryDaoHelper.getQueryDao().deleteQuery(this)) {
            final Job job = this.getConfig().getParentJob();
            job.removeQuery(this);
            final JobConfiguration jobConfig = job.getConfig();

            if (jobConfig.isAutomatic() && 0 < jobConfig.getTestInterval()) {
                job.updateScheduleState();
            }
            return true;
        }

        return false;
    }
}
