<?php

if (get_option('termsopt_sitewide') == 1) {
	$termspageid = get_option('termsopt_page');
}
elseif (get_option('termsopt_sitewide') <> 1) {
	if ($enabled == 1) {
		$termspageid = get_post_meta( $currentpostid, 'terms_selectedterms', true );
	}
	elseif ($enabled <> 1) {
		//nothing happens
		return;
	}
}
else {
	//nothing happens
	return;
}


if( (get_post_meta( $termspageid, 'terms_redirecturl', true )) != '' ) {
	$termsRedirectUrl = get_post_meta( $termspageid, 'terms_redirecturl', true );
}
elseif (get_option('termsopt_redirecturl') && get_option('termsopt_redirecturl') != '') {
	$termsRedirectUrl = get_option('termsopt_redirecturl');
}
else {
	$termsRedirectUrl = 'http://google.com';
}

if (get_option('termsopt_expiry') && get_option('termsopt_expiry') != '') {
	$sesslifetime = (get_option('termsopt_expiry')) * 60 * 60; // in seconds
}
else {
	$sesslifetime = 3 * 24 * 60 * 60; // 3 days (in seconds)
}

$terms_sessionid = 'ttbwtpsessionname'.$termspageid;

ini_set('session.name', $terms_sessionid);

ini_set('session.gc_maxlifetime', $sesslifetime);
session_set_cookie_params($sesslifetime);

@session_start();
$interception_string=ob_get_clean(); // get output


if (isset($_POST['SubmitAgree'])) {
	$_SESSION['terms_accepted'] = true;
}
else if (isset($_POST['SubmitDecline'])) {
    if (!headers_sent())
    {    
        header('Location: '.$termsRedirectUrl);
        exit;
        }
    else
        {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$termsRedirectUrl.'" />';
        echo '</noscript>'; exit;
    }
}


if(isset($_SESSION['terms_accepted'])) {
	echo $interception_string;
	//echo session_id();
	//echo $_SESSION['terms_accepted'];
	//echo session_name();
}
else {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if( is_plugin_active( 'wp-terms-popup-pro/index.php' ) ) {
		include ( ABSPATH . 'wp-content/plugins/wp-terms-popup-pro/terms-pro.php' );
	}
	else {
		include ('terms.php');
	}
}

?>