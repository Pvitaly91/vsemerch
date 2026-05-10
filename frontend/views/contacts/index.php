<?

use yii\widgets\Breadcrumbs;
use yii\web\View;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);
$this->registerCssFile('/new/css/form.css');
$this->registerCssFile('/new/css/contact.css');
$breadcrumbs[] = [
    "link" => \yii\helpers\Url::to(['site/index']),
    "label" => Yii::t('yii', 'Home')
];


$breadcrumbs[] = [
    "label" => $text->title,
];
$pattern = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
    $body = preg_replace($pattern, '<$1$2>', $text->body);
   // $body = strip_tags($body,["div","p","span"])
?>

<main class="container">
	<section >
        <?= $this->render('/catalog/breadcrumbs',['breadcrumbs'=>$breadcrumbs]) ?>
       
        <div class="main-block">
            <div class="content contact-infotmation_input">
                 <h1><?=$text->title?></h1>
                  <?=$text->getEditLink()?>
             <?= $body; ?>
            </div> 

         
                         <?
        $modelPopup = new \frontend\models\Mail();
        echo $this->context->renderPartial('/mail/index', [
            'model' => $modelPopup,
        ]);
        ?>
            
        </div>
    </section>
</main>
