<?
$this->title = 'Добавить каталог';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить каталог</h1>
<?= $this->render('_form',['model'=>$model]) ?>