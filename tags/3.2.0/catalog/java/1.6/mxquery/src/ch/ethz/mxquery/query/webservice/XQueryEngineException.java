package ch.ethz.mxquery.query.webservice;


class XQueryEngineException extends Exception
{
  /**
	 * 
	 */
	private static final long serialVersionUID = -2847140366856169209L;
String errorMessage;

//----------------------------------------------
// Default constructor - initializes instance variable to unknown

  public XQueryEngineException()
  {
    super();             // call superclass constructor
    errorMessage = "unknown";
  }
  

//-----------------------------------------------
// Constructor receives some kind of message that is saved in an instance variable.

  public XQueryEngineException(String err)
  {
    super(err);     // call super class constructor
    errorMessage = err;  // save message
  }
  

//------------------------------------------------  
// public method, callable by exception catcher. It returns the error message.

  public String getError()
  {
    return errorMessage;
  }
}
  
