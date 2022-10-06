<?php
session_start();

require_once('../../bootstrap.php');
require_once('../Gateway/OrchardPaymentGateway.php');

use Src\Controller\OrchardPaymentGateway;

if (isset($_SESSION['step1Done']) && isset($_SESSION['step2Done']) && isset($_SESSION['step3Done']) && isset($_SESSION['step4Done']) && isset($_SESSION['step5Done']) && isset($_SESSION['step6Done']) && isset($_SESSION['step7Done'])) {
    if ($_SESSION['step1Done'] == true && $_SESSION['step2Done'] == true && $_SESSION['step3Done'] == true && $_SESSION['step4Done'] == true && $_SESSION['step5Done'] == true && $_SESSION['step6Done'] == true && $_SESSION['step7Done'] == true) {

        $form_price = $_SESSION["step6"]["amount"];
        $callback_url = "https://localhost/rmu_admissions/purchase/purchase_confirm.php";
        $trans_id = time();
        $network = $_SESSION["step7"]["momo_agent"];
        $landing_page = "https://localhost/rmu_admissions/purchase/payment-checkpoint.php";
        $service_id = 2216;

        $payload = array(
            "amount" => $form_price,
            "callback_url" => $callback_url,
            "customer_number" => "0554603299",
            "exttrid" => $trans_id,
            "nw" => $network,
            "reference" => "Test payment",
            "service_id" => $service_id,
            "trans_type" => "CTM",
            "nickname" => "RMU Admissions",
            "landing_page" => $landing_page,
            "ts" => "2022-10-05 07:21:43",
            "payment_mode" => "CRM",
            "currency_code" => "GHS",
            "currency_val" => "233"
        );

        $payload = json_encode($payload);
        $client_id = getenv('ORCHARD_CLIENT');
        $client_secret = getenv('ORCHARD_SECRET');
        $signature = hash_hmac("sha256", $payload, $client_secret);

        $secretKey = $client_id . ":" . $signature;/**/
        $payUrl = "https://orchard-api.anmgw.com/third_party_request";
        $request = 'POST';

        $pay = new OrchardPaymentGateway($secretKey, $payUrl, $request, $payload);
        $response = $pay->initiatePayment();
        echo $response;
        /*if ($response->status == 'success') {
            //$_SESSION['processing'] = true;
            header("Location: " . $response->data->link);
        } else {
            echo 'Payment processing failed!';
            //5531886652142950  09/32   564     3310    12345
            //5399838383838381	470	3310	10/31	12345
            //4187427415564246	828	3310	09/32	12345

            // Insufficient funds: 5258585922666506	883	3310	09/31	12345
            // Incorrect PIN	5399834697894723	883	3310	09/31	12345
        }*/
    }
}