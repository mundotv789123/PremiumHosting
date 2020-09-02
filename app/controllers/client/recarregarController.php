<?php

namespace app\controllers\client;

use app\libs\customers\Authentication;
use app\libs\customers\Customer;
use app\libs\customers\Profile;
use app\libs\customers\Wallet;
use app\libs\financial\Transaction;
use app\libs\gateways\PayPal;
use app\libs\utils\JsonPretty;
use app\libs\utils\Mail;
use app\system\Config;
use app\system\Controller;

use PagSeguro\Configuration\Configure;
use PagSeguro\Domains\Requests\Payment;
use PagSeguro\Library;

class recarregarController extends Controller
{

    public $title;
    public $data = [];

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $this->title = "Recarregar";

        $this->view();
    }

    public function checkout()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $gateway = $_POST['gateway'];
        $amount = $_POST['value'];

        $reference = Authentication::id() . ":{$amount}:" . uniqid();

        if($gateway == 'paypal') {
            $paypal = new PayPal();

            $paypal->addItem('Recarga PremiumHost', floatval($amount), 1);
            $paypal->setCredential(Config::PAYPAL_EMAIL);
            $paypal->setCurrency('BRL');
            $paypal->setReference($reference);
            $paypal->setNotificationURL('https://premiumhosting.com.br/client/recarregar/notification/paypal');

            echo JsonPretty::encode([
                'success' => true,
                'link' => $paypal->checkout()
            ]);
        }

        if($gateway == 'mercadopago') {
            try {
                $mp = new \MP(Config::MERCADOPAGO_CLIENT_ID, Config::MERCADOPAGO_CLIENT_SECRET);
            }catch (\MercadoPagoException $e) {
                echo json_encode([ 'status' => false, 'message' => $e->getMessage() ]);
                die();
            }

            $items = [];

            $items[] = [
                "title" => 'Recarga PremiumHost',
                "quantity" => 1,
                "currency_id" => "BRL",
                "unit_price" => (double) floatval($amount)
            ];

            $backUrls = array("success" => APP_ROOT, "failure" => APP_ROOT, "pending" => APP_ROOT);

            $preference_data = [
                "items" => $items,
                "back_urls" => $backUrls,
                "auto_return" => "approved",
                "notification_url" => 'https://premiumhosting.com.br/client/recarregar/notification/mercadopago',
                "external_reference" => $reference
            ];

            $preference = $mp->create_preference($preference_data);

            $url = $preference['response']['init_point'];

            echo JsonPretty::encode([ 'success' => true, 'link' => $url]);

            die();
        }

        if($gateway == 'pagseguro') {
            try {
                Library::initialize();
                Library::cmsVersion()->setName("Kodesky")->setRelease("1.0.0");
                Library::moduleVersion()->setName("Kodesky")->setRelease("1.0.0");

                $environment = 'production';

                Configure::setEnvironment($environment);
                Configure::setAccountCredentials(Config::PAGSEGURO_EMAIL, Config::PAGSEGURO_TOKEN);
                Configure::setCharset('UTF-8');

                $payment = new Payment();

                $payment->addItems()->withParameters(
                    '01',
                    'Recarga PremiumHost',
                    1,
                    floatval($amount)
                );


                $payment->addMetadata()->withParameters('GAME_NAME', 'MINECRAFT');

                $payment->setCurrency('BRL');
                $payment->setReference($reference);
                $payment->setRedirectUrl(APP_ROOT);
                $payment->setNotificationUrl('https://premiumhosting.com.br/client/recarregar/notification/pagseguro');

                $url = $payment->register(Configure::getAccountCredentials());

                echo JsonPretty::encode(['success' => true, 'link' => $url ]);
                die();
            }catch (\Exception $e)
            {
                echo JsonPretty::encode(['success' => false, 'message' => $e->getMessage()]);
                die();
            }
        }

        die();
    }

    public function notification() {
        $gateway = $this->getParams(0);
        $transaction = new Transaction();

        mail('jmachadoreal@gmail.com', 'Chegou notificação principal', 'Chegou uma nova notificação');

        if($gateway == 'pagseguro') {
            if(isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction') {
                $email = Config::PAGSEGURO_EMAIL;
                $token = Config::PAGSEGURO_TOKEN;

                $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/' . $_POST['notificationCode'] . '?email=' . $email . '&token=' . $token;

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $transactionCH = curl_exec($curl);
                curl_close($curl);

                if ($transactionCH == 'Unauthorized') {
                    exit;
                }

                $response = simplexml_load_string($transactionCH);
                $response = $this->xml2array($response);

                $code = $response['code'];
                $name = $response['sender']['name'];
                $email = $response['sender']['email'];
                $reference = $response['reference'];
                $net = $response['netAmount'];
                $gross = $response['grossAmount'];
                $status = $response['status'];
                $paid = ($status == 3) ? 1 : ($status == 4) ? 1 : 0;

                $splitedReference = explode(':', $reference);

                if($status == 3)
                {
                    if(!$transaction->hasPaid($code))
                    {
                        $wallet = new Wallet();
                        $wallet->add(floatval($splitedReference[1]), $splitedReference[0]);

                        $email = Profile::email($splitedReference[0]);
                        $html = file_get_contents('./app/templates/emails/service_pending.html');
                        Mail::send($email, "Recarga efetuada!", str_replace('{amount}', number_format($splitedReference[1], 2, ',', '.'), $html));
                    }
                }

                $transaction->store('PagSeguro', $code, $splitedReference[0], $name, $email, $gross, $net, $reference, $status, $paid);
            }
        }

        if($gateway == 'mercadopago') {

            mail('jmachadoreal@gmail.com', 'Chegou notificação', 'Chegou uma nova notificação');

            try {
                $mp = new \MP(Config::MERCADOPAGO_CLIENT_ID, Config::MERCADOPAGO_CLIENT_SECRET);
            }catch (\MercadoPagoException $e) {
                echo json_encode([ 'status' => false, 'message' => $e->getMessage() ]);
                die();
            }

            mail('jmachadoreal@gmail.com', 'Notificação MP', var_export($_GET, true));

            if($_GET['topic'] == 'payment') {
                $payment_info = $mp->get_payment_info($_GET['id']);

                if ($payment_info["status"] == 200) {
                    mail('jmachadoreal@gmail.com', 'Response MP', var_export($payment_info['response'], true));

                    $data = $payment_info['response'];
                    $code = $data['id'];
                    $status = $data['status'];
                    $gross = $data['transaction_amount'];
                    $amount = $data['net_received_amount'];
                    $reference = $data['external_reference'];
                    $email = $data['payer']['email'];
                    $name = (isset($data['payer']['first_name'])) ? $data['payer']['first_name']." ".$data['payer']['last_name'] : 'indefinido';

                    if ($amount == 0 || empty($amount)) {
                        $amount = ($gross - (4.99 * $gross) / 100);
                    }

                    $paid = ($status == 'approved') ? 1 : 0;

                    $splitedReference = explode(':', $reference);

                    if($status == 'approved')
                    {
                        if(!$transaction->hasPaid($code))
                        {
                            $wallet = new Wallet();
                            $wallet->add(floatval($splitedReference[1]), $splitedReference[0]);

                            $email = Profile::email($splitedReference[0]);
                            $html = file_get_contents('./app/templates/emails/service_pending.html');
                            Mail::send($email, "Recarga efetuada!", str_replace('{amount}', number_format($splitedReference[1], 2, ',', '.'), $html));
                        }
                    }

                    $transaction->store('MercadoPago', $code, $splitedReference[0], $name, $email, $gross, $amount, $reference, $status, $paid);
                }
            }
        }

        if($gateway == 'paypal') {
            $paypal = new PayPal();

            if(!$paypal->isIPNValid($_POST))
            {
                die();
            }

            $reference = $_POST['custom'];
            $email = $_POST['payer_email'];
            $code = $_POST['txn_id'];
            $gross = $_POST['mc_gross'];
            $amount = $_POST['mc_gross'] - $_POST['mc_fee'];
            $status = $_POST['payment_status'];
            $name = $_POST['first_name'] . " " . $_POST['last_name'];
            $paid = ($status == 'Completed') ? 1 : 0;

            $splitedReference = explode(':', $reference);

            if($status == 'Completed')
            {
                if(!$transaction->hasPaid($code))
                {
                    $wallet = new Wallet();
                    $wallet->add(floatval($splitedReference[1]), $splitedReference[0]);

                    $email = Profile::email($splitedReference[0]);
                    $html = file_get_contents('./app/templates/emails/service_pending.html');
                    Mail::send($email, "Recarga efetuada!", str_replace('{amount}', number_format($splitedReference[1], 2, ',', '.'), $html));
                }
            }

            $transaction->store('PayPal', $code, $splitedReference[0], $name, $email, $gross, $amount, $reference, $status, $paid);
        }
    }

    private function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }

}