package org.easysdi.monitor.biz.logging;

import java.io.Serializable;
import java.util.Calendar;

import org.apache.commons.lang.builder.HashCodeBuilder;
import org.easysdi.monitor.biz.job.Query;
import org.easysdi.monitor.biz.util.HashCodeConstants;

/**
 * Represents aggregation stats of raw log entries for a given day and 
 * a given query.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public class QueryAggregateLogEntry extends AbstractAggregateLogEntry implements
                Serializable {

    private static final long serialVersionUID = 426114429754090300L;
    private long              queryId;
    
    

    /**
     * No-argument constructor used by the persistence mechanism.
     */
    @SuppressWarnings("unused")
    private QueryAggregateLogEntry() {
                
    }

    
    
    /**
     * Creates a new query aggregate log entry.
     * 
     * @param   parentQuery     the query for which this log enty is created
     * @param   dateLog         the date of the log entry
     */
    public QueryAggregateLogEntry(Query parentQuery, Calendar dateLog) {

        if (null == parentQuery || !parentQuery.isValid()) {
            throw new IllegalArgumentException(
                    "Parent query must be a valid one");
        }

        if (null == dateLog) {
            throw new IllegalArgumentException("Log date can't be null");
        }

        if (dateLog.compareTo(Calendar.getInstance()) > -1) {
            throw new IllegalArgumentException("Log date must be in the past");
        }

        this.setQueryId(parentQuery.getQueryId());
        this.setLogDate(dateLog);
    }



    /**
     * Creates a new query aggregate log entry.
     * 
     * @param   parentQuery the query for which this log entry is created
     * @param   dateLog     the date of the log entry
     * @param   h24Stats    the aggregate stats for the 24-hour period
     * @param   slaStats    the aggregate stats for the SLA-defined period
     */
    public QueryAggregateLogEntry(Query parentQuery, Calendar dateLog,
                    AggregateStats h24Stats, AggregateStats slaStats) {
        this(parentQuery, dateLog);
        this.setH24Stats(h24Stats);
        this.setSlaStats(slaStats);
    }


    
    /**
     * Set the identifier of the parent query.
     * 
     * @param   newQueryId  the parent query identifier
     */
    private void setQueryId(long newQueryId) {

        if (1 > newQueryId) {
            
            throw new IllegalArgumentException(
                    "Identifier must be higher than 0");
            
        }

        this.queryId = newQueryId;
    }



    /**
     * Gets the identifier of the parent query.
     * 
     * @return  the parent query identifier
     */
    public long getQueryId() {
        return this.queryId;
    }



    /**
     * {@inheritDoc}
     */
    @Override
    public boolean equals(Object anObject) {

        if (anObject instanceof QueryAggregateLogEntry) {
            final QueryAggregateLogEntry that 
                = (QueryAggregateLogEntry) anObject;

            return (this.getQueryId() == that.getQueryId() 
                    && this.getLogDate() == that.getLogDate());
        }

        return false;
    }



    /**
     * {@inheritDoc}
     */
    @Override
    public int hashCode() {
        final HashCodeBuilder hashCodeBuilder 
            = new HashCodeBuilder(HashCodeConstants.SEED, 
                                  HashCodeConstants.MULTIPLIER);
    
        hashCodeBuilder.append(this.getQueryId());
        hashCodeBuilder.append(this.getLogDate());
        
        return hashCodeBuilder.toHashCode();
    }
}
