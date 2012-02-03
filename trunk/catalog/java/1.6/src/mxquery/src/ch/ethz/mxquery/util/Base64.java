
package ch.ethz.mxquery.util;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * <p>Encodes and decodes to and from Base64 notation.</p>
 * <p>Homepage: <a href="http://iharder.net/base64">http://iharder.net/base64</a>.</p>
 *
 * <p>
 * I am placing this code in the Public Domain. Do with it as you will.
 * This software comes with no guarantees or warranties but with
 * plenty of well-wishing instead!
 * Please visit <a href="http://iharder.net/base64">http://iharder.net/base64</a>
 * periodically to check for updates or to contribute improvements.
 * </p>
 *
 * @author Robert Harder
 * @author rob@iharder.net
 * @version 2.2.1
 * modified for MXQuery by Rokas Tamosevicius  
 */
public class Base64
{
    
/* ********  P U B L I C   F I E L D S  ******** */    
    
    /** Don't break lines when encoding (violates strict Base64 specification) */
    public final static int DONT_BREAK_LINES = 8;
	
    
/* ********  P R I V A T E   F I E L D S  ******** */  
    
   /** Maximum line length (76) of Base64 output. */
    private final static int MAX_LINE_LENGTH = 76;
    
    
    /** The equals sign (=) as a byte. */
    private final static byte EQUALS_SIGN = (byte)'=';
    
    
    /** The new line character (\n) as a byte. */
    private final static byte NEW_LINE = (byte)'\n';
    
    
    /** Preferred encoding. */
    private final static String PREFERRED_ENCODING = "UTF-8";
    
	
    private final static byte WHITE_SPACE_ENC = -5; // Indicates white space in encoding
    private final static byte EQUALS_SIGN_ENC = -1; // Indicates equals sign in encoding
	
	
/* ********  S T A N D A R D   B A S E 6 4   A L P H A B E T  ******** */	
    
    /** The 64 valid Base64 values. */
    //private final static byte[] ALPHABET;
	/* Host platform might be something funny like EBCDIC, so we hardcode these values. */
	private final static byte[] _STANDARD_ALPHABET =
    {
        (byte)'A', (byte)'B', (byte)'C', (byte)'D', (byte)'E', (byte)'F', (byte)'G',
        (byte)'H', (byte)'I', (byte)'J', (byte)'K', (byte)'L', (byte)'M', (byte)'N',
        (byte)'O', (byte)'P', (byte)'Q', (byte)'R', (byte)'S', (byte)'T', (byte)'U', 
        (byte)'V', (byte)'W', (byte)'X', (byte)'Y', (byte)'Z',
        (byte)'a', (byte)'b', (byte)'c', (byte)'d', (byte)'e', (byte)'f', (byte)'g',
        (byte)'h', (byte)'i', (byte)'j', (byte)'k', (byte)'l', (byte)'m', (byte)'n',
        (byte)'o', (byte)'p', (byte)'q', (byte)'r', (byte)'s', (byte)'t', (byte)'u', 
        (byte)'v', (byte)'w', (byte)'x', (byte)'y', (byte)'z',
        (byte)'0', (byte)'1', (byte)'2', (byte)'3', (byte)'4', (byte)'5', 
        (byte)'6', (byte)'7', (byte)'8', (byte)'9', (byte)'+', (byte)'/'
    };
	
    
    /** 
     * Translates a Base64 value to either its 6-bit reconstruction value
     * or a negative number indicating some other meaning.
     **/
    private final static byte[] _STANDARD_DECODABET =
    {   
        -9,-9,-9,-9,-9,-9,-9,-9,-9,                 // Decimal  0 -  8
        -5,-5,                                      // Whitespace: Tab and Linefeed
        -9,-9,                                      // Decimal 11 - 12
        -5,                                         // Whitespace: Carriage Return
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 14 - 26
        -9,-9,-9,-9,-9,                             // Decimal 27 - 31
        -5,                                         // Whitespace: Space
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,              // Decimal 33 - 42
        62,                                         // Plus sign at decimal 43
        -9,-9,-9,                                   // Decimal 44 - 46
        63,                                         // Slash at decimal 47
        52,53,54,55,56,57,58,59,60,61,              // Numbers zero through nine
        -9,-9,-9,                                   // Decimal 58 - 60
        -1,                                         // Equals sign at decimal 61
        -9,-9,-9,                                      // Decimal 62 - 64
        0,1,2,3,4,5,6,7,8,9,10,11,12,13,            // Letters 'A' through 'N'
        14,15,16,17,18,19,20,21,22,23,24,25,        // Letters 'O' through 'Z'
        -9,-9,-9,-9,-9,-9,                          // Decimal 91 - 96
        26,27,28,29,30,31,32,33,34,35,36,37,38,     // Letters 'a' through 'm'
        39,40,41,42,43,44,45,46,47,48,49,50,51,     // Letters 'n' through 'z'
        -9,-9,-9,-9                                 // Decimal 123 - 126
        /*,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 127 - 139
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 140 - 152
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 153 - 165
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 166 - 178
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 179 - 191
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 192 - 204
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 205 - 217
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 218 - 230
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,     // Decimal 231 - 243
        -9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9,-9         // Decimal 244 - 255 */
    };
    
    /** Defeats instantiation. */
    private Base64(){}
    
    
/* ********  E N C O D I N G   M E T H O D S  ******** */    
    
    /**
     * <p>Encodes up to three bytes of the array <var>source</var>
     * and writes the resulting four Base64 bytes to <var>destination</var>.
     * The source and destination arrays can be manipulated
     * anywhere along their length by specifying 
     * <var>srcOffset</var> and <var>destOffset</var>.
     * This method does not check to make sure your arrays
     * are large enough to accomodate <var>srcOffset</var> + 3 for
     * the <var>source</var> array or <var>destOffset</var> + 4 for
     * the <var>destination</var> array.
     * The actual number of significant bytes in your array is
     * given by <var>numSigBytes</var>.</p>
	 * <p>This is the lowest level of the encoding methods with
	 * all possible parameters.</p>
     *
     * @param source the array to convert
     * @param srcOffset the index where conversion begins
     * @param numSigBytes the number of significant bytes in your array
     * @param destination the array to hold the conversion
     * @param destOffset the index where output will be put
     * @return the <var>destination</var> array
     * @since 1.3
     */
    private static byte[] encode3to4( 
     byte[] source, int srcOffset, int numSigBytes,
     byte[] destination, int destOffset )
    {
	
        //           1         2         3  
        // 01234567890123456789012345678901 Bit position
        // --------000000001111111122222222 Array position from threeBytes
        // --------|    ||    ||    ||    | Six bit groups to index ALPHABET
        //          >>18  >>12  >> 6  >> 0  Right shift necessary
        //                0x3f  0x3f  0x3f  Additional AND
        
        // Create buffer with zero-padding if there are only one or two
        // significant bytes passed in the array.
        // We have to shift left 24 in order to flush out the 1's that appear
        // when Java treats a value as negative that is cast from a byte to an int.
        int inBuff =   ( numSigBytes > 0 ? ((source[ srcOffset     ] << 24) >>>  8) : 0 )
                     | ( numSigBytes > 1 ? ((source[ srcOffset + 1 ] << 24) >>> 16) : 0 )
                     | ( numSigBytes > 2 ? ((source[ srcOffset + 2 ] << 24) >>> 24) : 0 );

        switch( numSigBytes )
        {
            case 3:
                destination[ destOffset     ] = _STANDARD_ALPHABET[ (inBuff >>> 18)        ];
                destination[ destOffset + 1 ] = _STANDARD_ALPHABET[ (inBuff >>> 12) & 0x3f ];
                destination[ destOffset + 2 ] = _STANDARD_ALPHABET[ (inBuff >>>  6) & 0x3f ];
                destination[ destOffset + 3 ] = _STANDARD_ALPHABET[ (inBuff       ) & 0x3f ];
                return destination;
                
            case 2:
                destination[ destOffset     ] = _STANDARD_ALPHABET[ (inBuff >>> 18)        ];
                destination[ destOffset + 1 ] = _STANDARD_ALPHABET[ (inBuff >>> 12) & 0x3f ];
                destination[ destOffset + 2 ] = _STANDARD_ALPHABET[ (inBuff >>>  6) & 0x3f ];
                destination[ destOffset + 3 ] = EQUALS_SIGN;
                return destination;
                
            case 1:
                destination[ destOffset     ] = _STANDARD_ALPHABET[ (inBuff >>> 18)        ];
                destination[ destOffset + 1 ] = _STANDARD_ALPHABET[ (inBuff >>> 12) & 0x3f ];
                destination[ destOffset + 2 ] = EQUALS_SIGN;
                destination[ destOffset + 3 ] = EQUALS_SIGN;
                return destination;
                
            default:
                return destination;
        }   // end switch
    }   // end encode3to4
    
    
    /**
     * Encodes a byte array into Base64 notation.
     *
     * @param source The data to convert
     * @since 1.4
     */
    public static String encodeBytes( byte[] source )
    {
        return encodeBytes( source, 0, source.length );
    }   // end encodeBytes
    

    /**
     * Encodes a byte array into Base64 notation.
     * <p>
     * Valid options:<pre>
     *   DONT_BREAK_LINES: don't break lines at 76 characters
     *     <i>Note: Technically, this makes your encoding non-compliant.</i>
     * </pre>
     * <p>
     * Example: <code>encodeBytes( myData, Base64.GZIP )</code> or
     * <p>
     * Example: <code>encodeBytes( myData, Base64.GZIP | Base64.DONT_BREAK_LINES )</code>
     *
     *
     * @param source The data to convert
     * @param off Offset in array where conversion should begin
     * @param len Length of data to convert
     * @since 2.0
     * @return the encoded content as string
     */
    public static String encodeBytes( byte[] source, int off, int len )
    {
        
            // DON'T BREAK LINES
            boolean breakLines = false;
            
            int    len43   = len * 4 / 3;
            byte[] outBuff = new byte[   ( len43 )                      // Main 4:3
                                       + ( (len % 3) > 0 ? 4 : 0 )      // Account for padding
                                       + (breakLines ? ( len43 / MAX_LINE_LENGTH ) : 0) ]; // New lines      
            int d = 0;
            int e = 0;
            int len2 = len - 2;
            int lineLength = 0;
            for( ; d < len2; d+=3, e+=4 )
            {
                encode3to4( source, d+off, 3, outBuff, e );

                lineLength += 4;
                if( breakLines && lineLength == MAX_LINE_LENGTH )
                {   
                    outBuff[e+4] = NEW_LINE;
                    e++;
                    lineLength = 0;
                }   // end if: end of line
            }   // en dfor: each piece of array

            if( d < len )
            {
                encode3to4( source, d+off, len - d, outBuff, e );
                e += 4;
            }   // end if: some padding needed

            
            // Return value according to relevant encoding.
            try
            {
                return new String( outBuff, 0, e, PREFERRED_ENCODING );
            }   // end try
            catch (java.io.UnsupportedEncodingException uue)
            {
                return new String( outBuff, 0, e );
            }   // end catch
        
    }   // end encodeBytes
    

    
/* ********  D E C O D I N G   M E T H O D S  ******** */
    
    /**
     * Decodes four bytes from array <var>source</var>
     * and writes the resulting bytes (up to three of them)
     * to <var>destination</var>.
     * The source and destination arrays can be manipulated
     * anywhere along their length by specifying 
     * <var>srcOffset</var> and <var>destOffset</var>.
     * This method does not check to make sure your arrays
     * are large enough to accomodate <var>srcOffset</var> + 4 for
     * the <var>source</var> array or <var>destOffset</var> + 3 for
     * the <var>destination</var> array.
     * This method returns the actual number of bytes that 
     * were converted from the Base64 encoding.
	 * <p>This is the lowest level of the decoding methods with
	 * all possible parameters.</p>
     * 
     *
     * @param source the array to convert
     * @param srcOffset the index where conversion begins
     * @param destination the array to hold the conversion
     * @param destOffset the index where output will be put
     * @return the number of decoded bytes converted
     * @since 1.3
     */
    private static int decode4to3( byte[] source, int srcOffset, byte[] destination, int destOffset )
    {	 
	
        // Example: Dk==
        if( source[ srcOffset + 2] == EQUALS_SIGN )
        {
            // Two ways to do the same thing. Don't know which way I like best.
            //int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset    ] ] << 24 ) >>>  6 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1] ] << 24 ) >>> 12 );
            int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset    ] ] & 0xFF ) << 18 )
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1] ] & 0xFF ) << 12 );
            
            destination[ destOffset ] = (byte)( outBuff >>> 16 );
            return 1;
        }
        
        // Example: DkL=
        else if( source[ srcOffset + 3 ] == EQUALS_SIGN )
        {
            // Two ways to do the same thing. Don't know which way I like best.
            //int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset     ] ] << 24 ) >>>  6 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1 ] ] << 24 ) >>> 12 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 2 ] ] << 24 ) >>> 18 );
            int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset     ] ] & 0xFF ) << 18 )
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1 ] ] & 0xFF ) << 12 )
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 2 ] ] & 0xFF ) <<  6 );
            
            destination[ destOffset     ] = (byte)( outBuff >>> 16 );
            destination[ destOffset + 1 ] = (byte)( outBuff >>>  8 );
            return 2;
        }
        
        // Example: DkLE
        else
        {
           
            // Two ways to do the same thing. Don't know which way I like best.
            //int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset     ] ] << 24 ) >>>  6 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1 ] ] << 24 ) >>> 12 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 2 ] ] << 24 ) >>> 18 )
            //              | ( ( _STANDARD_DECODABET[ source[ srcOffset + 3 ] ] << 24 ) >>> 24 );
            int outBuff =   ( ( _STANDARD_DECODABET[ source[ srcOffset     ] ] & 0xFF ) << 18 )
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 1 ] ] & 0xFF ) << 12 )
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 2 ] ] & 0xFF ) <<  6)
                          | ( ( _STANDARD_DECODABET[ source[ srcOffset + 3 ] ] & 0xFF )      );

            
            destination[ destOffset     ] = (byte)( outBuff >> 16 );
            destination[ destOffset + 1 ] = (byte)( outBuff >>  8 );
            destination[ destOffset + 2 ] = (byte)( outBuff       );

            return 3;
//           // }catch( Exception e){
//                System.out.println(""+source[srcOffset]+ ": " + ( _STANDARD_DECODABET[ source[ srcOffset     ] ]  ) );
//                System.out.println(""+source[srcOffset+1]+  ": " + ( _STANDARD_DECODABET[ source[ srcOffset + 1 ] ]  ) );
//                System.out.println(""+source[srcOffset+2]+  ": " + ( _STANDARD_DECODABET[ source[ srcOffset + 2 ] ]  ) );
//                System.out.println(""+source[srcOffset+3]+  ": " + ( _STANDARD_DECODABET[ source[ srcOffset + 3 ] ]  ) );
//                return -1;
//            //}   // end catch
        }
    }   // end decodeToBytes
        
    
    /** Rule: if 3d byte is equal '=' then the 2nd byte should be equal to one of the symbols AQgw 
     *  http://www.w3.org/TR/xmlschema11-2/#base64Binary
     * */
    private static void checkPadded8(int byte3) throws MXQueryException {
    	switch(byte3){
	    	case 65://A
	    	case 81://Q
	    	case 103://g
	    	case 119://w
    		// do nothing
	    	break;
	    	default:
            	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, 
            			"Second byte in the Padded8 should be equal to one of the symbols AQgw", null );
    	}
    }
    
    
    /** Rule: if 4th byte is equal '=' then the 3rd byte should be equal to one of the symbols AEIMQUYcgkosw048 
     *  http://www.w3.org/TR/xmlschema11-2/#base64Binary
     * */
    private static void checkPadded16(int byte3) throws MXQueryException {
    	switch(byte3){
	    	case 65://A
	    	case 69://E
	    	case 73://I
	    	case 77://M
	    	case 81://Q
	    	case 85://U
	    	case 89://Y
	    	case 99://c
	    	case 103://g
	    	case 107://k
	    	case 111://o
	    	case 115://s
	    	case 119://w
	    	case 48://0
	    	case 52://4
	    	case 56://8
    		// do nothing
	    	break;
	    	default:
            	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, 
            			"Third byte in the Padded16 should be equal to one of the symbols AEIMQUYcgkosw048", null );
    	}
    }    
    /**
     * Very low-level access to decoding ASCII characters in
     * the form of a byte array. 
     * @param source The Base64 encoded data
     * @param off    The offset of where to begin decoding
     * @param len    The length of characters to decode
     * @return decoded data
     * @since 1.3
     */
    public static byte[] decode( byte[] source, int off, int len ) throws MXQueryException
    {
        int    len34   = len * 3 / 4;
        byte[] outBuff = new byte[ len34 ]; // Upper limit on size of output
        int    outBuffPosn = 0;
        
        byte[] b4        = new byte[4];
        int    b4Posn    = 0;
        int    i         = 0;
        byte   sbiCrop   = 0;
        byte   sbiDecode = 0;
            
//        //AQgw
//        
//        byte[] a = "AEIMQUYcgkosw048".getBytes();
//        for (int j =0; j < a.length; j++) {
//        	
//            int  asbiCrop = (byte)(a[j] & 0x7f);
//            int asbiDecode =_STANDARD_DECODABET[ sbiCrop ];
//            
////            System.out.println( a[j] );
//            System.out.println("case " + asbiCrop + "://" + "AEIMQUYcgkosw048".charAt(j) );
////            System.out.println( asbiDecode );
////            System.out.println("----------------");
//        }
        
        for( i = off; i < off+len; i++ )
        {
            sbiCrop = (byte)(source[i] & 0x7f); // Only the low seven bits
            sbiDecode = _STANDARD_DECODABET[ sbiCrop ];
            
            if( sbiDecode >= WHITE_SPACE_ENC ) // White space, Equals sign or better
            {
            
            	//--- validate all the constraints related to '='
            	if (sbiDecode == EQUALS_SIGN_ENC && b4Posn < 2)
                	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, 
                			"Bad Base64 input character at " + i + ": " + source[i] + "(decimal)", null );
            	
            	if (sbiDecode == EQUALS_SIGN_ENC && b4Posn == 2) {
            		checkPadded8( b4[b4Posn-1] );
            	}
            	//---
            	
                if( sbiDecode >= EQUALS_SIGN_ENC )
                {
                	b4[ b4Posn++ ] = sbiCrop;
                    if( b4Posn > 3 )
                    {
                    	//--- validate all the constraints related to '='
                    	// if pos 3 is '=', then pos 4 must be '=' too
                    	if (sbiDecode != EQUALS_SIGN_ENC && _STANDARD_DECODABET[ b4[b4Posn-2] ] == EQUALS_SIGN_ENC)
                        	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, 
                        			"Base 64 Padded8 part should be of form ??== instead of ??=?", null );

                    	if(sbiDecode == EQUALS_SIGN_ENC && _STANDARD_DECODABET[ b4[b4Posn-2] ] != EQUALS_SIGN_ENC) {
                    		checkPadded16( b4[b4Posn-2] );
                    	}
                    	//--
                    	
                        outBuffPosn += decode4to3( b4, 0, outBuff, outBuffPosn );
                        b4Posn = 0;
                        
                        // If that was the equals sign, break out of 'for' loop
                        if( sbiCrop == EQUALS_SIGN )
                            break;
                    }   // end if: quartet built
                    
                }   // end if: equals sign or better
                
            }   // end if: white space, equals sign or better
            else
            {
            	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, 
            			"Bad Base64 input character at " + i + ": " + source[i] + "(decimal)", null );
            }   // end else: 
        }   // each input character

        if (b4Posn != 0)
        	throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Cannot decode base64Binary value " + new String(source), null );
        
        
        byte[] out = new byte[ outBuffPosn ];
        System.arraycopy( outBuff, 0, out, 0, outBuffPosn ); 
        return out;
    }   // end decode
    
	
    /**
     * Decodes data from Base64 notation
     *
     * @param s the string to decode
     * @return the decoded data
     * @since 1.4
     */
    
    public static byte[] decode( String s ) throws MXQueryException
    {   
        byte[] bytes;
        try
        {
            bytes = s.getBytes( PREFERRED_ENCODING );
        }   // end try
        catch( java.io.UnsupportedEncodingException uee )
        {
            bytes = s.getBytes();
        }   // end catch
		//</change>
        
        bytes = decode( bytes, 0, bytes.length );
        return bytes;
    }   // end decode

              
}   // end class Base64
