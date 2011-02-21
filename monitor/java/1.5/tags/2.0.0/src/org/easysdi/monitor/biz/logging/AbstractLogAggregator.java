package org.easysdi.monitor.biz.logging;

import java.util.Calendar;
import java.util.LinkedHashSet;
import java.util.Set;

import org.deegree.framework.util.DateUtil;
import org.easysdi.monitor.biz.job.Status.StatusValue;

/**
 * Defines generic log aggregation (that is, statistics calculation on a given 
 * period).
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public abstract class AbstractLogAggregator implements ILogAggregator {

    /**
     * The number of milliseconds in an hour.
     */
    private static final int MILLISECONDS_IN_HOUR = 3600000;
    
    
    /**
     * The total percentage.
     */
    private static final int TOTAL_PERCENT = 100;


    /**
     * Gets the dates for which a log entry exists in the given set.
     * 
     * @param rawLogs   the set containing the raw log entries to process
     * @return          a set containing the log dates
     */
    protected final Set<Calendar> getRawLogDates(Set<RawLogEntry> rawLogs) {
        final Set<Calendar> rawLogDates = new LinkedHashSet<Calendar>();

        if (null == rawLogs) {
            return null;
        }

        for (RawLogEntry entry : rawLogs) {
            final Calendar entryDate = 
                    DateUtil.truncateTime(entry.getRequestTime());

            if (!rawLogDates.contains(entryDate)) {
                rawLogDates.add(entryDate);
            }
        }

        return rawLogDates;
    }


    /**
     * Calculates statistics for a given set of log entries.
     * 
     * @param logs                  the set containing the logs to aggregate
     * @param availStart            the time when the availability period 
     *                              starts. (The date part is ignored)
     * @param availEnd              the time when the availability period ends. 
     *                              (The date parts is ignored)
     * @param logManager            the log manager for the object whose logs 
     *                              are processed.
     * @return                      the aggregate stats for the passed log 
     *                              entries 
     */
    protected final AggregateStats calculateStats(Set<RawLogEntry> logs,
            Calendar availStart, Calendar availEnd,
            LogManager logManager) {

        float totalRespTime = 0;
        float availHours = 0;
        int nbBizErrors = 0;
        int nbConnErrors = 0;
        int nbSuccessQueries = 0;
        Calendar spanEnd = availEnd;
        final float totalSpanHours = this.computeSpanHours(availStart, 
                                                           availEnd);

        for (RawLogEntry logEntry : logs) {
            final float respDelay = logEntry.getResponseDelay();
            final Calendar requestTime = logEntry.getRequestTime();

            if (0 < respDelay) {
                totalRespTime += respDelay;
                ++nbSuccessQueries;
            }

            if (logEntry.isBusinessError()) {
                ++nbBizErrors;
            } else if (logEntry.isConnectError()) {
                ++nbConnErrors;
            }

            if (StatusValue.AVAILABLE == logEntry.getStatusValue()) {
                availHours += this.computeSpanHours(requestTime, spanEnd);
            }

            spanEnd = (Calendar) requestTime.clone();
        }
        
        availHours += this.getSpanStartAvailHours(availStart, spanEnd, 
                                                  logManager);

        return this.buildStatsObject(nbSuccessQueries, totalRespTime, 
                                     availHours, totalSpanHours, nbBizErrors, 
                                     nbConnErrors);
    }


    /**
     * Gets how long the service was available during the span between the start
     * of the aggregation span and the first log entry.
     * 
     * @param   spanStart       the start of the aggregation span
     * @param   firstLogTime    the request date and time for the first log 
     *                          entry in the span 
     * @param   logManager      a log manager allowing to fetch the parent 
     *                          object's last log entry before the aggregation 
     *                          span 
     * @return                  the number of hours during which the service was
     *                          was available between the span start and the 
     *                          first log entry
     */
    private float getSpanStartAvailHours(Calendar spanStart, 
                                         Calendar firstLogTime, 
                                         LogManager logManager) {
        
        final ILogFetcher fetcher = logManager.getLogFetcher(); 
        final RawLogEntry lastLog = fetcher.fetchLastLogBeforeDate(spanStart); 

        if (null != lastLog
            && StatusValue.AVAILABLE == lastLog.getStatusValue()) {
            
            return this.computeSpanHours(spanStart, firstLogTime);
        }
        
        return 0;
    }
    
    

    /**
     * Generates an object holding aggregate stats.
     * 
     * @param   nbSuccessQueries    the number of times a query succeeded 
     * @param   totalRespTime       the sum of the successful queries' response
     *                              time
     * @param   availHours          the number of hours during which the web
     *                              service was available
     * @param   totalSpanHours      the total number of hours in the stats 
     *                              aggregation span
     * @param   nbBizErrors         the number of business errors in the span
     * @param   nbConnErrors        the number of connection errors in the span
     * @return                      the aggregate stats object
     */
    private AggregateStats buildStatsObject(int nbSuccessQueries, 
            float totalRespTime, float availHours, float totalSpanHours,
            int nbBizErrors, int nbConnErrors) {
        
        final AggregateStats stats = new AggregateStats();
        
        if (0 < nbSuccessQueries) {
            stats.setMeanRespTime(totalRespTime / nbSuccessQueries);
        } else {
            stats.setMeanRespTime(0F);
        }
        
        final float availRatio = availHours / totalSpanHours;
        
        stats.setAvailability(availRatio * AbstractLogAggregator.TOTAL_PERCENT);
        stats.setNbBizErrors(nbBizErrors);
        stats.setNbConnErrors(nbConnErrors);
        
        return stats;
    }

    
    
    /**
     * Calculates how many hours separates two dates. 
     * 
     * @param spanStart the start of the period
     * @param spanEnd   the end of the period
     * @return          the number of hours between the start and the end of
     *                  the period
     */
    private float computeSpanHours(Calendar spanStart, 
                                           Calendar spanEnd) {
        
        final long spanEndMillis = spanEnd.getTimeInMillis(); 
        final long spanStartMillis = spanStart.getTimeInMillis();
        final long differenceMillis = spanEndMillis - spanStartMillis; 
        
        return differenceMillis / AbstractLogAggregator.MILLISECONDS_IN_HOUR;
    }

}
