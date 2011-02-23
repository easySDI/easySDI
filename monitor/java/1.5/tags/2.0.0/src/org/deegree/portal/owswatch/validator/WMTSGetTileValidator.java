package org.deegree.portal.owswatch.validator;

import java.io.InputStream;
import java.io.Serializable;

import javax.imageio.ImageIO;

import org.apache.commons.httpclient.HttpMethodBase;
import org.deegree.framework.util.StringTools;
import org.deegree.portal.owswatch.Status;
import org.deegree.portal.owswatch.ValidatorResponse;

public class WMTSGetTileValidator extends AbstractValidator implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 6010956771613989911L;

    /*
     * (non-Javadoc)
     *
     * @see org.deegree.portal.owswatch.validator.AbstractValidator#validateAnswer(org.apache.commons.httpclient.HttpMethodBase,
     *      int)
     */
    @Override
    protected ValidatorResponse processAnswer( HttpMethodBase method ) {
        String contentType = method.getResponseHeader( "Content-Type" ).getValue();
        String lastMessage = null;
        Status status = null;

        if ( !contentType.contains( "image" ) ) {
            if ( !contentType.contains( "xml" ) ) {
                status = Status.RESULT_STATE_UNEXPECTED_CONTENT;
                lastMessage = StringTools.concat( 100, "Error: Response Content is ", contentType, " not image" );
                return new ValidatorResponse( lastMessage, status );
            } else {
                return validateXmlServiceException( method );
            }
        }

        try {
            InputStream stream = copyStream( method.getResponseBodyAsStream() );
            stream.reset();
            ImageIO.read( stream );
            status = Status.RESULT_STATE_AVAILABLE;
            lastMessage = status.getStatusMessage();
            return new ValidatorResponse( lastMessage, status );
        } catch ( Exception e ) {
            status = Status.RESULT_STATE_SERVICE_UNAVAILABLE;
            lastMessage = e.getLocalizedMessage();
            return new ValidatorResponse( lastMessage, status );
        }
    }

}
