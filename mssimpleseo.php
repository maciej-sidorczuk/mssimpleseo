<?php

/**
 * @package Simple_SEO
 * @version 1.0
 */
/*
Plugin Name: Simple SEO
Plugin URI: http://sidorczuk.com.pl
Description: Simple SEO for posts, categories and pages.
Author: Maciej Sidorczuk
Version: 1.0
Author URI: http://sidorczuk.com.pl
*/

function insert_seo_fields_in_post_edit_screen() {
  add_meta_box( 'meta_description', 'Simple SEO fields: ', 'add_metadescription', 'post', 'side', 'default' );
  add_meta_box( 'meta_description', 'Simple SEO fields: ', 'add_metadescription', 'page', 'side', 'default' );
  function add_metadescription($post) {
    $keywords = get_post_meta( $post->ID, '_keywords', true);
    $canonical_url = get_post_meta( $post->ID, '_canonical_url', true);
    $metadescription = get_post_meta( $post->ID, '_metadescription', true);
    echo 'meta description: ';
    ?>
    <textarea name="metadescription" rows="4" cols="35" form="post"><?php echo esc_attr( $metadescription ); ?></textarea>
    <?php echo '<br />keywords: ' ?>
    <input type="text" name="keywords" value="<?php echo esc_attr( $keywords ); ?>" />
    <?php echo 'canonical URL: ' ?>
    <input type="text" name="canonical_url" value="<?php echo esc_attr( $canonical_url ); ?>" />
    <?php
  }
}

add_action( 'add_meta_boxes', 'insert_seo_fields_in_post_edit_screen' );

function save_seo_fields($post_ID ) {
  global $post;
  if (isset( $_POST ) ) {
    update_post_meta( $post_ID, '_keywords', strip_tags( $_POST['keywords'] ) );
    update_post_meta( $post_ID, '_canonical_url', strip_tags( $_POST['canonical_url'] ) );
    update_post_meta( $post_ID, '_metadescription', strip_tags( $_POST['metadescription'] ) );
  }
}

add_action( 'save_post', 'save_seo_fields' );

function save_category_seo_fileds( $term_id ) {
    if ( isset( $_POST['Cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['Cat_meta'][$key])){
                $cat_meta[$key] = $_POST['Cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}

add_action ( 'edited_category', 'save_category_seo_fileds');

function category_seo_fields( $tag ) {
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id");
?>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="extra1">Simple SEO fields:</label></th>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="metadescription"><?php _e('meta description: '); ?></label></th>
      <td>
        <textarea name="Cat_meta[metadescription]" id="Cat_meta[metadescription]" style="width:60%;"><?php echo $cat_meta['metadescription'] ? $cat_meta['metadescription'] : ''; ?></textarea><br />
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="keywords"><?php _e('keywords: '); ?></label></th>
      <td>
        <input type="text" name="Cat_meta[keywords]" id="Cat_meta[keywords]" size="25" style="width:60%;" value="<?php echo $cat_meta['keywords'] ? $cat_meta['keywords'] : ''; ?>"><br />
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><label for="canonical_url"><?php _e('canonical URL: '); ?></label></th>
      <td>
        <input type="text" name="Cat_meta[canonical_url]" id="Cat_meta[canonical_url]" size="25" style="width:60%;" value="<?php echo $cat_meta['canonical_url'] ? $cat_meta['canonical_url'] : ''; ?>"><br />
      </td>
    </tr>
<?php
}

add_action ( 'edit_category_form_fields', 'category_seo_fields');

function insert_meta_tags_frontend() {
  global $post;
  if (is_category()) {
    $cat_id = get_query_var('cat');
    $cat_data = get_option("category_$cat_id");
    $meta_description = $cat_data['metadescription'];
    $keywords = $cat_data['keywords'];
    $canonical_url = $cat_data['canonical_url'];
  } else {
    $post_id = $post->ID;
    $meta_description = get_post_meta( $post_id, '_metadescription', true);
    $keywords = get_post_meta( $post_id, '_keywords', true);
    $canonical_url = get_post_meta( $post_id, '_canonical_url', true);
  }
  /* TODO metatags in search page result */
  /* TODO metatags in recent posts view */
  if(!is_search() && !is_home()) {
    echo '<meta name="description" content="' . $meta_description . '">' . "\n";
    echo '<meta name="keywords" content="' . $keywords . '">' . "\n";
    if(isset($canonical_url) && !empty($canonical_url)) {
      remove_action('wp_head', 'rel_canonical');
      remove_action('embed_head', 'rel_canonical');
      echo '<link rel="canonical" href="' . $canonical_url . '"/>' . "\n";
    }
  }
}

add_action( 'wp_head', 'insert_meta_tags_frontend', 1);

 ?>
