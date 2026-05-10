<?
$this->title = 'Добавить Заболевания';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить Заболевания</h1>
<?= $this->render('_form',['model'=>$model]) ?>