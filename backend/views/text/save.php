<?
$this->title = 'Добавить текстовую страницу';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить текстовую страницу</h1>
<?= $this->render('_form',['model'=>$model]) ?>