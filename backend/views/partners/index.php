<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Партнеры';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Партнеры</h1>
<?= Html::a('Создать', ['/partners/save'], ['class'=>'btn btn-success']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
		[
		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
		[
		'attribute' => 'image',
                'format' => 'image',
		'value'=>function($data) { return '/upload/partners/'.$data->image; },
		'contentOptions'=>['style'=>'width: 100px;']
		],
		[
		'attribute' => 'title_ru',
		'value'=>'title_ru',
		//'contentOptions'=>['style'=>'max-width: 300px;']
		],		
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
			        'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/partners/save','id'=>$model->id],
                        [
                            'title' => "Редактировать",
                        ]);
                    }
                ],
			'contentOptions'=>['style'=>'width: 70px;']
        ],		
    ],
]) ?>
