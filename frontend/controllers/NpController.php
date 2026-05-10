<?
namespace frontend\controllers;

use yii\web\Controller;
use common\models\Product;
/*use common\models\Image;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\validators\NumberValidator;
use yii\web\ServerErrorHttpException;
use console\models\Category;
use console\models\CategoryTranslate;
use common\models\Language;

use console\models\Product as consoleProduct;
use console\models\ProductOption;
use console\models\ProductOptionTranslate;
use console\models\ProductSize;
use console\models\ProductTranslate;
use Yii;
use PHPThumb\GD;
use yii\helpers\FileHelper;*/
class NpController extends Controller
{
    public $token = "48a302f16a0fea2543d5458218669cb6";
    public $url = "https://api.novaposhta.ua/v2.0/json/";
    
    public function curl($array,&$status){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($array) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
      
        $info = curl_getinfo($ch);
        curl_close($ch);
       //print_r($result);  
        if(isset($info["http_code"])){
            $status = $info["http_code"];
        }
        return json_decode($result,JSON_UNESCAPED_UNICODE);
    }
    private function getModelArray($limit = false){
          return  array (
            'apiKey' => $this->token,
            'modelName' => 'Address',
            'methodProperties' => array (
                'Page' => '1',
             
                'Limit' => $limit, 
                ),
            );
       
    }
    protected function getCityArray(string $string){
        $arr = $this->getModelArray(10);
        $arr["calledMethod"] = "getCities";
        $arr["methodProperties"]["FindByString"] = $string;
        return  $arr;
       
    }
    protected function getDepartmentArray(string $CityRef){
        $arr = $this->getModelArray();
        $arr["calledMethod"] = "getWarehouses";
        $arr["methodProperties"]["CityRef"] = $CityRef;
        $arr["methodProperties"]['Language'] ="UA";
     
        return  $arr;
        
 
    }
    function makeList(array $array):array{
        $result = [];
        foreach($array as $key => $value){
            if(isset($value["Ref"]) && $value["Description"]){
                $result[] = ["key" => $value["Ref"],"value" =>$value["Description"]];
            }
        }
        return $result;
    }
    private function getData($string,$method){
        $status = "";
        $result = [];

        $res = $this->curl($this->$method($string), $status);
    //    print_r($this->$method($string));
        if($status == "200" && isset($res["success"]) && $res["success"] == 1 && isset($res["data"]) &&  is_array($res["data"])){
           $result = $this->makeList($res["data"]);
        }
        return $result;
    }
    function getCitys(string $string){
        return $this->getData($string, "getCityArray");
    }
    function getDepartaments(string $CityRef):array{
        return $this->getData($CityRef, "getDepartmentArray");
    }
    function actionCitys(){
        header('Content-Type: application/json; charset=utf-8');
       $result = $this->getCitys($_GET["term"]);
       /*$result = [
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
            ];*/
       echo json_encode($result,JSON_UNESCAPED_UNICODE);
       exit;
    }
    function actionDepartaments(){
       header('Content-Type: application/json; charset=utf-8');
    
       $result = $this->getDepartaments($_GET["gid"]);
       /*$result = [
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
                [
                    "key" => "fsfsfs",
                    "value" => "Київ"
                ],
            ];*/
       echo json_encode($result,JSON_UNESCAPED_UNICODE);
       exit;
          
    }
       
   
}
