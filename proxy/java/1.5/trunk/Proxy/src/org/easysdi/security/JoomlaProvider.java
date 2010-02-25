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
package org.easysdi.security;

import java.security.NoSuchAlgorithmException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import javax.sql.DataSource;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.namedparam.MapSqlParameterSource;
import org.springframework.jdbc.core.simple.ParameterizedRowMapper;
import org.springframework.jdbc.core.simple.SimpleJdbcTemplate;
import org.springframework.security.Authentication;
import org.springframework.security.AuthenticationException;
import org.springframework.security.BadCredentialsException;
import org.springframework.security.GrantedAuthority;
import org.springframework.security.GrantedAuthorityImpl;
import org.springframework.security.providers.UsernamePasswordAuthenticationToken;
import org.springframework.security.providers.dao.AbstractUserDetailsAuthenticationProvider;
import org.springframework.security.userdetails.UserDetails;
import org.springframework.security.userdetails.UsernameNotFoundException;

public class JoomlaProvider extends AbstractUserDetailsAuthenticationProvider {

    @Autowired
    private DataSource dataSource;

    private String prefix;

    public String getPrefix() {
	return prefix;
    }

    public void setPrefix(String prefix) {
	this.prefix = prefix;
    }

    private JoomlaUser loadUserByUsername(String username) throws BadCredentialsException {
	String sql = "select u.* from " + getPrefix() + "easysdi_community_partner p join " + getPrefix() + "users u on (p.user_id = u.id) where username = :username and block = 0";
	MapSqlParameterSource source = new MapSqlParameterSource();
	source.addValue("username", username);
	SimpleJdbcTemplate sjt = new SimpleJdbcTemplate(dataSource);
	JoomlaUser user = null;
	    user = sjt.queryForObject(sql, new UserMapper(), source);
//	if (user == null) {
//	    user = new JoomlaUser("spring2a2d595e6ed9a0b24f027f2b63b134d6", "anonymous", null, new GrantedAuthority[] { new GrantedAuthorityImpl("anonymous") }, true, true, true, true);
//	}

	return user;
    }

    private GrantedAuthority[] getAuthorities(String username) {
	String sql = "select profile_code as role from " + getPrefix() + "easysdi_community_profile r join " + getPrefix()
		+ "easysdi_community_partner_profile pr on (pr.profile_id = r.profile_id) join " + getPrefix() + "easysdi_community_partner p on (p.partner_id = pr.partner_id) join " + getPrefix()
		+ "users u on (u.id = p.user_id) where u.username = ?";
	SimpleJdbcTemplate sjt = new SimpleJdbcTemplate(dataSource);
	List<GrantedAuthority> authList = sjt.query(sql, new ParameterizedRowMapper<GrantedAuthority>() {

	    public GrantedAuthority mapRow(ResultSet rs, int rowNum) throws SQLException {
		return new GrantedAuthorityImpl(rs.getString("role"));
	    }
	}, username);
	authList.add(0, new GrantedAuthorityImpl("proxy_user"));
	return authList.toArray(new GrantedAuthority[] {});
    }

    private class UserMapper implements ParameterizedRowMapper<JoomlaUser> {

	public JoomlaUser mapRow(ResultSet rs, int arg1) throws SQLException {
	    String[] passwordParts = rs.getString("password").split(":");
	    String password = passwordParts[0];
	    String salt = (passwordParts.length > 1) ? passwordParts[1] : null;
	    return new JoomlaUser(rs.getString("username"), rs.getString("password"), salt, getAuthorities(rs.getString("username")), true, true, true, !rs.getBoolean("block"));

	}
    }

    public Authentication authenticate(Authentication authentication) throws AuthenticationException {
	UsernamePasswordAuthenticationToken token = null;
	JoomlaUser user = loadUserByUsername(authentication.getPrincipal().toString());
	if (user == null)
	    throw new UsernameNotFoundException("Username not found !");
	else if (authentication.getPrincipal().equals(user.getUsername())) {
	    if (authentication.getCredentials().equals(user.getPassword())) {
		token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), authentication.getCredentials().toString(), user.getAuthorities());
	    } else {
		java.security.MessageDigest msgDigest;
		try {
		    msgDigest = java.security.MessageDigest.getInstance("MD5");
		    msgDigest.update(authentication.getCredentials().toString().getBytes());
		    if (user.getSalt() != null)
			msgDigest.update(user.getSalt().getBytes());
		    StringBuffer joomlaPasswordBuilder = new StringBuffer();
		    byte[] digest = msgDigest.digest();
		    joomlaPasswordBuilder.append(toHexString(digest));
		    if (user.getSalt() != null) {
			joomlaPasswordBuilder.append(":");
			joomlaPasswordBuilder.append(user.getSalt());
		    }
		    String joomlaPassword = joomlaPasswordBuilder.toString();

		    if (joomlaPassword.equals(user.getPassword())) {
			token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), joomlaPassword, user.getAuthorities());
		    } else
			throw new BadCredentialsException("username or password incorrect !");
		} catch (NoSuchAlgorithmException e) {

		}
	    }
	}
	return token;
    }

    public boolean supports(Class authenticationClassName) {
	return true;
    }

    @Override
    protected void additionalAuthenticationChecks(UserDetails userDetails, UsernamePasswordAuthenticationToken authentication) throws AuthenticationException {
    }

    @Override
    protected UserDetails retrieveUser(String username, UsernamePasswordAuthenticationToken authentication) throws AuthenticationException {
	UserDetails user = loadUserByUsername(username);
	return user;
    }

    private static final String HEX_DIGITS = "0123456789abcdef";

    private static String toHexString(byte[] v) {

	StringBuffer sb = new StringBuffer(v.length * 2);
	for (int i = 0; i < v.length; i++) {
	    int b = v[i] & 0xFF;
	    sb.append(HEX_DIGITS.charAt(b >>> 4)).append(HEX_DIGITS.charAt(b & 0xF));
	}
	return sb.toString();
    }
}
