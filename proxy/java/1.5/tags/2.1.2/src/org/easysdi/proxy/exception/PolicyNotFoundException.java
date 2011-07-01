package org.easysdi.proxy.exception;

public class PolicyNotFoundException extends RuntimeException{

	private static final long serialVersionUID = 4979269297236189882L;
	public static final String NO_POLICY_FOUND = "No policy found";
	
	public PolicyNotFoundException (String message)
	{
		super(message);
	}
}
