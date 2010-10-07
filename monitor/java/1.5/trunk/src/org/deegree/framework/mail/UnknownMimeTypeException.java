//$HeadURL: svn+ssh://rbezema@svn.wald.intevation.org/deegree/base/branches/2.3_testing/src/org/deegree/framework/mail/UnknownMimeTypeException.java $
/*----------------------------------------------------------------------------
 This file is part of deegree, http://deegree.org/
 Copyright (C) 2001-2009 by:
   Department of Geography, University of Bonn
 and
   lat/lon GmbH

 This library is free software; you can redistribute it and/or modify it under
 the terms of the GNU Lesser General Public License as published by the Free
 Software Foundation; either version 2.1 of the License, or (at your option)
 any later version.
 This library is distributed in the hope that it will be useful, but WITHOUT
 ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 details.
 You should have received a copy of the GNU Lesser General Public License
 along with this library; if not, write to the Free Software Foundation, Inc.,
 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

 Contact information:

 lat/lon GmbH
 Aennchenstr. 19, 53177 Bonn
 Germany
 http://lat-lon.de/

 Department of Geography, University of Bonn
 Prof. Dr. Klaus Greve
 Postfach 1147, 53001 Bonn
 Germany
 http://www.geographie.uni-bonn.de/deegree/

 e-mail: info@deegree.org
----------------------------------------------------------------------------*/
package org.deegree.framework.mail;

/**
 * A UnknownMimetypeException is thrown if the MIME type is not supported.
 *
 * @author <a href="mailto:tfr@users.sourceforge.net">Torsten Friebe</A>
 *
 * @author last edited by: $Author: mschneider $
 *
 * @version $Revision: 18195 $, $Date: 2009-06-18 17:55:39 +0200 (Do, 18. Jun 2009) $
 */
public class UnknownMimeTypeException extends Exception {

    private static final long serialVersionUID = -7118350825074904139L;

    private String mimeType;

    /**
     * Creates a exception with the given message and MIME type
     */
    public UnknownMimeTypeException( String message, String mimeType ) {
        super( message + " : Unknown MIME Type :" + mimeType );
        this.mimeType = mimeType;
    }

    /**
     * @return the name of the unknown mime type
     *
     */
    public String getMimeType() {
        return this.mimeType;
    }

}
