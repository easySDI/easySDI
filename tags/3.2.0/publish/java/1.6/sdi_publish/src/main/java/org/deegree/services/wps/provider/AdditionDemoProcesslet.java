package org.deegree.services.wps.provider;

import org.deegree.services.wps.Processlet;
import org.deegree.services.wps.ProcessletException;
import org.deegree.services.wps.ProcessletExecutionInfo;
import org.deegree.services.wps.ProcessletInputs;
import org.deegree.services.wps.ProcessletOutputs;
import org.deegree.services.wps.input.LiteralInput;
import org.deegree.services.wps.output.LiteralOutput;
import org.easysdi.publish.biz.database.Geodatabase;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class AdditionDemoProcesslet implements Processlet {

    private static final Logger LOG = LoggerFactory.getLogger( AdditionDemoProcesslet.class );
	
    public void process( ProcessletInputs in, ProcessletOutputs out, ProcessletExecutionInfo info )
                            throws ProcessletException {
    	
    	//Read input values
        int summandA = Integer.parseInt( ( (LiteralInput) in.getParameter( "SummandA" ) ).getValue() );
        int summandB = Integer.parseInt( ( (LiteralInput) in.getParameter( "SummandB" ) ).getValue() );
        LiteralInput st = (LiteralInput) in.getParameter( "SleepTime" );
        LOG.debug( "- LiteratlInput: " + st );
              
        
        int sleepSeconds = determineSleepTime( st );
        // sleep a total of sleepSeconds (but update the percent completed information for every
        // percent)
        try {
            float sleepMillis = sleepSeconds * 1000;
            int sleepStep = (int) ( sleepMillis / 99.0f );
            LOG.debug( "Sleep step (millis): " + sleepStep );
            for ( int percentCompleted = 0; percentCompleted <= 99; percentCompleted++ ) {
                LOG.debug( "Setting percent completed: " + percentCompleted );
                info.setPercentCompleted( percentCompleted );
                Thread.sleep( sleepStep );
            }
        } catch ( InterruptedException e ) {
            throw new ProcessletException( e.getMessage() );
        }
        
        
        //compute the sum
        int sum = summandA + summandB;

        
        
        LiteralOutput output = (LiteralOutput) out.getParameter( "Sum" );
        output.setValue( "" + sum );
    }

    @Override
    public void destroy() {
        LOG.debug( "AdditionDemoProcesslet#destroy() called" );
    }

    @Override
    public void init() {
        LOG.debug( "AdditionDemoProcesslet#init() called" );
    }
    
    private int determineSleepTime( LiteralInput input ) {

        int seconds = -1;
        String uom = input.getUOM();

        LOG.debug( "dataType: " + input.getDataType() + ", uom: " + input.getUOM() );

        // NOTE: it is guaranteed (by the deegree WPS) that the UOM is always
        // one of the UOMs specified in the process definition
        if ( "seconds".equals( uom ) ) {
            LOG.debug( "Sleep time given in seconds" );
            seconds = (int) Double.parseDouble( input.getValue() );
        } else if ( "minutes".equals( uom ) ) {
            LOG.debug( "Sleep time given in minutes" );
            seconds = (int) ( Double.parseDouble( input.getValue() ) * 60 );
        }
        return seconds;
    }
}
