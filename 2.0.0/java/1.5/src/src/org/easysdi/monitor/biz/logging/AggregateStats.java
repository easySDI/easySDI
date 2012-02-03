package org.easysdi.monitor.biz.logging;

/**
 * Holds daily aggregate availability statistics for a period.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public class AggregateStats {

    private float availability;
    private float meanRespTime;
    private int   nbBizErrors;
    private int   nbConnErrors;
    
    
    
    /**
     * Creates a new set of aggregate stats.
     */
    public AggregateStats() {
        
    }



    /**
     * Defines the percentage of availaibility time in the period.
     * 
     * @param   newAvailability the availability percentage
     */
    public void setAvailability(float newAvailability) {
        this.availability = newAvailability;
    }



    /**
     * Gets the percentage of availaibility time in the period.
     * 
     * @return the availability percentage
     */
    public float getAvailability() {
        return this.availability;
    }



    /**
     * Defines the average time that the job took to respond.
     * 
     * @param   newMeanRespTime the mean response time in seconds
     */
    public void setMeanRespTime(float newMeanRespTime) {
        this.meanRespTime = newMeanRespTime;
    }



    /**
     *  Gets the average time that the job took to respond.
     *  
     * @return  the mean response time in seconds
     */
    public float getMeanRespTime() {
        return this.meanRespTime;
    }



    /**
     * Defines how many business errors occurred in the period.
     * 
     * @param   newNbBizErrors  the number of business errors
     */
    public void setNbBizErrors(int newNbBizErrors) {
        this.nbBizErrors = newNbBizErrors;
    }



    /**
     * Gets how many business errors occurred in the period.
     * 
     * @return the number of business errors
     */
    public int getNbBizErrors() {
        return this.nbBizErrors;
    }



    /**
     * Defines how many connection errors occurred in the period.
     * 
     * @param   newNbConnErrors the number of connection errors
     */
    public void setNbConnErrors(int newNbConnErrors) {
        this.nbConnErrors = newNbConnErrors;
    }



    /**
     * Gets how many connection errors occurred in the period.
     * 
     * @return the number of connection errors
     */
    public int getNbConnErrors() {
        return this.nbConnErrors;
    }

}
