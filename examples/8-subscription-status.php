<?php

try {
    if (false == @include 'init.php') {
        die('init.php missing - copy or rename the init.example.php to init.php and configure it with your account details');
    }

    if (! empty($_POST['subscription_id'])) {
        $oSubscription = $oCardGate->subscriptions()->get($_POST['subscription_id']);
        if (true == $oSubscription->changeStatus($_POST['action'])) {
            echo "<h3>succes</h3>";
        } else {
            echo "<h3>failed</h3>";
        }
    } else {
        echo '<h5>Change subscription</h5>';
        echo '<form method="post" id="2" action="8-subscription-status.php">';
        echo 'Subscription Id <input type="text" name="subscription_id" value=""> ';
        echo 'Action <select name="action">';

        foreach ([ 'reactivate' , 'suspend', 'cancel', 'deactivate' ] as $period) {
            echo '<option value="' . $period . '">' . ucfirst($period) . '</option>';
        }

        echo '</select> ';
        echo '<button>Submit</button>';
        echo '</form>';
    }
} catch (cardgate\api\Exception $oException_) {
    echo htmlspecialchars($oException_->getMessage());
}
