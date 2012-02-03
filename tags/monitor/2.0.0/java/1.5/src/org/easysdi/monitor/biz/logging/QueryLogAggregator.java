package org.easysdi.monitor.biz.logging;

import java.util.Calendar;
import java.util.Date;
import java.util.Map;
import java.util.Set;

import org.deegree.framework.util.DateUtil;
import org.easysdi.monitor.biz.job.JobConfiguration;
import org.easysdi.monitor.biz.job.QueryConfiguration;
import org.easysdi.monitor.dat.dao.LogDaoHelper;

/**
 * Aggregates the raw log entries of a query into daily statistical summaries.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public class QueryLogAggregator extends AbstractLogAggregator {

    private LogManager logManager;



    /**
     * Creates a new aggregator for query logs.
     * 
     * @param   queryLogManager  the object managing the log entries for the 
     *                           target query
     */
    public QueryLogAggregator(LogManager queryLogManager) {

        if (null == queryLogManager) {
            throw new IllegalArgumentException("Log manager can't be null");
        }

        this.setLogManager(queryLogManager);
    }



    /**
     * Sets the manager for the query's log entries.
     * 
     * @param   queryLogManager the log manager object
     */
    private void setLogManager(LogManager queryLogManager) {
        this.logManager = queryLogManager;
    }



    /**
     * Gets the manager for the query's log entries.
     * 
     * @return  the log manager
     */
    private LogManager getLogManager() {
        return this.logManager;
    }



    /**
     * Creates the aggregated logs entries for the query.
     */
    public void aggregateRawLogs() {
        final Set<RawLogEntry> allRawLogs = this.getLogManager().getRawLogs();
        final Set<Calendar> rawLogDates = this.getRawLogDates(allRawLogs);
        final Map<Date, AbstractAggregateLogEntry> allAggregateLogs 
            = this.getLogManager().getAggregateLogs();
        final Calendar today = DateUtil.truncateTime(Calendar.getInstance());

        for (Calendar dateRawLog : rawLogDates) {
            final QueryLogFetcher logFetcher 
                = (QueryLogFetcher) this.getLogManager().getLogFetcher();
            final QueryConfiguration queryConfig 
                = logFetcher.getParentQuery().getConfig(); 
            final JobConfiguration parentJobConfig 
                = queryConfig.getParentJob().getConfig();
            final Calendar h24EndTime = DateUtil.setTime(dateRawLog, 
                                                         "23:59:59");
            final Calendar slaStart 
                = DateUtil.mixDateTime(dateRawLog, 
                                       parentJobConfig.getSlaStartTime());
            final Calendar slaEnd 
                = DateUtil.mixDateTime(dateRawLog, 
                                       parentJobConfig.getSlaEndTime());
            Set<RawLogEntry> h24RawLogs;
            Set<RawLogEntry> slaRawLogs;

            if (!allAggregateLogs.containsKey(dateRawLog.getTime())
                && 0 != DateUtil.compareWithoutTime(dateRawLog, today)) {

                h24RawLogs = logFetcher.fetchRawLogsSubset(dateRawLog,
                                                           h24EndTime, null,
                                                           null);
                slaRawLogs = logFetcher.fetchRawLogsSubset(slaStart, slaEnd,
                                                           null, null);
                final AggregateStats h24Stats 
                    = this.calculateStats(h24RawLogs, dateRawLog, h24EndTime,
                                          this.getLogManager());
                final AggregateStats slaStats
                    = this.calculateStats(slaRawLogs, slaStart, slaEnd, 
                                          this.getLogManager());

                final QueryAggregateLogEntry aggregLog 
                    = new QueryAggregateLogEntry(logFetcher.getParentQuery(), 
                                                 dateRawLog, h24Stats, 
                                                 slaStats);

                LogDaoHelper.getLogDao().persistAggregLog(aggregLog);
                allAggregateLogs.put(dateRawLog.getTime(), aggregLog);
            }
        }

    }
}
