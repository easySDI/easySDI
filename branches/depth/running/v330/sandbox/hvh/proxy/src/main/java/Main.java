import java.util.Date;

import org.easysdi.domain.ProxySdiUser;
import org.easysdi.domain.ProxySdiUserDao;
import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;


public class Main {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub

		ApplicationContext context = new ClassPathXmlApplicationContext(new String[]{"file:C:/Sources/Proxy330/proxy/src/main/webapp/WEB-INF/spring/hibernate-config.xml"});
		
		ProxySdiUserDao pdao = (ProxySdiUserDao)context.getBean("proxySdiUser");
		
		ProxySdiUser u = new ProxySdiUser();
		
		//u.setId(1);
		u.setGuid("f1515566-8530-4fde-894d-bd7c4002b8de");
		u.setCreatedBy(1);
		u.setCreated(new Date());
		u.setNotificationrequesttreatment(true);
		u.setOrdering(1);
		u.setState(true);
		u.setCheckedOutTime(new Date());
		u.setCheckedOut(1);
		
		pdao.save(u);
		System.out.println(u.getId());
		
	}

}
