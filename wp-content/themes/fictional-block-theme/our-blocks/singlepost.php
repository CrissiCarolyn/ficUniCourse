<?php


  while(have_posts()) {
    the_post();
    pageBanner();
     ?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
        <p><a class="metabox__blog-home-link" href="<?php echo site_url('/blog'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Blog Home</a> <span class="metabox__main">Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></span></p>
    </div>

    <div class="post-generic-content"><?php the_content(); ?>

      

        <?php
                      
          $postLikeCount = new WP_Query(array(
              'post_type' => 'like',
              'meta_query' => array(
                  array(
                      'key' => 'liked_blog_id',
                      'compare' => '=',
                      'value' => get_the_ID()
                  )
              )
          ));


          $existStatus = 'no';

          if (is_user_logged_in()) {

              $existQuery = new WP_Query(array(
                  'author' => get_current_user_id(),
                  'post_type' => 'like',
                  'meta_query' => array(
                      array(
                          'key' => 'liked_blog_id',
                          'compare' => '=',
                          'value' => get_the_ID()
                      )
                  )
              ));


              if ($existQuery->found_posts) {
                  $existStatus = 'yes';
              }
      

          }
        ?>      
    
    <p align="left">
            <i><strong> Like this blog post:</strong></i>
        </p>

        <span class="post-like-box" post-data-like=" <?php echo $existQuery->posts[0]->ID; ?>" data-blog="<?php the_ID(); ?>" data-exists-post="<?php echo $existStatus; ?>">
     
            <i class="fa fa-heart-o" aria-hidden="true"></i>    

            <i class="fa fa-heart" aria-hidden="true"></i> 

        <span class="post-like-count"><?php echo $postLikeCount->found_posts; ?></span>
    



    </div>
  </div>
</div>
    

    
  <?php }
