<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="2"><?=Yii::t('shop', 'Specifications')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model->option as $item):?>
    <tr>
        <td><?=$item->option?>:</td>
        <td><?=$item->value?></td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>