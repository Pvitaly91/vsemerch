<?
$this->title = 'Добавить новость';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить новость</h1>
<?= $this->render('_form',['model'=>$model]) ?>