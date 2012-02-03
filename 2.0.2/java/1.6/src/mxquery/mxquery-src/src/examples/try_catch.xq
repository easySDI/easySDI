(: Shows the new try/catch error recovery mechanism in XQuery 1.1
  
  The catch clause captures all error types, 
  and returns the error code and the description

:)
try {
    2 + "3"
} catch(*, $ecode, $desc) {
    string-join(($ecode, $desc), " ")
}
