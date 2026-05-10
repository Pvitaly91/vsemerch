<?php
namespace frontend\controllers;

use yii\web\Controller;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Credential\AccountSecretTestCredential;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\Product;
use WayForPay\SDK\Wizard\PurchaseWizard;
use WayForPay\SDK\Domain\MerchantTypes;
use WayForPay\SDK\Domain\PaymentSystems;
use WayForPay\SDK\Exception\WayForPaySDKException;
use WayForPay\SDK\Handler\ServiceUrlHandler;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Domain\Delivery;

class WayforpayController extends Controller{
    public $credential;
    public $protocol;
    function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        if(IS_PROD){
            $this->credential = new AccountSecretCredential('agcity_com_ua', '2c408a2f513356cdfeb8f276d84d801ff3a64854');
            $this->protocol = "https";
        }else{
            $this->credential = new AccountSecretTestCredential(); 
            $this->protocol = "http";
        }
    }
    function pay($orderData){
        
        
      //  
        $orderData['phone'] = str_replace("(", "", $orderData['phone']);
        $orderData['phone'] = str_replace(")", "-", $orderData['phone']);
        $orderData['phone'] = "+3".$orderData['phone'];
        $deliveryPhone = $orderData['phone'];
        $this->log($orderData,"POST");
        $fio = explode(" ",$orderData["name"]);
        $firstName = (isset($fio[0]))?$fio[0]:"";
        $lastName = (isset($fio[1]))?$fio[1]:"";
     //  dd($productData);
        $productsData = [];
        $cart = \Yii::$app->cart;
        $products = $cart->getPositions();
        foreach($products as $item){
            $item->makeDiscount();
        }
        $total = \Yii::$app->cart->getCost();
        foreach ($products as $prod){
            if($prod instanceof \common\models\ProductSize)
            {
                $productsData[] = new Product($prod->product->title." ".$prod->product->code, $prod->getCost(), $prod->getQuantity());
            }else{
                $productsData[] = new Product($prod->title." ".$prod->code, $prod->getCost(), $prod->getQuantity());
            }
        }

        $paymentSystems = new PaymentSystems(["card"]);
        if(IS_PROD == false){
          $total = "1";
        }
       
       // $deliveryPhone = str_replace("-", "", $deliveryPhone);
        
       // $delivery = new Delivery("Імя","Фамілия",$orderData["address"],"Київ","Київська","00000",'Україна',$orderData["email"], $orderData['phone']);
        $data = PurchaseWizard::get($this->credential)
        ->setLanguage('ua')    
        ->setOrderReference(sha1(microtime(true)))
        ->setAmount($total)
         
        ->setCurrency('UAH')
        ->setOrderDate(new \DateTime())
        ->setMerchantDomainName('https://agcity.com.ua')
     //   ->setDelivery($delivery)          
    //    ->setMerchantTransactionType(MerchantTypes::TRANSACTION_AUTO) 
    //    ->setMerchantTransactionType(MerchantTypes::TRANSACTION_AUTH) //  hold
        ->setClient(new Client(
            $firstName,
            $lastName,
            $orderData["email"],
            $orderData["phone"],
            'UA'
        ))
        ->setProducts(new ProductCollection($productsData))
        ->setReturnUrl($this->protocol.'://'.$_SERVER["SERVER_NAME"].'/returnwayforpay')
        ->setServiceUrl($this->protocol.'://'.$_SERVER["SERVER_NAME"].'/servicewayforpay')
        ->setPaymentSystems($paymentSystems)    
        ->getForm()
        ->getAsString();    
       //->getData();
       // dd($data);
        $status = "";
        $data = str_replace('action="https://secure.wayforpay.com/pay"', 'action="https://secure.wayforpay.com/pay" id="formPay"', $data);
        ?>
        <style>
            input[value=Pay].btn.btn-primary{
               display: none; 
            }
        </style><?   echo $data; ?>
        <script>
             document.getElementById("formPay").submit();
        </script><?
     
        exit;
    }
    function actionIndex(){
     
        if(($allOrderData = \Yii::$app->session->get("order_post")) == true && isset($allOrderData['Order'])){
            $this->pay($allOrderData['Order']);
        }
    }
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }
    function actionReturnwayforpay(){
     
    
      // 
        try {
            $handler = new ServiceUrlHandler($this->credential);
            $response = $handler->parseRequestFromGlobals();
         //   d($response);
         //   d($response->getReason());    
            if ($response->getReason()->isOK()) {
               
             //   $_SESSION["order_post"]["Order"]["payment"] = json_encode($_POST);
                $this->log($_POST,"success");
                return $this->redirect('/shop/cart/success');
            } else {
                $this->log($_POST,"error");
                \Yii::$app->session->addFlash('payment_error_1', $response->getReason()->getMessage());
                return $this->redirect('/shop/cart/order');
            }
        } catch (WayForPaySDKException $e) {
            $this->log($_POST,"error_service_error");
            \Yii::$app->session->addFlash('payment_error_2', $e->getMessage());
            return $this->redirect('/shop/cart/order');
        }
        
        $this->enableCsrfValidation = false;
        
    }
 
    function log($array,$key){
        $path = \Yii::$app->runtimePath."\paymentlog";
        if(is_dir($path)){
            file_put_contents($path."\\".$key."_payment_".date("d-m-Y_H_i_s",time()).".txt", var_export($array,true));
        }
    }
    function actionServicewayforpay(){
       echo "Servicewayforpay";
       $this->log($_POST,"Servicewayforpay");
        /* $this->enableCsrfValidation = false;
        d($_POST);
        d($_GET);
        exit;*/
    }
}

