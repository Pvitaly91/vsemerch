<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Слайдер';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Слайдер</h1>
<?= Html::a('Создать', ['/slider/save'], ['class'=>'btn btn-success']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
		[
		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
        [
            'attribute' => 'sort',
            'value'=>'sort',
            'contentOptions'=>['style'=>'width: 40px;']
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/slider/save','id'=>$model->id],
                        [
                            'title' => "Редактировать",
                        ]);
                    }
                ],
			'contentOptions'=>['style'=>'width: 70px;']
        ],		
    ],
]) ?>
