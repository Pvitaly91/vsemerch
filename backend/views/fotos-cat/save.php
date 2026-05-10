<?
$this->title = 'Добавить фото';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить фото</h1>
<?= $this->render('_form',['model'=>$model]) ?>