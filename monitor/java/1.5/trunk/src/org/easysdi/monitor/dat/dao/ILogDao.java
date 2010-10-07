package org.easysdi.monitor.dat.dao;

import java.util.Calendar;
import java.util.Date;
import java.util.Map;
import java.util.Set;

import org.easysdi.monitor.biz.logging.AbstractAggregateLogEntry;
import org.easysdi.monitor.biz.logging.LogFetcherException;
import org.easysdi.monitor.biz.logging.RawLogEntry;

/**
 * Provides log entries persistance operations.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public interface ILogDao {

    /**
     * Log entry parent type.
     */
    enum ParentType {
        JOB,
        QUERY
    }

    
    
    /**
     * Log entry type.
     */
    enum LogType {
        AGGREGATE,
        RAW
    }



    /**
     * Erases an aggregate log entry.
     * 
     * @param   aggregateLog    the aggregate log entry to delete
     * @return                  <code>true</code> if the deletion was successful
     */
    boolean deleteAggregLog(AbstractAggregateLogEntry aggregateLog);



    /**
     * Erases a raw log entry.
     * 
     * @param   rawLog  the raw log entry to delete
     * @return          <code>true</code> if the deletion was successful
     */
    boolean deleteRawLog(RawLogEntry rawLog);



    /**
     * Gets aggregate log entries based on criteria.
     * <p>
     * Each parameter can be set to <code>null</code> if it must be ignored.
     * <p>
     * Log entries are always sorted by date descending.
     * 
     * @param   type        the parent type of the aggregate log entry
     * @param   parentId    the identifier for the aggregate log entry parent
     * @param   minDate     the date from which the aggregate logs must be 
     *                      fetched
     * @param   maxDate     the date up to which the aggregate logs must be 
     *                      fetched
     * @param   maxResults  the maximum number of aggregate logs to fetch
     * @param   startIndex  the index of the first entry to fetch inside the set
     *                      defined by the other criteria. This is useful for 
     *                      paging purposes.
     * @return              a map with the found aggregate log entries as the 
     *                      value and their date as the key
     * @throws  LogFetcherException an error occurred during the log fetching 
     */
    Map<Date, AbstractAggregateLogEntry> fetchAggregLogs(
                    ParentType type, long parentId, Calendar minDate,
                    Calendar maxDate, Integer maxResults, Integer startIndex)
        throws LogFetcherException;


    
    /**
     * Gets raw log entries based on criteria.
     * <p>
     * Each parameter can be set to <code>null</code> if it must be ignored.
     * <p>
     * Log entries are always sorted by date descending.
     * 
     * @param   queryIds    an array containing the identifiers of the queries
     *                      whose log entries must be fetched
     * @param   minDate     the date from which the raw logs must be 
     *                      fetched
     * @param   maxDate     the date up to which the raw logs must be 
     *                      fetched
     * @param   maxResults  the maximum number of raw logs to fetch
     * @param   startIndex  the index of the first entry to fetch inside the set
     *                      defined by the other criteria. This is useful for 
     *                      paging purposes.
     * @return              a map with the found raw log entries as the 
     *                      value and their date as the key
     * @throws  LogFetcherException an error occurred during the log fetching 
     */

    Set<RawLogEntry> fetchRawLogs(Long[] queryIds, Calendar minDate, 
                                  Calendar maxDate, Integer maxResults,
                                  Integer startIndex)
        throws LogFetcherException;



    /**
     * Saves a raw log entry.
     * 
     * @param   rawLog  the raw log entry
     * @return          <code>true</code> if the log entry was successfully 
     *                  saved
     */
    boolean persistRawLog(RawLogEntry rawLog);



    /**
     * Saves an aggregate log entry. 
     * 
     * @param   aggregateLog    the aggregate log entry
     * @return                  <code>true</code> if the log entry was 
     *                          successfully saved
     */
    boolean persistAggregLog(AbstractAggregateLogEntry aggregateLog);



    /**
     * Gets the last raw log entry before a given date.
     * 
     * @param   queryIds    an array containing the identifer of the queries
     *                      whose raw logs are concerned
     * @param   date        the date
     * @return              the last raw log entry before the date, if any, or
     *                      <br><code>null</code> otherwise
     */
    RawLogEntry fetchLastLogBeforeDate(Long[] queryIds, Calendar date);



    /**
     * Gets the number of raw log items returned by a query. 
     * 
     * @param   queryIds    an array containing the identifiers of the queries
     *                      whose log entries must be fetched
     * @param   minDate     the date from which the raw logs must be 
     *                      fetched
     * @param   maxDate     the date up to which the raw logs must be 
     *                      fetched
     * @param   maxResults  the maximum number of raw logs to fetch
     * @param   startIndex  the index of the first entry to fetch inside the set
     *                      defined by the other criteria. This is useful for 
     *                      paging purposes.
     * @return              a long indicating how many raw log entries match
     *                      the given parameters
     */
    long getRawLogsItemsNumber(Long[] queryIds, Calendar minDate,
                    Calendar maxDate, Integer maxResults, Integer startIndex);



    
    /**
     * Gets the number of aggregate log items returned by a query. 
     * 
     * @param   parentType  the type of the aggregate logs parent 
     * @param   parentId    the identifier of the aggregate logs parent
     * @param   minDate     the date from which the raw logs must be 
     *                      fetched
     * @param   maxDate     the date up to which the raw logs must be 
     *                      fetched
     * @param   maxResults  the maximum number of raw logs to fetch
     * @param   startIndex  the index of the first entry to fetch inside the set
     *                      defined by the other criteria. This is useful for 
     *                      paging purposes.
     * @return              a long indicating how many aggregate log entries 
     *                      match the given parameters
     */
    long getAggregateLogsItemsNumber(
                    ParentType parentType, long parentId, Calendar minDate,
                    Calendar maxDate, Integer maxResults, Integer startIndex);

}
