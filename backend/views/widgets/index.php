<?
use yii\helpers\Html;
use yii\grid\GridView;
use \common\models\Widget;
$this->title = 'Виджети';
$this->params['breadcrumbs'][] = $this->title;
?>

<?

    foreach(Widget::$types as $id => $label){
        echo Html::a('Создать '.$label, ['/widgets/save',"type" => $id], ['class'=>'btn btn-success',"style" => "margin-right:30px;"]);
    }    
    ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		[
			'attribute' => 'id',
			'value'=>'id',
			'contentOptions'=>['style'=>'width: 70px;']
		],
		[
			'attribute' => 'link_text_uk',
			'value'=>'link_text_uk',
			//'contentOptions'=>['style'=>'max-width: 300px;']
		],
        [
			'attribute' => 'type',
			'value'=>function($data){
    
                            return Widget::$types[$data->type];
                        },
                'format'=>'raw',
		],
        
		[
			'class'    => 'yii\grid\ActionColumn',
			'template' => '{update}&nbsp;&nbsp;{delete}',
			'buttons' => [
				'update' => function ($url, $model) {
					return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/widgets/save','id'=>$model->id,"type" => $model->type],
						[
							'title' => "Редактировать",
						]);
				}
			],
			'contentOptions'=>['style'=>'width: 70px;']
		],
	],
]) ?>




