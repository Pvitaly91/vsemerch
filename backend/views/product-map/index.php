<? use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dmstr\widgets\Alert;?>

<div class="categories-map-create">
    <p><?=  Html::encode("Перемести выбрание товари:")?></p>
       <?php $form = ActiveForm::begin(
       [ 
           'id' => 'login-form',
            'options' => ['class' => 'well form-vertical' ],
           "action" => "/admin/product-map/create"
        ]
    ); ?>
    <input type="hidden" name="refer" value="<?=$refer?>">
    <? foreach($titles as $id=> $title):?>
        <input type="hidden" name="products[]" value="<?=$id?>"><strong><?=$title?></strong><br>
        
    <? endforeach;?>
    <?= Alert::widget() ?>    
    <div class="form-group" style="margin-top:20px;">
        <?  $referLink = urlencode($_SERVER["REQUEST_URI"]);?>
        <a href="/admin/shop/category/create?parent_id=false&refer=<?=$referLink?>" class="btn btn-success">Создать корневую категорию</a>    
     </div>
    
    <div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding-left: 20px; margin-top: 20px; margin-bottom: 20px; max-width: 600px; background-color: white;'>
     
             <? foreach($menu as $slug => $subItems):?>
      
                  <p><strong><?=$catsBase[$slug]['title']?></strong> 
                    <a href="/admin/shop/category/create?parent_id=<?=$mainCatsId[$slug]?>&refer=<?=$referLink?>" class="btn btn-success">Добавить подкатегорию</a>    
                </p>
                 <? foreach($subItems as $subCat_slug => $uuid):?>
                     <p style='margin-left:20px;'>
                         <label>
                             <input name='uuid'  type='radio' value='<?=$uuid?>'> <?=$catsBase[$subCat_slug]['title']?>
                         </label>
                     </p>
                 <? endforeach;?>
             <? endforeach;?>
   
     </div> 
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>