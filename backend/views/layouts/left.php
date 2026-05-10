<aside class="main-sidebar">

    <section class="sidebar">



        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'submenuTemplate' => "\n<ul class='treeview-menu' style='display: block' role='menu'>\n{items}\n</ul>\n",
                'items' => [
                    ['label'=>'Users', 'icon' => 'far fa-file', 'url'=>['/users/index']],
                    ['label'=>'Текстовые страницы', 'icon' => 'far fa-file', 'url'=>['/text/index']],
                    
                    // ['label'=>'<span class="glyphicon glyphicon-repeat"></span> Галерея', 'url'=>['/fotos-cat/index']],
                    ['label'=>'Новости', 'icon' => 'far fa-list-alt', 'url'=>['/news/index']],
                    ['label'=>'Блог', 'icon' => 'far fa-list-alt', 'url'=>['/blog/index']],
                    ['label'=>'Статьи', 'icon' => 'far fa-list-alt', 'url'=>['/articles/index']],

                    ['label'=>'Каталог', 'icon' => 'far fa-list-alt', 'url'=>['/catalog/index']],
                    ['label'=>'Банера', 'icon' => 'far fa-file', 'url'=>['/banner/index']],
                    ['label'=>'Портфолио слайдер', 'icon' => 'far fa-list-alt', 'url'=>['/portfolio-slider']],
                    ['label'=>'Виджети', 'icon' => 'far fa-list-alt', 'url'=>['/widgets/index']],
                    ['label'=>'Опции', 'icon' => 'far fa-list-alt', 'url'=>['/option/index']],
                    ['label'=>'i18n переводы', 'icon' => 'fas fa-language', 'url'=>['/i18n/translation']],
                    ['label'=>'Пользователи', 'icon' => 'far fa-user', 'url'=>['/users/index']],
                    [
                        'label' => 'Магазин',
                        'options' => ['class' => 'menu-open'],
                        'icon' => 'fas fa-shopping-cart',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Каталог', 'icon' => 'fas fa-list', 'url' => ['/shop/category'],],
                            ['label' => 'Размеры', 'icon' => 'fas fa-bars', 'url' => ['/shop/size'],],
                            ['label' => 'Товары', 'icon' => 'fas fa-apple', 'url' => ['/shop/product'],],
                            ['label' => 'Заказы', 'icon' => 'fas fa-shopping-basket', 'url' => ['/shop/order'],],

                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
