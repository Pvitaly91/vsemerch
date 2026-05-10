<?
$this->title = 'Добавить блог';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=$this->title?></h1>
<?= $this->render('_form',['model'=>$model]) ?>