<div class="error-body text-center">
    <h3 class="text-uppercase"><?=$title?></h3>
    <p class="text-muted m-t-30 m-b-30"><?=$text?></p>
    <?php
    if($backURL){
        ?>
        <a href="<?=$backURL?>" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40"><?=$backButtonTitle?></a>
        <?php
    }
    ?>
</div>