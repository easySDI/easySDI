/**
 * 
 */
package org.easysdi.proxy.security;

import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;
import java.util.Set;

import javax.sql.DataSource;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.easysdi.proxy.domain.SdiUser;
import org.easysdi.proxy.domain.SdiUserRoleOrganism;
import org.easysdi.proxy.domain.Users;
import org.easysdi.proxy.domain.UsersHome;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
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

/**
 * @author DEPTH SA
 *
 */
public class EasysdiProvider implements AuthenticationProvider,
		UserDetailsService {

	@Autowired
    private DataSource dataSource;
    @Autowired
    private CacheManager cacheManager;
    @Autowired
    private UsersHome usersHome;
    
    private Users user;
    private Cache userCache;
    private static final String HEX_DIGITS = "0123456789abcdef";
    
	public EasysdiProvider() {
		super();
	}

	/* (non-Javadoc)
	 * @see org.springframework.security.core.userdetails.UserDetailsService#loadUserByUsername(java.lang.String)
	 */
	public UserDetails loadUserByUsername(String username)
			throws UsernameNotFoundException, DataAccessException {
		user = usersHome.findByUserName(username);
		String[] passwordParts = user.getPassword().split(":");
	    String salt = (passwordParts.length > 1) ? passwordParts[1] : null;
		JoomlaUser joomlaUser = new JoomlaUser(user.getUsername(), user.getPassword(), salt, getAuthorities(user.getUsername()), true, true, true, user.getBlock() == 0);
		return joomlaUser;
	}
	
	private Collection<GrantedAuthority> getAuthorities(String username)  {
		Set<SdiUser> sdiUser = user.getSdiUsers();
		if(sdiUser.isEmpty())
			throw new UsernameNotFoundException("EasySDI User not found !");
		
		Iterator<SdiUser> it = sdiUser.iterator();
		Set<SdiUserRoleOrganism> result = null;
        while (it.hasNext()) {
             result = ((SdiUser) it.next()).getSdiUserRoleOrganismsMember();
        }
        
        List<GrantedAuthority> authList = new ArrayList<GrantedAuthority>();
        Iterator<SdiUserRoleOrganism> i = result.iterator();
        while (i.hasNext()){
        	authList.add(new GrantedAuthorityImpl(String.valueOf(((SdiUserRoleOrganism)i.next()).getSdiOrganism().getId())));
        }

		authList.add(0, new GrantedAuthorityImpl("proxy_user"));
		return authList;
    }
	

	/* (non-Javadoc)
	 * @see org.springframework.security.authentication.AuthenticationProvider#authenticate(org.springframework.security.core.Authentication)
	 */
	public Authentication authenticate(Authentication authentication)
			throws AuthenticationException {
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
		    	token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), authentication.getCredentials().toString(), user.getAuthorities());
		    } 
		    else if (authentication.getCredentials().toString().split(":")[0].equals(user.getPassword().split(":")[0])) {
		    	token = new UsernamePasswordAuthenticationToken(authentication.getPrincipal().toString(), authentication.getCredentials().toString(), user.getAuthorities());
		    } 
		    else {
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

	private static String toHexString(byte[] v) {
		StringBuffer sb = new StringBuffer(v.length * 2);
		for (int i = 0; i < v.length; i++) {
		    int b = v[i] & 0xFF;
		    sb.append(HEX_DIGITS.charAt(b >>> 4)).append(HEX_DIGITS.charAt(b & 0xF));
		}
		return sb.toString();
    }
	 
	 
	/* (non-Javadoc)
	 * @see org.springframework.security.authentication.AuthenticationProvider#supports(java.lang.Class)
	 */
	public boolean supports(@SuppressWarnings("rawtypes") Class authenticationClassName) {
		return true;
	}

	/**
	 * @return the dataSource
	 */
	public DataSource getDataSource() {
		return dataSource;
	}

	/**
	 * @param dataSource the dataSource to set
	 */
	public void setDataSource(DataSource dataSource) {
		this.dataSource = dataSource;
	}

	/**
	 * @return the cacheManager
	 */
	public CacheManager getCacheManager() {
		return cacheManager;
	}

	/**
	 * @param cacheManager the cacheManager to set
	 */
	public void setCacheManager(CacheManager cacheManager) {
		this.cacheManager = cacheManager;
	}

}
