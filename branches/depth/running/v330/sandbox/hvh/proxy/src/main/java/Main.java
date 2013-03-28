import java.util.Date;

import org.easysdi.proxy.domain.SdiUser;
import org.easysdi.proxy.domain.SdiUserHome;
import org.easysdi.proxy.domain.Users;
import org.easysdi.proxy.domain.UsersHome;
import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;


public class Main {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub

		ApplicationContext context = new ClassPathXmlApplicationContext(new String[]{"file:C:/Sources/Proxy330/proxy/src/main/webapp/WEB-INF/spring/hibernate-config.xml"});
		
		SdiUserHome pdao = (SdiUserHome)context.getBean("SdiUser");
//		SdiUser u1 = pdao.findById(1);
//		System.out.println(u1.getId());
//		SdiUser u = pdao.findById(1);
//		Users user = u.getUsers();
		
		UsersHome users = (UsersHome) context.getBean("Users");
		Users uj = users.findById(601);
		
		SdiUser u = new SdiUser();
//		u.setId(1);
		u.setGuid("f1515566-8530-4fde-894d-bd7c4002b8de");
		u.setCreated_by(1);
		u.setCreated(new Date());
		u.setNotificationrequesttreatment(true);
		u.setOrdering(1);
		u.setState(1);
		u.setChecked_out_time(new Date());
		u.setChecked_out(1);
		u.setUsers(uj);
		pdao.save(u);
		
		System.out.println(u.getId());
		
	}

}
