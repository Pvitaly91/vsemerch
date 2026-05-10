<? $this->registerCssFile('/css/jquery-ui.css');?>
<? $this->registerJsFile('/js/jquery-ui.js',  ['depends' => [yii\web\JqueryAsset::className()]]); ?>
<?php
$script = <<< JS
      
       $(function() {
          
            $(document).mouseup( function(e){ 
                var npDep = $( "#npDep" );
                if (!npDep.is(e.target) 
                    && npDep.has(e.target).length === 0  
                    && $("#DepGid").val() == ''){ 
                     npDep.val('');
                }
                var npCity = $( "#npCity" );
                if ( !npCity.is(e.target) 
                    && npCity.has(e.target).length === 0  
                    && $("#CityGid").val() == '') { 
                     npCity.val('');
                }
            });
         
            $( "#npCity" ).keyup(function(){
                $("#automplete-2").val('');
                $("#CityGid").val('');
                $("#DepGid").val('');
                $("#npDep").attr("disabled","disabled");
            });
            $( "#npDep" ).keyup(function(){
                $("#DepGid").val('');
            });
         
            $( "#npCity" ).autocomplete({
                source: "/api/getCitys",
                minLength: 1,
                select: function( event, ui ) {
                    $("#npDep").removeAttr("disabled");
                    $("#CityGid").val(ui.item.key);
                    $.get( "/api/getDepartaments?gid="+ui.item.key, function( data ) {
                            $( "#npDep" ).autocomplete({
                            source: data,
                            minLength: 1,
                            select: function( event, ui ) {
                                $("#DepGid").val(ui.item.key);
                            }
                        });
                    });
                
                }
            });
            
         
         });
JS;
$this->registerJs($script);
?>
<style>
    .ui-autocomplete{
        overflow: auto;
        max-height: 300px;
    }
</style>
<div class="head2">
    <div class="container">
        <div class="info2 col-md-12"></div>
            
    </div>
        
</div>

<div class="container cnt">
    <div class="col-md-12">
        <label for = "automplete-1">Tags: </label>
         <input id = "npCity">
    </div>
      <div class="col-md-12">
        <label for = "automplete-1">Tags: </label>
        <input id = "npDep" disabled="disabled">
    </div>

        <input id = "CityGid">
        <input id = "DepGid">
  
</div>