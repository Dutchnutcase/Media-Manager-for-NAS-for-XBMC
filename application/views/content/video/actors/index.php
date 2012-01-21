<div id="actors-index">
  <div class="block">
    <div class="content">
      <h2 class="title"><?php echo $title; ?></h2>
      <div class="inner">
        <ul class="list">
          <?php foreach($actors as $actor): ?>
          <li><a title="<?php echo $actor->name; ?>" href="<?php echo site_url('actors/'.$actor->id); ?>"><img class="photo_thumb" src="<?php echo $actor->photo->url; ?>" alt="" /></a>
            <h3><?php echo $actor->name; ?></h3>
          </li>
          <?php endforeach; ?>
        </ul>
        <hr class="clear" />
      </div><!-- inner -->
      <div id="actions-bar" class="actions-bar wat-cf">
        <?php echo $this->my_pagination->create_links(); ?>
      </div><!-- actions-bar -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- actors-index -->
