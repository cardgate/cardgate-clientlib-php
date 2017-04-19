<?php
try {

	if ( FALSE == @include 'init.php' ) {
		die( 'init.php missing - copy or rename the init.example.php to init.php and configure it with your account details' );
	}

	$aIssuers = $oCardGate->methods()->get( cardgate\api\Method::IDEAL )->getIssuers();

	echo '<form method="post" action="1-payment.php">';
	echo '<input type="hidden" name="option" value="ideal">';
	echo 'Select your bank: <select name="issuer">';

	foreach( $aIssuers as $aIssuer ) {
		echo '<option value="' . $aIssuer['id'] . '">' . $aIssuer['name'] . '</option>';
	}

	echo '<option value="">or select later</option></select>';
	echo '<button>Submit</button>';
	echo '</form>';

} catch ( cardgate\api\Exception $oException_ ) {
	echo htmlspecialchars( $oException_->getMessage() );
}
