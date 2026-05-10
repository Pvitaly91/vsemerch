<?
$this->title = 'Добавить продукт';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить продукт</h1>
<?= $this->render('_form',['model'=>$model]) ?>