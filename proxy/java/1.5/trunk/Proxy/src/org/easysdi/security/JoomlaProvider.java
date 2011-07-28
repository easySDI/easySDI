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

import java.lang.reflect.Method;
import java.security.NoSuchAlgorithmException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Collection;
import java.util.List;

import javax.sql.DataSource;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.dao.DataRetrievalFailureException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.simple.ParameterizedRowMapper;
import org.springframework.security.authentication.AuthenticationProvider;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.authority.GrantedAuthorityImpl;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;

public class JoomlaProvider implements AuthenticationProvider, UserDetailsService {

    private DataSource dataSource;

    @Autowired
    private CacheManager cacheManager;

    private Cache userCache;

    public JdbcTemplate sjt = null;

    public JoomlaProvider() {
	super();
    }

    public DataSource getDataSource() {
	return dataSource;
    }

    public void setDataSource(DataSource dataSource) {
	this.dataSource = dataSource;
	sjt = new JdbcTemplate(dataSource);
    }

    private String prefix;

    public String getPrefix() {
	return prefix;
    }

    public void setPrefix(String prefix) {
	this.prefix = prefix;
    }

    private String version;

    public void setVersion(String version) {
	if (version != null)
	    version = version.replaceAll("\\.", "");

	this.version = version;
    }

    public String getVersion() {
	return version;
    }

    public UserDetails loadUserByUsername(String username) {
	String sql="";
	JoomlaUser user = null;
	if(getVersion()== null || Integer.parseInt(getVersion())<200)
	{
	    sql = "select u.* from " + getPrefix() + "easysdi_community_partner p join " + getPrefix()
	    + "users u on (p.user_id = u.id) where username = ? and block = 0";
	}
	else if (Integer.parseInt(getVersion()) >= 200 && Integer.parseInt(getVersion()) < 300)
	{
	    sql = "select u.* from " + getPrefix() + "sdi_account a join " + getPrefix()
	    + "users u on (a.user_id = u.id) where username = ? and block = 0";
	}
	else
	{
	    throw new DataRetrievalFailureException ("Requested Proxy version is not supported");
	}

	try {
	    user = sjt.queryForObject(sql, new UserMapper(), username);
	}catch (DataAccessException e) {
	} 
	return user;
    }

    private Collection<GrantedAuthority> getAuthorities(String username)  {
	Method m;
	String sql = "";
	if(getVersion()== null || Integer.parseInt(getVersion())<200)
	{
	    sql = "SELECT " + getPrefix() + "easysdi_community_role.role_name as role FROM " + getPrefix() + "easysdi_community_role Inner Join "
	    + getPrefix() + "easysdi_map_profile_role ON " + getPrefix() + "easysdi_map_profile_role.id_role = " + getPrefix()
	    + "easysdi_community_role.role_id Inner Join " + getPrefix() + "easysdi_community_profile ON " + getPrefix()
	    + "easysdi_map_profile_role.id_prof = " + getPrefix() + "easysdi_community_profile.profile_id Inner Join " + getPrefix()
	    + "easysdi_community_partner_profile ON " + getPrefix() + "easysdi_community_partner_profile.profile_id = " + getPrefix()
	    + "easysdi_community_profile.profile_id Inner Join " + getPrefix() + "easysdi_community_partner ON " + getPrefix()
	    + "easysdi_community_partner_profile.partner_id = " + getPrefix() + "easysdi_community_partner.partner_id Inner Join " + getPrefix()
	    + "users ON " + getPrefix() + "easysdi_community_partner.user_id = " + getPrefix() + "users.id WHERE " + getPrefix() + "users.username =  ? "
	    + "UNION SELECT profile_translation as role from " + getPrefix() + "easysdi_community_profile r join " + getPrefix()
	    + "easysdi_community_partner_profile pr on (pr.profile_id = r.profile_id) join " + getPrefix()
	    + "easysdi_community_partner p on (p.partner_id = pr.partner_id) join " + getPrefix() + "users u on (u.id = p.user_id) where u.username = ?";
	}
	else if (Integer.parseInt(getVersion()) >= 200 && Integer.parseInt(getVersion()) < 300)
	{
	    sql = "SELECT " + getPrefix()+ "sdi_list_role.code as role FROM " + getPrefix() + "sdi_list_role"
	    + " Inner Join "+ getPrefix()+ "sdi_actor ON " + getPrefix()+ "sdi_actor.role_id = " + getPrefix() + "sdi_list_role.id" 
	    + " Inner Join " + getPrefix()+ "sdi_account ON " + getPrefix() + "sdi_account.id = " + getPrefix()+ "sdi_actor.account_id"
	    + " Inner Join " + getPrefix()+ "users ON " + getPrefix() + "sdi_account.user_id = " + getPrefix() + "users.id"
	    + " WHERE " + getPrefix() + "users.username =  ? "
	    + "UNION SELECT r.code as role from " + getPrefix() + "sdi_accountprofile r "
	    + " join " + getPrefix() + "sdi_account_accountprofile pr on (pr.accountprofile_id = r.id) "
	    + " join " + getPrefix() + "sdi_account p on (p.id = pr.account_id)"
	    + " join " + getPrefix() + "users u on (u.id = p.user_id) where u.username = ?";
	}
	else
	{

	}
	List<GrantedAuthority> authList = sjt.query(sql, new ParameterizedRowMapper<GrantedAuthority>() {
	    public GrantedAuthority mapRow(ResultSet rs, int rowNum) throws SQLException {
		//				return (rs.getString("role") != null) ? new GrantedAuthorityImpl(rs.getString("role")) : null;
		return new GrantedAuthorityImpl(rs.getString("role"));
	    }
	}, username, username);
	authList.add(0, new GrantedAuthorityImpl("proxy_user"));
	return authList;
    }

    private class UserMapper implements ParameterizedRowMapper<JoomlaUser> {

	public JoomlaUser mapRow(ResultSet rs, int arg1) throws SQLException {
	    String[] passwordParts = rs.getString("password").split(":");
	    String salt = (passwordParts.length > 1) ? passwordParts[1] : null;
	    return new JoomlaUser(rs.getString("username"), rs.getString("password"), salt, getAuthorities(rs.getString("username")), true, true, true, !rs
		    .getBoolean("block"));
	}
    }

    public Authentication authenticate(Authentication authentication) throws AuthenticationException {
	JoomlaUser user = null;
	UsernamePasswordAuthenticationToken token = null;
	userCache = cacheManager.getCache("userCache");
	Element userElement = userCache.get(authentication.getPrincipal());
	user = (JoomlaUser) ((userElement != null) ? (userElement.getValue()) : null);
	if (user == null)
	{
	    user = (JoomlaUser) loadUserByUsername(authentication.getPrincipal().toString());
	}
	if (user == null)
	{
	    throw new UsernameNotFoundException("Username not found !");
	}
	else if (authentication.getPrincipal().equals(user.getUsername())) {
	    if (authentication.getCredentials().equals(user.getPassword())) {
		token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), authentication.getCredentials().toString(), user
			.getAuthorities());
	    } else if (authentication.getCredentials().toString().split(":")[0].equals(user.getPassword().split(":")[0])) {
		token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), authentication.getCredentials().toString(), user
			.getAuthorities());
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
		    } else {
			if (userElement != null)
			    userCache.remove(authentication.getPrincipal());
			throw new BadCredentialsException("username or password incorrect !");
		    }
		} catch (NoSuchAlgorithmException e) {

		}
	    }
	} else {
	    if (userElement != null)
		userCache.remove(authentication.getPrincipal());
	}
	userElement = new Element(authentication.getPrincipal(), user);
	userCache.put(userElement);

	return token;
    }

    public boolean supports(Class authenticationClassName) {
	return true;
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

    public JdbcTemplate getSjt() {
	return sjt;
    }
}
