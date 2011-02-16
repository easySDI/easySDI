package org.easysdi.proxy.ows;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

public interface OWSExceptionReport {

	public ByteArrayOutputStream generateExceptionReport (String errorMessage, String code, String locator) throws IOException;
}
