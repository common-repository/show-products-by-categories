<?php
	class show_by_categories_widget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'show_by_categories_widget', 
				__('Show products by categories', 'show_by_categories_widget_domain'), 
				array( 'description' => __( 'The widget displays products by categories that you have chosen when create a page or post, if nothing is selected, then nothing will be displayed', 'show_by_categories_widget_domain' ), ) 
			);
		}
		public function widget( $args, $instance ) {
			global $post;
			if(!empty($post)&&isset($post->ID)){
				$current_cats=get_post_meta($post->ID, 'cat_for_right_slidebar_show');
				if(!empty($current_cats[0])){
					$in_cat_list=$current_cats[0];
					$title = apply_filters( 'widget_title', $instance['title'] );
					echo $args['before_widget'];
					if ( ! empty( $title ) )
						echo $args['before_title'] . $title . $args['after_title'];
					$cats=join(",",$in_cat_list);
					$args = array(
				        'posts_per_page' => -1,
				        'product_cat' => $cats,
				        'post_type' => 'product',
				        'orderby' => 'rand',
				        'posts_per_page' => $instance['count']
				    );
					$the_query = new WP_Query( $args );?>

					<ul class="product_list_widget">
					    <?php if ($the_query->have_posts()) : ?>
						    <?php while ($the_query->have_posts()) :
						    	$the_query->the_post(); 
						    	global $product;
						    	?>
						     	<li class="media">
						     		<a class="thumb-holder" href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
						     		<div class="media-body">
							          	<h4 class="media-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							          	<div class="price"><ins><?php echo $product->get_price_html(); ?></ins></div>
							        </div>
						     	</li>
					     	<?php endwhile; ?>
						    <?php wp_reset_postdata(); ?>
					    <?php endif; ?>
					</ul>
					<?php
					//echo $args['after_widget'];
					echo "</aside>";
				}
			}
		}
			
		public function form( $instance ) {
			if($instance){
				$title = esc_attr($instance[ 'title' ]);
				$count = $instance[ 'count' ];
			}else {
				$title = "";
				$count = "10";
			}
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'show_by_categories_widget'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count of products:', 'show_by_categories_widget'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" max="" 
				value="<?php echo esc_attr( $count ); ?>">
			</p>
			<?php 
		}
		
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '10';
			return $instance;
		}
	} 

	function show_by_categories_load_widget() {
		register_widget( 'show_by_categories_widget' );
	}
	add_action( 'widgets_init', 'show_by_categories_load_widget' );
	
	add_action( 'save_post', 'show_by_categories_save' );
	function show_by_categories_save( $post_id ){
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'show_by_categories_meta_box_nonce' ) ) return;
	    if( !current_user_can( 'edit_post' ) ) return;
	    if(isset($_POST['cat_for_right_slidebar_show'])){
		    update_post_meta( $post_id, 'cat_for_right_slidebar_show', array_map( 'strip_tags', $_POST['cat_for_right_slidebar_show'])); 
		}else{
			delete_post_meta( $post_id, 'cat_for_right_slidebar_show');
		}
	}

	function show_by_categories_content() {
		global $post;
		$in_cat_list=array();
		$current_cats=get_post_meta($post->ID, 'cat_for_right_slidebar_show');
		if(!empty($current_cats[0])){
			$in_cat_list=$current_cats[0];
		}
		echo "<p>Select a category to display in the sidebar (<i>If nothing is selected, then nothing will be shown, showing the nesting level not more than 2, <b>also do not forget to add a widget to the panel</b></i>):</p>";
		echo '<div class="rsbcategorydiv" style="overflow:auto; height:200px; border:1px solid #dfdfdf; background-color: #fdfdfd;box-sizing:border-box;width:100%;padding:3px 10px;">';
		$args = array(
			'taxonomy' => 'product_cat',
			'orderby' => 'name',
			'show_count' => 0,
			'pad_counts' => 0,
			'hierarchical' => 1,
			'title_li' => '',
			'hide_empty' => 0
		);
		$all_categories = get_categories( $args );
		echo "<ul class='categorychecklist form-no-clear' style='margin-top:0px;'>";
		wp_nonce_field( 'show_by_categories_meta_box_nonce', 'meta_box_nonce' );
		foreach ($all_categories as $cat){
			if($cat->category_parent == 0){
				$category_id = $cat->term_id;
				$checked_cat="";
				if (in_array($cat->slug, $in_cat_list)) {
					$checked_cat="checked='checked'";
				}
				echo "<li><label><input type='checkbox' ".$checked_cat." value='".$cat->slug."' name='cat_for_right_slidebar_show[]'> ".$cat->name;
				$args2 = array(
					'taxonomy' => 'product_cat',
					'child_of' => 0,
					'parent' => $category_id,
					'orderby' => 'name',
					'show_count' => 0,
					'pad_counts' => 0,
					'hierarchical' => 1,
					'title_li' => '',
					'hide_empty' => 0
				);
				$sub_cats = get_categories( $args2 );
				if($sub_cats){
					echo "<ul class='children' style='margin-left:20px;'>";
					foreach($sub_cats as $sub_category){
						$checked_subcat="";
						if (in_array($sub_category->slug, $in_cat_list)) {
							$checked_subcat="checked='checked'";
						}
						echo "<li><label><input type='checkbox' ".$checked_subcat." value='".$sub_category->slug."' name='cat_for_right_slidebar_show[]'> ".$sub_category->cat_name."</label></li>";
					}
					echo "</ul>";
				}
				echo "</label></li>";
			}
		}
		echo "</ul>";
		echo "</div>";
	}

	add_action( 'add_meta_boxes', 'show_by_categories_meta_box_add' );
	function show_by_categories_meta_box_add(){
	    add_meta_box( 'show_by_categories', 'Customize widget display products in a sidebar on the page of this post', 'show_by_categories_content', 'post', 'normal', 'high' );
	    add_meta_box( 'show_by_categories', 'Customize widget display products in the sidebar on this page', 'show_by_categories_content', 'page', 'normal', 'high' );
	}

	function show_by_categories_init() {
		global $show_by_categories_name; 
		$current_locale = get_locale(); 
		if(!empty($current_locale)) {
			load_plugin_textdomain($show_by_categories_name, false, $show_by_categories_name."/languages/"); 
		}
		wp_enqueue_style( 'worker_finder-style', plugins_url("/css/style.css",__FILE__));
	}
	add_action("init", "show_by_categories_init"); 
	
?>
