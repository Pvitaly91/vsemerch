<?
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Каталог';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Каталог</h1>
<?= Html::a('Создать', ['/catalog/save'], ['class'=>'btn btn-success']) ?>
<?
echo GridView::widget([
    'dataProvider' =>$dataCatalog,
    'columns' => [
		[
		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
		[
		'attribute' => 'name_ru',
		'value'=>function($data){
                            return Html::a($data->name_ru, ['/products/index','catID'=>$data->id]);
                        },
                'format'=>'raw',
		//'contentOptions'=>['style'=>'max-width: 300px;']
		],		
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
            'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/catalog/save','id'=>$model->id],
                        [
                            'title' => "Редактировать",
                        ]);
                    },
                'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/catalog/delete','id'=>$model->id],
                        [
                            'title' => "Удалить",
                            'data-confirm' => 'Желаете удалить запись?',
                        ]);
                    },
                                   
                ],
			'contentOptions'=>['style'=>'width: 100px;']
        ],		
    ],
]) ?>
