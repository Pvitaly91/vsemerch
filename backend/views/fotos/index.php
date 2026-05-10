<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Фотос';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Фотос</h1>
<p>

<?= Html::a('Создать', ['/fotos/save','catID'=>$_GET['catID']], ['class'=>'btn btn-success']) ?>

<?= Html::a('Мульти загрузка', ['/fotos/upload','catID'=>$_GET['catID']], ['class'=>'btn btn-danger']) ?>
</p>
<?=Html::beginForm(['/fotos/delete-all']);?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn',
            'contentOptions'=>['style'=>'width: 20px;']],
        [

		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
		[
		'attribute' => 'name',
		'value'=>'name',
		//'contentOptions'=>['style'=>'max-width: 300px;']
		],
		[
		'attribute' => 'image',
                'format' => 'image',    
		'value'=>function($data) { return '/upload/fotos/ico/'.$data->image; },
		'contentOptions'=>['style'=>'width: 100px;']
		],         
        
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
			        'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/fotos/save','id'=>$model->id,'catID'=>$_GET['catID']],
                        [
                            'title' => "Редактировать",
                        ]);
                    },
                'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/fotos/delete','id'=>$model->id,'catID'=>$_GET['catID']],
                        [
                            'title' => "Удалить",
                            'data-confirm' => 'Желаете удалить запись?',
                        ]);
                    },
                           
                ],
			'contentOptions'=>['style'=>'width: 70px;']
        ],		
    ],
]) ?>
<?=Html::submitButton('Удалить', ['class' => 'btn btn-danger','data-confirm' => 'Желаете Удалить?']);?>
<?= Html::endForm();?>
