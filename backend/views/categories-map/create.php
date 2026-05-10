<? 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dmstr\widgets\Alert;
/* @var $this yii\web\View */
/* @var $model common\models\CategoriesMapForm */

$this->title = 'Create Categories Map';
$this->params['breadcrumbs'][] = ['label' => 'Categories Map', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="categories-map-create">
    <h1><?=  Html::encode($this->title)?></h1>

    <?php $form = ActiveForm::begin(
       [ 
           'id' => 'login-form',
            'options' => ['class' => 'well form-vertical']
        ]
    ); ?>
    <?
  
    $referLink = urlencode($_SERVER["REQUEST_URI"]);
    
    ?>
     <?= Alert::widget() ?>
     <? if($type == 'merge'):?>Склеить<? elseif($type == 'subcat'):?>Переместить<? endif;?>   категорию партнера: <strong><?=$partnerCategory->partner;?></strong> название <strong><?=$partnerCategory->title?></strong>
     <div class="form-group" style="margin-top:20px;">
        <a href="/admin/shop/category/create?parent_id=false&refer=<?=$referLink?>" class="btn btn-success">Создать корневую категорию</a>    
     </div>
     <?// dd($menu);?>
     <div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding-left: 20px; margin-top: 20px; margin-bottom: 20px; max-width: 600px; background-color: white;'>
        <? if($type == 'merge'):?>
            <? foreach($menu as $slug => $subItems):?>
                <p><strong><?=$catsBase[$slug]['title']?></strong> 
                    <a href="/admin/shop/category/create?parent_id=<?=$mainCatsId[$slug]?>&refer=<?=$referLink?>" class="btn btn-success">Добавить подкатегорию</a>    
                </p>
                <? foreach($subItems as $subCat_slug => $url):?>
                    <p style='margin-left:20px;'><label><input name='site_slug' <? if($model->site_slug == $subCat_slug):?>checked='checked'<? endif;?> type='radio' value='<?=$subCat_slug?>'> <?=$catsBase[$subCat_slug]['title']?></label></p>
                <? endforeach;?>
            <? endforeach;?>
        <? elseif($type == 'subcat'):?>
             <? foreach($menu as $slug => $subItems):?>
                <p style='margin-left:10px;'><label><input name='site_slug' <? if($model->site_slug == $slug):?>checked='checked'<? endif;?> type='radio' value='<?=$slug?>'> <?=$catsBase[$slug]['title']?></label></p>     
                <? foreach($subItems as $subCat_slug => $url):?>
                       <p style='margin-left:40px;'> <?=$catsBase[$subCat_slug]['title']?></p>
                   <? endforeach;?>
            <? endforeach;?>
        <? endif;?>        
    </div>        
      <?//= $form->field($model, 'partner_slug')->textInput() ?>
    <?//= $form->field($model, 'site_slug')->textInput() ?>

    <?//= $form->field($model, 'type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>