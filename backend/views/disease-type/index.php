<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Типы заболеваний';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Типы заболеваний</h1>
<p>
<?= Html::a('Создать', ['/disease-type/save','catID'=>$_GET['catID']], ['class'=>'btn btn-success']) ?>
</p>

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
		'value'=>'title_ru',
		//'contentOptions'=>['style'=>'max-width: 300px;']
		],		
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}&nbsp;&nbsp;{mod}',
			        'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/disease-type/save','id'=>$model->id,'catID'=>$_GET['catID']],
                        [
                            'title' => "Редактировать",
                        ]);
                    },
                'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/disease-type/delete','id'=>$model->id,'catID'=>$_GET['catID']],
                        [
                            'title' => "Удалить",
                            'data-confirm' => 'Желаете удалить запись?',
                        ]);
                    },
                       'fotos' => function ($url, $model) {
                            return Html::a('Фотос', ['/admin/fotos/index','productID'=>$model->id,'type'=>'products'],
                                [
                                    'title' => "Фотос",
                                ]);
                        }
                    ],
            'contentOptions'=>['style'=>'width: 110px;']
        ],		
    ],
]) ?>
