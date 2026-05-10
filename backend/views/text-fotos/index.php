<?

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Фото';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Фото</h1>
<?= Html::a('Создать', ['/text-fotos/save', 'textID' => $_GET['textID']], ['class' => 'btn btn-success']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'value' => 'id',
            'contentOptions' => ['style' => 'width: 70px;']
        ],
        [
            'attribute' => 'name_ru',
            'value' => 'name_ru',
            //'contentOptions'=>['style'=>'max-width: 300px;']
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}&nbsp;&nbsp;{mod}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/text-fotos/save', 'id' => $model->id, 'textID' => $_GET['textID']],
                        [
                            'title' => "Редактировать",
                        ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/text-fotos/delete', 'id' => $model->id, 'textID' => $_GET['textID']],
                        [
                            'title' => "Удалить",
                            'data-confirm' => 'Желаете удалить запись?',
                        ]);
                },
            ],
            'contentOptions' => ['style' => 'width: 110px;']
        ],
    ],
]) ?>
