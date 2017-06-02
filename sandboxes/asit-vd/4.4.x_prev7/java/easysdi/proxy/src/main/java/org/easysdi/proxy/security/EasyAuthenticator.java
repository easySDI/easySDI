/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package org.easysdi.proxy.security;

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
