<?
$this->title = 'Добавить пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Добавить пользователя</h1>
<?= $this->render('_form',['model'=>$model]) ?>