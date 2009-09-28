/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.easysdi.security;

import org.springframework.security.GrantedAuthority;
import org.springframework.security.userdetails.UserDetails;

/**
 * 
 * @author admin
 */
public class JoomlaUser implements UserDetails {

    private String username = null;
    private String password = null;
    private String salt = null;
    private GrantedAuthority[] authorities = null;
    private Boolean isAccountNonExpired = false;
    private Boolean isAccountNonLocked = false;
    private Boolean isCredentialsNonExpired = false;
    private Boolean isEnabled = false;

    public JoomlaUser(String username, String password, String salt, GrantedAuthority[] authorities, Boolean isAccountNonExpired, Boolean isAccountNonLocked, Boolean isCredentialsNonExpired, Boolean isEnabled) {
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

    public GrantedAuthority[] getAuthorities() {
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
