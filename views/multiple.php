
<div class="border mx-auto text-center m-2 py-2 px-4">
    <p class="fs-4 text-primary"><?php echo $args['title'];?></p>

    <?php if ($args['thumbnail']['sizes']['swg_download_thumb']):?>

        <a href="<?php echo $args['url'];?>" target="_blank"><img src="<?php echo $args['thumbnail']['sizes']['swg_download_thumb'];?>" class="mx-auto border" alt="<?php echo $args['thumbnail']['alt'];?>" width="<?php echo $args['width'];?>" height="<?php echo $args['height'];?>" /></a>

    <?php endif ?>

    <p class="mt-3 text-uppercase"><a href="<?php echo $args['url'];?>" class="btn btn-secondary" target="_blank"><?php echo $args['button_text'];?></a></p>
</div>
