package org.easysdi.monitor.biz.logging;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.deegree.portal.owswatch.ServiceConfiguration;
import org.deegree.portal.owswatch.ServiceLog;
import org.deegree.portal.owswatch.ValidatorResponse;
import org.easysdi.monitor.biz.job.QueryResult;
import org.easysdi.monitor.dat.dao.LogDaoHelper;

/**
 * Processes the result of a owsWatch query polling.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.1, 2010-04-30
 *
 */
public class MonitorServiceLog extends ServiceLog {

    private static final long serialVersionUID = 5207213371103847912L;
    
    private final Logger logger = Logger.getLogger(MonitorServiceLog.class);

    private boolean           resultLogging;
    private QueryResult       lastResult;



    /**
     * Creates a new logger for Monitor queries.
     * 
     * @throws  IOException the paths are invalid.
     *                      <p>
     *                      This is an exception that may be thrown by the 
     *                      subclass. It should never happen with this class.
     */
    public MonitorServiceLog() throws IOException {
        super("", -1, "", "", "", null);

        this.setResultLogged(true);
    }



    /**
     * Processes a new polling result.
     * 
     * @param   response        the query response produced by owsWatch
     * @param   serviceConfig   the owsWatch test configuration
     */
    @Override
    public void addMessage(ValidatorResponse response,
                           ServiceConfiguration serviceConfig) {
        final QueryResult result 
            = new QueryResult(serviceConfig.getServiceid(), 
                              serviceConfig.getServiceName(), response,
                              serviceConfig.createHttpRequest(), 
                              serviceConfig.getHttpMethod());
        
        this.setLastResult(result);

        if (this.isResultLogged()) {
            final RawLogEntry logEntry = result.createRawLogEntry();

            if (!LogDaoHelper.getLogDao().persistRawLog(logEntry)) {
                this.logger.error(
                       "An exception was thrown while saving a log entry");
            }
        }
    }



    /**
     * Defines whether the query results must be logged.
     * 
     * @param   newResultLogging    <code>true</code> if the next result must be
     *                              logged
     */
    public void setResultLogged(boolean newResultLogging) {
        this.resultLogging = newResultLogging;
    }



    /**
     * Gets if the query results are logged.
     * 
     * @return <code>true</code> if the query result are logged
     */
    private boolean isResultLogged() {
        return this.resultLogging;
    }



    /**
     * Defines the last query result to date.
     * 
     * @param   result  the latest query result
     */
    private void setLastResult(QueryResult result) {

        this.lastResult = result;

    }



    /**
     * Gets the last query result.
     * 
     * @return  the query result
     */
    public QueryResult getLastResult() {

        return this.lastResult;

    }

}
