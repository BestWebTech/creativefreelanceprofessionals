<?php 
function validate_data($pfSting){
	//var_dump($pfSting)
	 $args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $pfSting,
        'cookies' => array()
       );

    $post_validate = wp_remote_post('https://sandbox.payfast.co.za/eng/query/validate',$args);
    //var_dump($post_validate ['body']);
    if(strcasecmp($post_validate ['body'], 'VALID') !=0){
    	die('invalid post data');
    }
}

function validate_host(){
	$validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        );
         
        $validIps = array();
         
        foreach( $validHosts as $pfHostname )
        {
            $ips = gethostbynamel( $pfHostname );
         
            if( $ips !== false )
            {
                $validIps = array_merge( $validIps, $ips );
            }
        }
         
        // Remove duplicates
        $validIps = array_unique( $validIps );
         
        if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) )
        {
            die('Source IP not Valid');
        }
}

function validate_signature($return_type = ""){
	$payf_info = ae_get_option('payf');

	header( 'HTTP/1.0 200 OK' );
    flush();
    $pfData = $_POST;
    //creat an array on post value
    foreach( $pfData as $key => $val )
    {
        $pfData[$key] = stripslashes( $val );
    }

    $pfParamString="";
    foreach( $pfData as $key => $val )
    {
        if( $key != 'signature' )
        {
            $pfParamString .= $key .'='. urlencode( $val ) .'&';
        }
    }

    // Remove the last '&' from the parameter string
    $pfParamString = substr( $pfParamString, 0, -1 );
    $pfTempParamString = $pfParamString;
    // If a passphrase has been set in the PayFast Settings, then it needs to be included in the signature string.
    $passPhrase = $payf_info['salt_passphrase']; //You need to get this from a constant or stored in you website database
    /// !!!!!!!!!!!!!! If you testing your integration in the sandbox, the passPhrase needs to be empty !!!!!!!!!!!!
    if( !empty( $passPhrase ))
    {
        $pfTempParamString .= '&passphrase='.urlencode( $passPhrase );
    }
    $signature = md5( $pfTempParamString );
   
   
    if($signature!=$pfData['signature'])
    {
        die('Invalid Signature');
    }
    
    validate_data($pfParamString);
    return $pfData;
}
?>