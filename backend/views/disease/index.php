<?
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Заболевания';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Заболевания</h1>
<p>
<?= Html::a('Создать', ['/disease/save'], ['class'=>'btn btn-success']) ?>
</p>
<?
echo GridView::widget([
    'dataProvider' =>$dataProvider,
    'columns' => [
		[
		'attribute' => 'id',
		'value'=>'id',
		'contentOptions'=>['style'=>'width: 70px;']
		],
		[
		'attribute' => 'name_ru',
		'value'=>function($data){
                            return Html::a($data->name_ru, ['/disease-type/index','catID'=>$data->id]);
                        },
                'format'=>'raw',
		//'contentOptions'=>['style'=>'max-width: 300px;']
		],		
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
            'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/disease/save','id'=>$model->id],
                        [
                            'title' => "Редактировать",
                        ]);
                    },
                'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/disease/delete','id'=>$model->id],
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
