<?php
use yii\helpers\Url;
 function maekUrl($url){
    if(is_array($url) && isset($url[0])  ){
        return Url::to([$url[0], 'slug' => $url["slug"], "id" => $url["id"]]);
    }elseif(is_string($url)){
        return $url;
    }
 }

?>
<?php foreach($menuItems as $menu):?>
    <ul>
        <?php foreach($menu as $menuItem):?>
            <li class="header__popup-li">
                <?php if(is_array($menuItem["items"])):?>
                    <?=$menuItem["label"]?>
                    <ul class="header__popup-subpop hidden">
                        <?php foreach($menuItem["items"] as $submenuItem):?>
                            <?php if(!empty($submenuItem["items"]) &&  is_array($submenuItem["items"])):?>
                                <li>
                                    <?=$submenuItem["label"]?>
                                    <ul class="header__popup-third hidden hidden">
                                        <?php foreach($submenuItem["items"] as $lastmenuItems):?>
                                            <li><a href="<?=maekUrl($lastmenuItems["url"])?>"><?=$lastmenuItems["label"]?></a></li>
                                        <?php endforeach;?>
                                    </ul>
                                </li>
                            <?php else:?>
                                <li><a href="<?=maekUrl($submenuItem["url"])?>"><?=$submenuItem["label"]?></a></li>
                            <?php endif;?>
                        <?php endforeach;?>
                    </ul>
                <?php else:?>
                    <a <?php /*class="first-level" */?> href="<?=maekUrl($menuItem["url"])?>"><?=$menuItem["label"]?></a>
                <?php endif;?>
            </li>
        <?php endforeach;?>
    </ul>
<?php endforeach;?>
