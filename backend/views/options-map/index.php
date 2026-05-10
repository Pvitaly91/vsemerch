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
    <?= Alert::widget() ?>
    <?php $form = ActiveForm::begin(
       [ 
           'id' => 'login-form',
            'options' => ['class' => 'well form-vertical']
        ]
    ); ?>
     <?= Alert::widget() ?>
    Склеить свойство:<strong><?=$name?></strong> партнера: <strong><?=$partnerName;?></strong> для категории <strong><?=$catName?></strong>
    <div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding-left: 20px; margin-top: 20px; margin-bottom: 20px; max-width: 600px; background-color: white;'>
       
            <? foreach($list as $slug => $label):?>
              
                <p><label><input name='site_slug'  type='radio' value='<?=$slug?>'> <?=$label?></label></p>
               
            <? endforeach;?>
          
    </div>        
    

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>