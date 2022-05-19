
<div class="border d-inline-block mx-auto text-center m-2 py-2 px-4">
    <div class="position-relative h-100  pb-5">
        <p class="fs-5 text-primary mb-2"><?php echo $args['title'];?></p>

        <?php if (isset($args['thumbnail'])):?>

        <a href="<?php echo $args['url'];?>" target="_blank"><img src="<?php echo $args['thumbnail'];?>" class="mx-auto border" alt="<?php echo $args['thumbnail_alt'];?>" width="<?php echo $args['width'];?>" height="<?php echo $args['height'];?>" /></a>

        <?php endif ?>

        <p class="mt-3 text-uppercase position-absolute bottom-0 w-100 px-2"><a href="<?php echo $args['url'];?>" class="btn btn-secondary" target="_blank"><?php echo $args['button_text'];?></a></p>
    </div>
</div>
