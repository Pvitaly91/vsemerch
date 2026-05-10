<div class="navigation-block">
    <? foreach ($breadcrumbs as $item): ?>
        <? if (isset($item["link"])): ?>
            <a href="<?= $item["link"] ?>"><span><?= $item["label"] ?></span></a>
            <svg
                width="5"
                height="8"
                viewBox="0 0 5 8"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                >
                <path d="M1 1L4 3.76923L1 7" stroke="#989898" stroke-linecap="round" />
            </svg>
        <? else: ?>
            <p class="hide-if-mob ucfirst"><?= mb_strtolower($item["label"]) ?></p>
        <? endif; ?>
    <? endforeach; ?>

</div>