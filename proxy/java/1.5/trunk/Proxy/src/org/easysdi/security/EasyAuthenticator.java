package org.easysdi.security;

import java.net.Authenticator;
import java.net.PasswordAuthentication;

public class EasyAuthenticator extends Authenticator {

    private String login;
    private String password;
    private static EasyAuthenticator easyAuthenticator;

    public EasyAuthenticator() {
	super();
    }

    public EasyAuthenticator(String login, String password) {
	super();
	this.login = login;
	this.password = password;
    }

    @Override
    protected PasswordAuthentication getPasswordAuthentication() {
	return new PasswordAuthentication(login, password.toCharArray());
    }

    public static void setCredientials(String login, String password) {
	Authenticator.setDefault(getEasyAuthenticator(login, password));
    }

    private static EasyAuthenticator getEasyAuthenticator(String login, String password) {
	if (easyAuthenticator == null)
	    easyAuthenticator = new EasyAuthenticator();
	easyAuthenticator.login = login;
	easyAuthenticator.password = password;
	return easyAuthenticator;

    }
}
