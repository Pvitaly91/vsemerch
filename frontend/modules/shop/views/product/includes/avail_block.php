<? if ($model->not_available == 1): ?>
    <p class="product-description__availability not-avalable"><?= Yii::t('shop', 'Not available') ?></p>
<? else: ?>
    <p class="product-description__availability"><?= Yii::t('shop', 'Are available') ?></p>
<? endif; ?>