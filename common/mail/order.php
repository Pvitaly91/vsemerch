<?php
/* @var $order common\models\Order */
use yii\helpers\Html;
?>

<h1>Новый заказ #<?= $order->id ?></h1>

<h2>Контакты</h2>

<ul>
    <li>Имя: <?= Html::encode($order->name) ?></li>
    <li>Телефон: <?= Html::encode($order->phone) ?></li>
    <li>Email: <?= Html::encode($order->email) ?></li>
    <? if($order->address != ""):?>
        <li>Адрес: <?= Html::encode($order->address) ?></li>
    <? endif;?>
    <li>Способ оплати: <?=($order->payment == "2")?"Сплачено":"Післясплата" ?></li>
     
</ul>

<h2>Пожелания</h2>

<?= Html::encode($order->notes) ?>

<h2>Товары</h2>

<ul>
<?php
$sum = 0;
foreach ($order->orderItems as $item): ?>
    <?php $sum += $item->quantity * $item->price ?>
    <li><?= Html::encode($item->product->partner_id . ' x ' . $item->code . ' x ' . $item->title . ' x ' . $item->quantity . ' x ' . $item->price . 'грн.') ?></li>
<?php endforeach ?>
</ul>

<p><string>Вся сумма: </string> <?php echo $sum?>грн.</p>

