<?
use yii\widgets\Breadcrumbs;
use yii\web\View;
?>
<?
$this->title = $text->meta_title;
$this->registerMetaTag(['name' => 'description', 'content' => $text->meta_description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $text->meta_keywords]);

?>
<div class="head2">
	<div class="container">
		<div class="info2 col-md-12">
			<h1><?=$text->title;?></h1>


			<nav class="breadcrumbs">
				<?= Breadcrumbs::widget([
					'homeLink' => [
						'label' => Yii::t('yii', 'Home'),
						'url' => ['site/index'],
					],
					'links' => [$text->title],
				]) ?>
			</nav>
		</div>
	</div>
</div>
<div class="body_box">
	<div class="top2"></div>
	<div class="content">
		<div class="container cnt">
			<div class="row">
			<div class="col-md-6">

				<?=$text->body;?>

			</div>

				<div class="col-md-6">
					<h2><?=Yii::t('app', 'Mail send')?></h2>
				<?
				$modelPopup = new \frontend\models\Mail();
				echo $this->context->renderPartial('/mail/index', [
					'model' => $modelPopup,
				]);
				?>



			</div>
			</div>

			</div></div></div>