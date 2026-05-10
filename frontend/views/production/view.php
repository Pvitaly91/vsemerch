<main class="container">
    <section>
     
        <?= $this->render('/catalog/breadcrumbs',['breadcrumbs'=>$breadcrumbs]) ?>
      
        <div class="product-card-block">

            <h1><?= $text->title; ?></h1>
            
                 
             <?= $this->render('products-items',['items'=>$items]) ?>
            <div class="contect-block">
                
                <?= $body; ?>
            </div>


        </div>
    </section>
</main>
