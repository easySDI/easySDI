package org.easysdi.publish.diffusion;

public class ExecuteResponse {
	
	private Integer httpCode = null;
	private String message = null;
	private String body = null;
	
	public void setMessage(String message) {
		this.message = message;
	}
	public String getMessage() {
		return message;
	}
	public void setHttpCode(Integer httpCode) {
		this.httpCode = httpCode;
	}
	public Integer getHttpCode() {
		return httpCode;
	}
	public void setBody(String body) {
		this.body = body;
	}
	public String getBody() {
		return body;
	}
}
