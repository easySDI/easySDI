package org.easysdi.security;

import org.springframework.security.core.context.SecurityContextHolder;

public class CurrentUser {
	private String user;
	
	/*
	 * Change this to give back the user currently logged in
	 * in session or whatever
	 */
	
	public static String getCurrentPrincipal(){
		String user = null;
		try{
			user = SecurityContextHolder.getContext().getAuthentication().getPrincipal().toString();
		}catch(NullPointerException e){}
		
		return user;
	}
}
