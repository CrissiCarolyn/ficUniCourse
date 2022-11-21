<?php

require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/like-posts-route.php');
require get_theme_file_path('/inc/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function () {return get_the_author();}
    ));

    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function () {return count_user_posts(get_current_user_id(), 'note');}
    ));
 
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL) {
    if(!$args['title']) {
        $args['title'] = get_the_title();
    }

    if(!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if(!$args['photo']) {
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }



?>
<div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
        <div class="page-banner__intro">
          <p> <?php echo $args['subtitle']; ?></p>
        </div>
      </div>
    </div>

<?php }


/*function HeroSlider($args = NULL) {
    if(!$args['SliderTitle']) {
        $args['SliderTitle'] = get_field('slide_title');
    }

    if(!$args['SliderSubtitle']) {
        $args['SliderSubtitle'] = get_field('slide_subtitle');
    }

    if(!$args['buttonText']) {
        $args['buttonText'] = get_field('button_text');
    }

    if(!$args['buttonLink']) {
        $args['buttonLink'] = get_field('button_link');
    }

    if(!$args['SliderPhoto']) {
        $args['SliderPhoto'] = get_field('slider_image')['sizes']['HeroSlider'];
    } else {
        $args['SliderPhoto'] = get_theme_file_uri('/images/ocean.jpg');
    }
    
?>
    <div class="hero-slider">
        <div data-glide-el="track" class="glide__track">
            <div class="glide__slides">
                <div class="hero-slider__slide" style="background-image: url(<?php echo $args['slider_image']; ?>);">
                    <div class="hero-slider__interior container">
                        <div class="hero-slider__overlay">
                            <h2 class="headline headline--medium t-center"><?php echo $args['slide_title'] ?></h2>
                            <p class="t-center"><?php echo $args['slide_subtitle'] ?></p>
                            <p class="t-center no-margin"><a href="#" class="btn btn--blue"><?php echo $args['button_text'] ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]"></div>
        </div>
    </div>

<?php }
*/

function university_files() {
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBQ6FLrRTK4Xpg6-zT1h8oiTvaugDAeUKQ', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
    
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
    add_image_size('HeroSlider', 1900, 525, true);
    add_theme_support('editor-styles');
    add_editor_style(array('https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i', 'build/style-index.css', 'build/index.css'));
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query) {
    if (!is_admin() AND is_post_type_archive('campus') AND is_main_query()) {
         $query->set('posts_per_page', -1);
    }
    
    
    if (!is_admin() AND is_post_type_archive('program') AND is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }


    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric'
            )
          ));

    }

}

add_action('pre_get_posts', 'university_adjust_queries');

function universityMapKey($api) {
    $api['key'] = 'AIzaSyBQ6FLrRTK4Xpg6-zT1h8oiTvaugDAeUKQ';
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');


// redirect sub acc to home page
add_action('admin_init', 'redirectSubstoFrontend');

function redirectSubstoFrontend() {
$CurrentUser = wp_get_current_user();

    if (count($CurrentUser->roles) == 1 AND $CurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url(''));
        exit;
    }
}


// hiding top admin bar 

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
$CurrentUser = wp_get_current_user();

    if (count($CurrentUser->roles) == 1 AND $CurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}


// customise login screen

add_filter('login_headerurl', 'HeaderUrl');

function HeaderUrl() {
    return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'LoginCSS');

function LoginCSS() {
    wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

}


add_filter('login_headertitle', 'LoginpageTitle');

function LoginpageTitle() {
    return get_bloginfo('name');
}

// Note posts set to private

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) {

    if($data['post_type'] == 'note') {

        if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die("You have reached your note limit.");

        }

        $data['post_title'] = sanitize_text_field($data['post_title']);
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
    }

    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }

    return $data;
}


class PlaceholderBlock {
    function __construct($name) {
        $this->name = $name;
        add_action('init', [$this, 'onInit']);

    }

    function ourRenderCallback($attributes, $content) {
        ob_start();
        require get_theme_file_path("/our-blocks/{$this->name}.php");

        return ob_get_clean();


    }

    function onInit() {
        wp_register_script($this->name, get_stylesheet_directory_uri() . "/our-blocks/{$this->name}.js", array('wp-blocks', 'wp-editor'));

        register_block_type("ourblocktheme/{$this->name}", array(
            'editor_script' => $this->name,
            'render_callback' => [$this, 'ourRenderCallback']
        ));
    }
}

new PlaceholderBlock("eventsandblogs");
new PlaceholderBlock("header");
new PlaceholderBlock("footer");
new PlaceholderBlock("singlepost");
new PlaceholderBlock("page");
new PlaceholderBlock("blogindex");
new PlaceholderBlock("programarchive");
new PlaceholderBlock("singleprogram");
new PlaceholderBlock("singleprofessor");
new PlaceholderBlock("mynotes");
new PlaceholderBlock("eventarchive");
new PlaceholderBlock("singleevent");
new PlaceholderBlock("pastevents");
new PlaceholderBlock("archivecampus");
new PlaceholderBlock("singlecampus");




class JSXBlock {
    function __construct($name, $renderCallback = null, $data = null) {
        $this->name = $name;
        $this->data = $data;
        $this->renderCallback = $renderCallback;
        add_action('init', [$this, 'onInit']);

    }

    function ourRenderCallback($attributes, $content) {
        ob_start();
        require get_theme_file_path("/our-blocks/{$this->name}.php");

        return ob_get_clean();


    }

    function onInit() {
        wp_register_script($this->name, get_stylesheet_directory_uri() . "/build/{$this->name}.js", array('wp-blocks', 'wp-editor'));

        if ($this->data) {
            wp_localize_script($this->name, $this->name, $this->data);

        }

        $ourArgs = array(
            'editor_script' => $this->name
        );

        if($this->renderCallback) {
            $ourArgs['render_callback'] = [$this, 'ourRenderCallback'];

        }


        register_block_type("ourblocktheme/{$this->name}", $ourArgs);

    }

}

new JSXBlock('banner', true, ['fallbackimage' => get_theme_file_uri('/images/library-hero.jpg')]);
new JSXBlock('genericheading');
new JSXBlock('genericbutton');
new JSXBlock('slideshow', true);
new JSXBlock('slide', true, ['themeimagepath' => get_theme_file_uri('/images/')]);
 
function myAllowedBlocks($allowed_block_types, $editor_context) {



    // if on page/post editor
    if(!empty($editor_context->post)) {
        return $allowed_block_types;
    }

    // on full site editor 

    return array('ourblocktheme/header', 'ourblocktheme/footer', 'ourblocktheme/eventsandblogs', 'ourblocktheme/genericheading', 'ourblocktheme/slideshow', 'ourblocktheme/banner');

}


add_filter('allowed_block_types_all', 'myAllowedBlocks', 10, 2);