<?php

add_action('rest_api_init', 'universityBlogLikeRoutes');

function universityBlogLikeRoutes() {
  register_rest_route('university/v1', 'managePostLike', array(
    'methods' => 'POST',
    'callback' => 'createLikePost'
  ));

  register_rest_route('university/v1', 'managePostLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLikePost'
  ));
}

function createLikePost($data) {
  if (is_user_logged_in()) {
    $blog = sanitize_text_field($data['blogId']);

    $existQuery = new WP_Query(array(
      'author' => get_current_user_id(),
      'post_type' => 'like',
      'meta_query' => array(
        array(
          'key' => 'liked_blog_id',
          'compare' => '=',
          'value' => $blog
        )
      )
    ));

    if ($existQuery->found_posts == 0 AND get_post_type($blog) == 'post') {
      return wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => 'Blog like',
        'meta_input' => array(
          'liked_blog_id' => $blog
        )
      ));
    } else {
      die("Invalid blog id");
    }

    
  } else {
    die("Only logged in users can create a like.");
  }

  
}

function deleteLikePost($data) {
  $likeId = sanitize_text_field($data['like']);
  if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true);
    return 'Like deleted.';
  } else {
    die("You do not have permission to delete that.");
  }
}