<?
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Статьи';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Статьи</h1>
<?= Html::a('Создать', ['/articles/save'], ['class'=>'btn btn-success']) ?>
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
			'template' => '{update}&nbsp;&nbsp;{delete}',
			'buttons' => [
				'update' => function ($url, $model) {
					return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/articles/save','id'=>$model->id],
						[
							'title' => "Редактировать",
						]);
				}
			],
			'contentOptions'=>['style'=>'width: 70px;']
		],
	],
]) ?>




