<?php

try {
    if (false == @include 'init.php') {
        die('init.php missing - copy or rename the init.example.php to init.php and configure it with your account details');
    }

    $details = [];
    $oTransaction = $oCardGate->transactions()->get('T26211231862', $details);

    if ($oTransaction->canRefund()) {
        $oTransaction->refund(50);
        echo '€ 0,50 of transaction ' . $oTransaction->getId() . ' refunded.';

        $remainder = 0;
        if (
            $oTransaction->canRefund($remainder)
            && $remainder > 0
        ) {
            echo ' € ' . number_format($remainder / 100, 2, ',', '.') . ' remaining.';
        }
    } else {
        echo 'Transaction ' . $oTransaction->getId() . ' can not be refunded.';
    }
} catch (cardgate\api\Exception $oException_) {
    echo htmlspecialchars($oException_->getMessage());
}
