<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Текстовые страницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Текстовые страницы</h1>
<?= Html::a('Создать', ['/text/save'], ['class'=>'btn btn-success']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
		[
		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
        [
            'attribute' => 'title_ru',
            'value'=>function($data){
                return Html::a($data->title_ru, ['/text-fotos/index','textID'=>$data->id]);
            },
            'format'=>'raw',
            //'contentOptions'=>['style'=>'max-width: 300px;']
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
			        'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/text/save','id'=>$model->id],
                        [
                            'title' => "Редактировать",
                        ]);
                    },
                        'fotos' => function ($url, $model) {
                            return Html::a('Фотос', ['/fotos/index','productID'=>$model->id,'type'=>'text'],
                                [
                                    'title' => "Фотос",
                                ]);
                        }
                ],
			'contentOptions'=>['style'=>'width: 170px;']
        ],		
    ],
]) ?>
