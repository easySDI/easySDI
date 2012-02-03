package org.easysdi.publish.security;

import java.util.Collection;

import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;

/**
 * 
 * @author admin
 */
public class JoomlaUser implements UserDetails {

	/**
	 * 
	 */
	private static final long serialVersionUID = -2336166921935589321L;

	private String username = null;
	private String password = null;
	private String salt = null;
	private Collection<GrantedAuthority> authorities = null;
	private Boolean isAccountNonExpired = false;
	private Boolean isAccountNonLocked = false;
	private Boolean isCredentialsNonExpired = false;
	private Boolean isEnabled = false;

	public JoomlaUser(String username, String password, String salt, Collection<GrantedAuthority> authorities, Boolean isAccountNonExpired,
			Boolean isAccountNonLocked, Boolean isCredentialsNonExpired, Boolean isEnabled) {
		this.username = username;
		this.password = password;
		this.authorities = authorities;
		this.isAccountNonExpired = isAccountNonExpired;
		this.isAccountNonLocked = isAccountNonLocked;
		this.isCredentialsNonExpired = isCredentialsNonExpired;
		this.isEnabled = isEnabled;
		this.salt = salt;
	}

	public String getUsername() {
		return username;
	}

	public String getPassword() {
		return password;
	}

	public String getSalt() {
		return salt;
	}

	public Collection<GrantedAuthority> getAuthorities() {
		return authorities;
	}

	public boolean isAccountNonExpired() {
		return isAccountNonExpired;
	}

	public boolean isAccountNonLocked() {
		return isAccountNonLocked;
	}

	public boolean isCredentialsNonExpired() {
		return isCredentialsNonExpired;
	}

	public boolean isEnabled() {
		return isEnabled;
	}
}