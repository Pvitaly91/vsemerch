<?php
namespace frontend\controllers;

use common\models\Banner;
use frontend\models\Articles;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
use frontend\models\News;
use frontend\models\Text;
use common\models\Widget;
use common\models\Category;
use yii\web\HttpException;
use yii\helpers\Inflector;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
    
        //Yii::$app->xmlImport->import();
       // exit;
       
        $modelText = Text::find()->where(['slug'=>'home'])->one();
        $modelTextSeo = Text::find()->where(['slug'=>'seo'])->one();
        $modelArticles = Articles::find()->limit(3)->orderBy('id DESC')->all() ;

        $query = News::find()->where(["is","type", new \yii\db\Expression('null')])->orderBy('id DESC') ;
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize'=>18]);
        $pages->forcePageParam = false;
        $pages->pageSizeParam = false;
        $news = $query->offset($pages->offset)
        // ->orderBy('id ASC')
        // ->limit($pages->limit)
        ->orderBy('id DESC')
            ->limit ('3')
            ->all();
        $widgets = Widget::find()
                ->where(['active' => 1, 'type' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
          $widgets_suveniry = Widget::find()
                ->where(['active' => 1, 'type' => 2])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
        

        $banners = Banner::find()->orderBy(['sort'=>SORT_DESC])->all();
       // dd($banners);        
        return $this->render('new/index', [
            'text'=>$modelText,
            'textSeo'=>$modelTextSeo,
            'pages'=>$pages,
            'news'=>$news,
            'articles'=>$modelArticles,
            "widgets" => $widgets,
            "banners" => $banners,
            "widgets_suveniry" => $widgets_suveniry
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    
    function actionTest(){

        $allImagesUser = getenv('ALL_IMAGES_FTP_USER') ?: 'all_images';
        $allImagesPassword = getenv('ALL_IMAGES_FTP_PASSWORD') ?: '';
        dd((scandir("ftp://{$allImagesUser}:{$allImagesPassword}@176.9.83.91/")));
        exit;

        $ftp_server = '176.9.83.91';
        $ftp_username = $allImagesUser;
        $ftp_password = $allImagesPassword;
        
        $ftp_server = '176.9.83.91';
        $ftp_username = getenv('AGCITY_FTP_USER') ?: 'Agcity';
        $ftp_password = getenv('AGCITY_FTP_PASSWORD') ?: '';
        // Connect to the FTP server
        $conn_id = ftp_connect($ftp_server);
        
        if ($conn_id) {
            // Login to the FTP server
            $login = ftp_login($conn_id, $ftp_username, $ftp_password);
           // var_dump($conn_id);
           
            if ($login) {
                // Specify the directory you want to list the files from
                $directory = '';
        
                // Get the file list
                $file_list = ftp_rawlist($conn_id,"..");
                var_dump($file_list);
               
                if ($file_list) {
                    // Output the list of files
                    echo "List of files in $directory:\n";
                    foreach ($file_list as $file) {
                        echo "$file\n";
                    }
                }else {
                        echo "Failed to retrieve file list. Error: ";
                   
                }
        
                // Close the FTP connection
                ftp_close($conn_id);
            } else {
                echo "Failed to login to the FTP server.";
            }
        } else {
            echo "Unable to connect to the FTP server.";
        }
       
      exit;
      
        $xml_file = file_get_contents('ftp://stock:Stua9000@ftp.bergamo.ua/items.xml');
        echo $xml_file;
        exit;
        $model = \common\models\Product::find()->where(["id" => "240947"])->one();
        dd($model->morePhotos());
        exit;
        $f = "ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/voyager/v4541_04_a.jpg";
         $xml_file = file_get_contents($f);
         header('Content-Type: image/jpeg');
         echo $xml_file;
       //  dd($xml_file);
      //   echo $xml_file;
         
        exit;
      
   
       if(!empty($this->data))
            return;
        
        $xml_file = file_get_contents('ftp://stock:Stua9000@ftp.bergamo.ua/items.xml');
        $xml = new \SimpleXMLElement($xml_file);
        $productIds = [];
        $i = 0;
       // echo count($xml->Item);
        
        $noCats = $usetProds = [];
        $it = [];
        // model:
        //image
        //slug
        //code
  
        //category_id
        //
        // translates:
        //title
        //description
        //meta_title
        //meta_description
        $map["Artikul"] = "code";
        $map["Name"] =  "title";
        $map["Opisanie"] =  "description";
        $map["Picture1"] = "image";
        
        $ext = [
            "FilterGroup",
            "artikulmodel",
            "CodePodatkova",
            "NameForDocuments",
            "Poznica",
            "Amountsupply",
            "Datesupply",
            "Indupakovka",
            "Subgroup",
            "Mupakovka",
            "Bupakovka",
            "Group",
            "Ostatok",
            "Store",
           // "Odezhda",
           // "type_id",
            "Code",
            "Colornumber",
            "colorcode"
        ];
        $num = 100000000000;
        $i = 0;
        $products = $ids = [];
              $options = [];
              
                
        foreach ($xml->Item as $item) {
            if($num < $i ){
                break;
            }
            
          //  if($item->Odezhda == "true" )
          //      continue;
            
            $arr = [];
         
            foreach ($item as $name => $value){
                
                
                
                if(substr($name,0,7) != "Picture"){
                    if(!in_array($name, $ext) && (string)$item->$name != ""){
                       if(isset($map[$name])){
                            $arr[strtolower($map[$name])] = (string)$item->$name; 
                        }else{
                            $arr["options"][strtolower($name)] = (string)$item->$name; 
                            $options[$name] = (string)$item->$name;
                          //  if($name == "category")
                            /*if($options[$name] == null|| !in_array((string)$item->$name, $options[$name])){
                                if($name == "Razmer"){
                                    $options[$name][] = [(string)$item->$name => (string)$item->Odezhda];
                                }else
                                    $options[$name][] = (string)$item->$name;
                            }*/
                                
                         //   else    
                         //       $options[$name] = (string)$item->$name; 
                        } 
                        
                    }
                }elseif($name != "Picture1"){
                    $arr["more_photo"][] = (!empty((string)$item->$name )) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . (string)$item->$name : null;
            
                }
                
            }
            $arr["image"] = (!empty((string)$item->Picture1)) ? 'ftp://stock:Stua9000@ftp.bergamo.ua/kartinki_dlya_saita/' . (string)$item->Picture1 : null;
            $arr["is_main"] = 1;
            $arr["meta_description"] = $arr["meta_title"] = $arr["title"];
            $arr["slug"] =  Inflector::slug($arr["title"]);
            $arr["partner_id"] = (string)$item->Artikul;
            $arr['price'] = (float)str_replace(' ', '', (string)$item->Poznica);
            $arr["sku_group"] = str_replace((string)$item->Colornumber, '', (string)$item->Artikul);
            if($arr['price'] == "0")
                continue;
            $arr['not_available'] = ($item['ostatok']> 0) ? 0 : 1;
            $i++;

          
            if(!in_array($arr["sku_group"], $ids)){
                $arr["is_main"] = 1;
                $ids[] = $arr["sku_group"];
            }else{
                $arr["is_main"] = 0; 
            }


        //    }
         //   print_r($arr);
        //    exit;
            
            /////////////////////////////////
            $Group = (string)$item->Group;
            $Subgroup = (string)$item->Subgroup;
           
            if($Subgroup == "")
               $key =$Group;
            else
               $key = $Subgroup;
            
            $key = ($key == "")?"БЕЗ КАТЕГОРИИ": $key;
            
          // if(!isset($usetProds[$Name]))
            $slug = Inflector::slug($key);
            if(!isset($this->data[$slug])){
                $this->data[$slug] = [ "cat" => [
                        "translates" =>[
                            "title" => $key,
                            "meta_title" => $key,
                            "meta_description" => $key,
                        ],
                        "slug" => $slug,
                        "partner_slug" => $slug,
                        "partner" => $this->partnerName, 
                    ]    
                ];
            }

            $this->data[$slug]['products'][] = $arr;
           //  $usetProds[$Name] = "";
          //  }
             
        }
       // print_r($i);
        dd($options);
        dd($this->data);
      // exit;
        // $this->data["products"] = $products;
       
        
        exit;
        if(!\common\Helpers\Partners::isAdmin())
            throw new HttpException(404, 'Данной странице не существует!');
        
        $xml_file = file_get_contents('ftp://stock:Stua9000@ftp.bergamo.ua/items.xml');
        $xml = new \SimpleXMLElement($xml_file);
        $productIds = [];
        $i = 0;
       // echo count($xml->Item);
        
        $cats =  $noCats = [];
        foreach ($xml->Item as $item) {
            $Group = (string)$item->Group;
            $Subgroup = (string)$item->Subgroup;
            $Name = (string)$item->Name;
          //  if(!is_object($item->Subgroup))
          //      $cats[$Group] = (string)$item->Subgroup;
         //   else{
            if($Subgroup == "")
               $key =$Group;
            else
               $key = $Subgroup;
            
            $key = ($key == "")?"БЕЗ КАТЕГОРИИ": $key;
            
            if(!isset($usetProds[$Name]))
                $cats[$key][] = $Name;
           
             $usetProds[$Name] = "";
          //  }
             
        }
        dd($cats);
        
        exit;
          $sinonimi["ofis"] = [
            "sodennik",
            "bloknoti"
            
        ];
        $sinonimi["dim"] = ["fartuhi"];
        
        $sinonimi["golovni-ubori"] = [
            "sapki-sarfi-rukavici"
            ];
        $sinonimi["sumki"] = [
            "sumka-transformer",
            "festibax"
            ];
        $sinonimi["elektronika"] = [
            "ekotovari",
            "kolonki",
            "fleski-ta-navusniki"
            ];
        $sinonimi["upakovka"] = [
            "kraft-paketi",
            "pakuvanna-universalne"
            ];
        $sinonimi["odag"] = [
            "militari-stil"
        ];
         $sinonimi["personalni-aksessuari"] = [
            "brasleti",
            "klucnici-ta-breloki"
        ];
         $sinonimi["rucki"] =[
             "futlari-ta-pidstavki"
             ];
        
        $models = Category::find()
                ->where(['=','partner' , "totobi"])
                
                ->all();
  
       echo "<br><pre>";
     
        $cats = [];
            foreach($models as $k => $model){
                if($model->parent_id == NULL){
                    $cats[$model->id] = [
                        "slug" =>$model->slug,
                       // "name" => $model->title
                    ];
                    unset($models[$k]);
                }
            }
            $menu = [];
            foreach($models as $k => $model){
                if(isset($cats[$model->parent_id])){
                    $menu[$cats[$model->parent_id]["slug"]][$model->slug] = $model->slug."-".$model->id;
                    
                }
            }
       
        function makeMenu(&$eney,&$menu,$_part,$parent,&$coincidence){
            foreach($eney as $slug => $id){

              if(stripos($slug,$_part) !== false){
                    $menu[$parent][$slug] = $slug."-".$id."|ENEY";
                    setCoincidence($coincidence,$parent,$slug,"subcat")  ;    
                    unset($eney[$slug]);
              }
           }
        }    
        
        $eney = [];
        $models = Category::find()
            ->where(['=','partner' , "eney"])
            ->all();

        foreach($models as $k => $model){
            $eney[$model->slug] = $model->id;

        }
       unset($eney["eney"]);
       print_r(count($eney));
       echo "<br>";
        $coincidence = [];
       function setCoincidence(&$coincidence,$siteSlug,$eneySlug,$type){
            $coincidence[$eneySlug] = [
                "slug" => $siteSlug,
                "type" => $type
            ];
          
       }
     
       
       foreach($menu as $parent => $items){
           /*****first level menu coincidence*****/
           if(isset($eney[$parent])){
            //   print_r($eney[$parent]);
          //     exit;
               
             $menu[$parent][$parent] = $parent."-".$eney[$parent]." ENEY 1-level";
             setCoincidence($coincidence,$parent,$parent,"subcat");
             unset($eney[$parent]);
           }
           /*******second level menu coincidence********/
           if(is_array($items)){
            //   print_r($items);
            //   print_r($eney);
              
               foreach($items as $slug =>$url){
                   
                    if(isset($eney[$slug])){
                        
                        $menu[$parent][$slug] = $slug."-".$eney[$slug]." ENEY 2-level";
                        setCoincidence($coincidence,$slug,$slug,"merge");
                        unset($eney[$slug]);
                    }
               }
           }
       }
     
      
        foreach($menu as $parent => $items){
            $_parts = explode("-",$parent);
            foreach($_parts as $k => $_part){  
                if(strlen($_part) > 3 ){
                    makeMenu($eney,$menu,$_part,$parent,$coincidence);
                }    
            }
            if(isset($sinonimi[$parent]) ){
                foreach($sinonimi[$parent] as $_part){
                    makeMenu($eney,$menu,$_part,$parent,$coincidence);
                }
            }
        }   
        
        /*****sub cats*******/
        for($i =0; $i<= 1; $i++){
            foreach($menu as $parent => $items){

                foreach ($items as $subCat){
                    $_parts = explode("-",$subCat);
                    foreach($_parts as $k => $_part){  
                        if(strlen($_part) > 3){

                            makeMenu($eney,$menu,$_part,$parent,$coincidence);   
                        }   
                    }
                }
            }
        }
        
         print_r($menu);
        echo "</pre><br><pre>";
        print_r($eney); 
        print_r(count($eney));
        var_export($coincidence);
        echo "</pre><br>";
    }
}
