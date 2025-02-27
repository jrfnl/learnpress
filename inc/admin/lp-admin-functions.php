<?php
/**
 * Common functions used for admin
 *
 * @package   LearnPress
 * @author    ThimPress
 * @version   1.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! function_exists( 'learn_press_add_row_action_link' ) ) {
	/**
	 * Setup action links to the admin course, lesson, quiz, question. e.g: Add Duplicate link, hide View link for lesson, quiz so on.
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	function learn_press_add_row_action_link( $actions ) {

		global $post;

		if ( LP_COURSE_CPT == $post->post_type ) {
			$duplicate_link = '#';
			$duplicate_link = array(
				array(
					'link'  => $duplicate_link,
					'title' => _x( 'Copy', 'copy course', 'learnpress' ),
					'class' => 'lp-duplicate-post lp-duplicate-course',
					'data'  => $post->ID
				)
			);
			$links          = apply_filters( 'learn_press_row_action_links', $duplicate_link );
			if ( count( $links ) > 1 ) {
				$drop_down = array( '<ul class="lpr-row-action-dropdown">' );
				foreach ( $links as $link ) {
					$drop_down[] = '<li>' . sprintf( '<a href="%s" class="%s" data-post-id="%s">%s</a>', $link['link'], $link['class'], $link['data'], $link['title'] ) . '</li>';
				};
				$drop_down[] = '</ul>';
				$link        = sprintf( '<div class="lpr-row-actions"><a href="%s">%s</a>%s</div>', 'javascript: void(0);', __( 'Course', 'learnpress' ), join( "\n", $drop_down ) );
			} else {
				$link = array_shift( $links );
				$link = sprintf( '<a href="%s" class="%s" data-post-id="%s">%s</a>', $link['link'], $link['class'], $link['data'], $link['title'] );
			}
			$actions['lp-duplicate-row-action'] = $link;
		} else if ( LP_QUIZ_CPT === $post->post_type ) {
			unset( $actions['view'] );
			$link                               = sprintf( '<a href="#" class="lp-duplicate-post lp-duplicate-quiz" data-post-id="%s">%s</a>', $post->ID, _x( 'Copy', 'copy quiz', 'learnpress' ) );
			$actions['lp-duplicate-row-action'] = $link;
		} else if ( LP_QUESTION_CPT === $post->post_type ) {
			unset( $actions['view'] );
			$link                               = sprintf( '<a href="#" class="lp-duplicate-post lp-duplicate-question" data-post-id="%s">%s</a>', $post->ID, _x( 'Copy', 'copy question', 'learnpress' ) );
			$actions['lp-duplicate-row-action'] = $link;
		} else if ( LP_LESSON_CPT === $post->post_type ) {
			unset( $actions['view'] );
			$link                               = sprintf( '<a href="#" class="lp-duplicate-post lp-duplicate-lesson" data-post-id="%s">%s</a>', $post->ID, _x( 'Copy', 'copy lesson', 'learnpress' ) );
			$actions['lp-duplicate-row-action'] = $link;
		}

		return apply_filters( 'learn-press/row-action-links', $actions );
	}

	add_filter( 'post_row_actions', 'learn_press_add_row_action_link' );
	add_filter( 'page_row_actions', 'learn_press_add_row_action_link' );
}

if ( ! function_exists( 'learn_press_settings_tabs_array' ) ) {
	/**
	 * Default admin settings pages
	 *
	 * @return mixed
	 */
	function learn_press_settings_tabs_array() {
		$default_tabs = array(
			'general'  => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-general.php",
			'courses'  => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-courses.php",
			'profile'  => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-profile.php",
			'payments' => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-payments.php",
			// 3.x.x
			//'pages'    => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-pages.php",
			'emails'   => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-emails.php",
			'advanced' => include_once LP_PLUGIN_PATH . "inc/admin/settings/class-lp-settings-advanced.php",
		);

		// Deprecated
		$tabs = apply_filters( 'learn_press_settings_tabs_array', $default_tabs );

		return apply_filters( 'learn-press/admin/settings-tabs-array', $tabs );
	}
}

/*******************************/

function learn_press_is_hidden_post_box( $id, $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	$data = learn_press_get_user_option( 'post-closed-box' );
	if ( ! $data ) {
		$data = array();
	}

	return false !== array_search( $id, $data );
}

/**
 * Get html view path for admin to display
 *
 * @param $name
 * @param $plugin_file
 *
 * @return mixed
 */
function learn_press_get_admin_view( $name, $plugin_file = null ) {
	if ( ! preg_match( '/\.(html|php)$/', $name ) ) {
		$name .= '.php';
	}
	if ( $plugin_file ) {
		$view = dirname( $plugin_file ) . '/inc/admin/views/' . $name;
	} else {
		$view = LP()->plugin_path( 'inc/admin/views/' . $name );
	}

	return apply_filters( 'learn_press_admin_view', $view, $name );
}

function learn_press_admin_view_content( $name, $args = array() ) {
	return learn_press_admin_view( $name, $args, false, true );
}

/**
 * Find a full path of a view and display the content in admin
 *
 * @param            $name
 * @param array      $args
 * @param bool|false $include_once
 * @param            bool
 *
 * @return bool
 */
function learn_press_admin_view( $name, $args = array(), $include_once = false, $return = false ) {
	$view = learn_press_get_admin_view( $name, ! empty( $args['plugin_file'] ) ? $args['plugin_file'] : null );

	if ( file_exists( $view ) ) {
		ob_start();
		// extract parameters as local variables if passed
		is_array( $args ) && extract( $args );
		do_action( 'learn_press_before_display_admin_view', $name, $args );
		if ( $include_once ) {
			include_once $view;
		} else {
			include $view;
		}
		do_action( 'learn_press_after_display_admin_view', $name, $args );
		$output = ob_get_clean();
		if ( ! $return ) {
			echo $output;
		}

		return $return ? $output : true;
	}

	return false;
}

/**
 * List all pages as a dropdown with "Add New Page" option
 *
 * @param            $name
 * @param bool|false $selected
 * @param array      $args
 *
 * @return mixed|string
 */
function learn_press_pages_dropdown( $name, $selected = false, $args = array() ) {
	$id           = null;
	$class        = null;
	$css          = null;
	$before       = array(
		'add_new_page' => __( '[ Add a new page ]', 'learnpress' )
	);
	$after        = null;
	$echo         = true;
	$allow_create = true;

	if ( func_num_args() == 1 && is_array( $name ) ) {
		$args = $name;
	}

	is_array( $args ) && extract( $args );

	if ( empty( $id ) ) {
		$id = $name;
	}

	$class .= 'list-pages lp-list-pages';

	$args    = array(
		'name'             => $name,
		'id'               => $id,
		'sort_column'      => 'menu_order',
		'sort_order'       => 'ASC',
		'show_option_none' => __( 'Select Page', 'learnpress' ),
		'class'            => $class,
		'echo'             => false,
		'selected'         => $selected,
		'allow_create'     => true,
	);
	$output  = wp_dropdown_pages( $args );
	$replace = "";

	if ( $class ) {
		$replace .= ' class="' . $class . '"';
	}
	if ( $css ) {
		$replace .= ' style="' . $css . '"';
	}

	$replace .= ' data-selected="' . $selected . '"';
	$replace .= " data-placeholder='" . __( 'Select a page&hellip;', 'learnpress' ) . "' id=";
	$output  = '<div class="list-pages-wrapper">' . str_replace( ' id=', $replace, $output );

	if ( $before ) {
		$before_output = array();
		foreach ( $before as $v => $l ) {
			$before_output[] = sprintf( '<option value="%s">%s</option>', $v, $l );
		}
		$before_output = join( "\n", $before_output );
		$output        = preg_replace( '!(<option class=".*" value="[0-9]+".*>.*</option>)!', $before_output . "\n$1", $output, 1 );
	}

	$output = str_replace( '<option class="level-0" value="00000">#0 (no title)</option>', '', $output );

	if ( $selected && get_post_status( $selected ) !== 'publish' ) {
		$selected = 0;
	}

	if ( $allow_create ) {
		ob_start(); ?>
        <button class="button button-quick-add-page" data-id="<?php echo $id; ?>"
                type="button"><?php _e( 'Create', 'learnpress' ); ?></button>
		<?php echo '</div>'; ?>
        <p class="quick-add-page-inline <?php echo $id; ?> hide-if-js">
            <input type="text" placeholder="<?php esc_attr_e( 'New page title', 'learnpress' ); ?>"/>
            <button class="button" type="button"><?php esc_html_e( 'Ok [Enter]', 'learnpress' ); ?></button>
            <a href=""><?php _e( 'Cancel [ESC]', 'learnpress' ); ?></a>
        </p>
        <p class="quick-add-page-actions <?php echo $id; ?><?php echo $selected ? '' : ' hide-if-js'; ?>">
            <a class="edit-page" href="<?php echo get_edit_post_link( $selected ); ?>"
               target="_blank"><?php _e( 'Edit Page', 'learnpress' ); ?></a>
            <a class="view-page" href="<?php echo get_permalink( $selected ); ?>"
               target="_blank"><?php _e( 'View Page', 'learnpress' ); ?></a>
        </p>
		<?php $output .= ob_get_clean();
	} else {
		$output .= '</div>';
	}

	$output = sprintf( '<div class="learn-press-dropdown-pages">%s</div>', $output );

	if ( $echo ) {
		echo $output;
	}

	return $output;
}

/**
 * List all registered question types into dropdown
 *
 * @param array
 *
 * @return string
 */
function learn_press_dropdown_question_types( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'name'     => 'learn-press-dropdown-question-types',
			'id'       => '',
			'class'    => '',
			'selected' => '',
			'echo'     => true
		)
	);
	if ( ! $args['id'] ) {
		$args['id'] = $args['name'];
	}
	$args['class'] = 'lp-dropdown-question-types' . ( $args['class'] ? ' ' . $args['class'] : '' );
	$types         = learn_press_question_types();
	$output        = sprintf( '<select name="%s" id="%s" class="%s"%s>', $args['name'], $args['id'], $args['class'], $args['selected'] ? 'data-selected="' . $args['selected'] . '"' : '' );
	foreach ( $types as $slug => $name ) {
		$output .= sprintf( '<option value="%s"%s>%s</option>', $slug, selected( $slug == $args['selected'], true, false ), $name );
	}
	$output .= '</select>';
	if ( $args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * List all registered question types into dropdown
 *
 * @param array
 *
 * @return string
 */
function learn_press_field_question_duration( $args = array(), $question ) {
	global $post;
	$duration_type = get_post_meta( $post->ID, "_lp_duration_type", true );
	$value         = get_post_meta( $question->id, '_question_duration', true );

	$wrap_class = 'learn-press-question-duration';
	if ( 'questions_duration' !== $duration_type ) {
		$wrap_class .= ' hide';
	}
	$args          = wp_parse_args(
		$args,
		array(
			'name'        => 'learn_press_question[' . $question->id . '][duration]',
			'id'          => 'learn-press-question-duration-' . $question->id,
			'class'       => 'learn-press-question-duration',
			'selected'    => '',
			'echo'        => true,
			'value'       => 0,
			'step'        => 1,
			'min'         => 0,
			'placeholder' => __( 'Minutes', 'learnpress' ),
		)
	);
	$args['value'] = $value;

	if ( ! $args['id'] ) {
		$args['id'] = $args['name'];
	}

	return '<span class="' . esc_attr( $wrap_class ) . '">' . sprintf(
			'<input type="number" class="%s" name="%s" id="%s" value="%s" step="%s" min="%s" max="%s" placeholder="%s"/>',
			$args['class'],
			$args['name'],
			empty( $args['clone'] ) ? $args['id'] : '',
			$args['value'],
			$args['step'],
			$args['min'],
			! empty( $args['max'] ) ? $args['max'] : '',
			$args['placeholder']
		) . $args['placeholder'] . '</span>';
}

/**
 * Displays email formats support into a dropdown
 *
 * @param array $args
 *
 * @return string
 */
function learn_press_email_formats_dropdown( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'name'        => 'learn-press-dropdown-email-formats',
			'id'          => '',
			'class'       => '',
			'selected'    => '',
			'option_none' => '',
			'echo'        => true
		)
	);

	$formats = array(
		'plain_text' => __( 'Plain Text', 'learnpress' ),
		'html'       => __( 'HTML', 'learnpress' ),
	);

	if ( empty( $args['id'] ) ) {
		$args['id'] = sanitize_file_name( $args['name'] );
	}

	$output = sprintf( '<select name="%s" id="%s" class="%s" %s>', $args['name'], $args['id'], $args['class'], '' );

	if ( $args['option_none'] ) {
		if ( is_array( $args['option_none'] ) ) {
			$text  = reset( $args['option_none'] );
			$value = key( $args['option_none'] );
		} else {
			$text  = $args['option_none'];
			$value = '';
		}

		$output .= sprintf( '<option value="%s">%s</option>', $value, $text );
	}

	foreach ( $formats as $name => $text ) {
		$output .= sprintf( '<option value="%s" %s>%s</option>', $name, selected( $args['selected'] == $name, true, false ), $text ) . "\n";
	}
	$output .= '</select>';

	if ( $args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Return array of email formats.
 *
 * @return mixed
 */
function learn_press_email_formats() {
	$formats = array(
		'plain' => __( 'Plain Text', 'learnpress' ),
		'html'  => __( 'HTML', 'learnpress' ),
	);

	return apply_filters( 'learn-press/email-formats', $formats );
}

function learn_press_trim_content( $content, $count = 0 ) {
	$content = preg_replace( '/(?<=\S,)(?=\S)/', ' ', $content );
	$content = str_replace( "\n", ' ', $content );
	$content = explode( " ", $content );

	$count = $count > 0 ? $count : sizeof( $content ) - 1;
	$full  = $count >= sizeof( $content ) - 1;

	$content = array_slice( $content, 0, $count );
	$content = implode( " ", $content );
	if ( ! $full ) {
		$content .= '...';
	}

	return $content;
}

/**
 * Get list of themes that support LearnPress.
 *
 * @return mixed
 */
function learn_press_get_education_themes() {

	// New theme can be added here
	return apply_filters(
		'learn-press/education-themes',
		array(
			'23451388' => 'kindergarten',
			'22773871' => 'ivy-school',
			'20370918' => 'wordpress-lms',
			'14058034' => 'eduma',
			'17097658' => 'coach',
			'11797847' => 'lms'
		)
	);
}

if ( ! function_exists( 'learn_press_get_item_referral' ) ) {
	/**
	 * Set item link referral.
	 *
	 * @param int|string $item_id
	 *
	 * @return string
	 */
	function learn_press_get_item_referral( $item_id ) {
//		return array(
//			'ref'        => 'ThimPress',
//			'utm_source' => 'lp-backend',
//			'utm_medium' => 'lp-addondashboard'
//		);

		$affiliate_links = array(
			14058034      => 'https://1.envato.market/Yx2YR', // eduma
			22773871      => 'https://1.envato.market/akrzZ', // ivy-school
			20370918      => 'https://1.envato.market/13Zkd', // wordpress-lms
			17097658      => 'https://1.envato.market/Xq2Ra', // coach
			23451388      => 'https://1.envato.market/oWov9',
			11797847      => 'https://1.envato.market/zknvM',
//			'Sailing'     => 'https://1.envato.market/G5Rkk',
//			'Hotel'       => 'https://1.envato.market/VW2K3',
//			'Megabuilder' => 'https://1.envato.market/03R5V',
//			'GALAX'       => 'https://1.envato.market/qqO6y',
//			'MAGAZET'     => 'https://1.envato.market/xPz65',
//			'CHARITY'     => 'https://1.envato.market/jgJ65'
		);

		return isset( $affiliate_links[ $item_id ] ) ? $affiliate_links[ $item_id ] : '';
	}
}

/**
 * Display advertisement about related themes at the bottom of admin pages.
 *
 * @updated 12 Nov 2018 - Enable/Disable shuffle the list of themes
 *
 * @return bool|void
 */
function learn_press_footer_advertisement() {
	// Only show in our custom post types
	$admin_post_type = array(
		'lp_course',
		'lp_lesson',
		'lp_quiz',
		'lp_question',
		'lp_order'
	);

	// And our admin pages
	$pages = array(
		'learnpress_page_learn-press-statistics',
		'learnpress_page_learn-press-settings',
		'learnpress_page_learn-press-tools'

	);
	if ( ! $screen = get_current_screen() ) {
		return;
	}
	if ( ! ( ( in_array( $screen->post_type, $admin_post_type ) && $screen->base === 'edit' ) || ( in_array( $screen->id, $pages ) ) ) ) {
		return;
	}

	$theme_ids     = learn_press_get_education_themes();
	$current_theme = wp_get_theme();


	$include = array_keys( $theme_ids );

	if ( false !== ( $key = array_search( $current_theme->name, $theme_ids, true ) ) ) {
		unset( $theme_ids[ $key ] );
	}

	// Get items education
	$list_themes = (array) LP_Plugins_Helper::get_related_themes(
		'education',
		array(
			'include' => $include
		)
	);

	if ( empty ( $list_themes ) ) {
		return;
	}

	// Disable shuffle themes for 3 days
	$shuffle = LP()->settings()->get( 'ad_shuffle_themes' );

	if ( ! $shuffle ) {
		if ( wp_next_scheduled( 'learn-press/schedule-enable-shuffle-themes' ) === false ) {
			wp_schedule_single_event( time() + 3 * DAY_IN_SECONDS, 'learn-press/schedule-enable-shuffle-themes' );
		}
		// Keep the first theme always in #1 and shuffle other themes
		$first_theme = array_shift( $list_themes );
		shuffle( $list_themes );
		array_unshift( $list_themes, $first_theme );
	} else {
		shuffle( $list_themes );
	}

	//$query_arg = learn_press_get_item_referral();

	?>

    <div id="learn-press-advertisement" class="learn-press-advertisement-slider">
		<?php
		foreach ( $list_themes as $theme ) {
			if ( empty( $theme['url'] ) ) {
				continue;
			}
			//$theme['url'] = add_query_arg( $query_arg, $theme['url'] );
			//$theme['description'] = learn_press_trim_content( $theme['description'], 10 );

			$url               = learn_press_get_item_referral( $theme['id'] );
			$full_description  = learn_press_trim_content( $theme['description'] );
			$short_description = learn_press_trim_content( $theme['description'], 75 );

			$url_demo = $theme['attributes'][4]['value'];

			?>
            <div id="thimpress-<?php echo esc_attr( $theme['id'] ); ?>" class="slide-item">
                <div class="slide-thumbnail">
                    <a href="<?php echo esc_url( $url ); ?>">
                        <img src="<?php echo esc_url( $theme['previews']['landscape_preview']['landscape_url'] ) ?>"/>
                    </a>
                </div>

                <div class="slide-detail">
                    <h2><a href="<?php echo esc_url( $url ); ?>"><?php echo $theme['name']; ?></a></h2>
                    <p class="slide-description description-full">
						<?php echo wp_kses_post( $full_description ); ?>
                    </p>
                    <p class="slide-description description-short">
						<?php echo wp_kses_post( $short_description ); ?>
                    </p>
                    <p class="slide-controls">
                        <a href="<?php echo esc_url( $url ); ?>" class="button button-primary"
                           target="_blank"><?php _e( 'Get it now', 'learnpress' ); ?></a>
                        <a href="<?php echo esc_url( $url_demo ); ?>" class="button"
                           target="_blank"><?php _e( 'View Demo', 'learnpress' ); ?></a>
                    </p>
                </div>

            </div>
			<?php
		}
		?>
    </div>
	<?php
}


/**************************************************/
/**************************************************/
/**************************************************/

/**
 * Count number of orders between to dates
 *
 * @param string
 * @param int
 *
 * @return int
 */
function learn_press_get_order_by_time( $by, $time ) {
	global $wpdb;
	$user_id = get_current_user_id();

	$y = date( 'Y', $time );
	$m = date( 'm', $time );
	$d = date( 'd', $time );
	switch ( $by ) {
		case 'days':
			$orders = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*)
					FROM $wpdb->posts AS p
					INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
					WHERE p.post_author = %d
					AND p.post_type = %s
					AND p.post_status = %s
					AND m.meta_key = %s
					AND m.meta_value = %s
					AND YEAR(p.post_date) = %s AND MONTH(p.post_date) = %s AND DAY(p.post_date) = %s",
					$user_id, LP_ORDER_CPT, 'publish', '_learn_press_transaction_status', 'completed', $y, $m, $d
				)
			);
			break;
		case 'months':
			$orders = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*)
					FROM $wpdb->posts AS p
					INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
					WHERE p.post_author = %d
					AND p.post_type = %s
					AND p.post_status = %s
					AND m.meta_key = %s
					AND m.meta_value = %s
					AND YEAR(p.post_date) = %s AND MONTH(p.post_date) = %s",
					$user_id, LP_ORDER_CPT, 'publish', '_learn_press_transaction_status', 'completed', $y, $m
				)
			);
			break;
	}

	return $orders;
}

/**
 * Count number of orders by status
 *
 * @param string Status of the orders
 *
 * @return int
 */
function learn_press_get_courses_by_status( $status ) {
	global $wpdb;
	$user_id = get_current_user_id();
	$courses = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM $wpdb->posts
			WHERE post_author = %d
			AND post_type = %s
			AND post_status = %s",
			$user_id, LP_COURSE_CPT, $status
		)
	);

	return $courses;
}

/**
 * Count number of orders by price
 *
 * @param string
 *
 * @return int
 */
function learn_press_get_courses_by_price( $fee ) {
	global $wpdb;
	$user_id = get_current_user_id();
	$courses = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM $wpdb->posts AS p
			INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
			WHERE p.post_author = %d
			AND p.post_type = %s
			AND p.post_status IN (%s, %s)
			AND m.meta_key = %s
			AND m.meta_value = %s",
			$user_id, LP_COURSE_CPT, 'publish', 'pending', '_lpr_course_payment', $fee
		)
	);

	return $courses;
}

/**
 * Get data about students to render in chart
 *
 * @param null $from
 * @param null $by
 * @param      $time_ago
 *
 * @return array
 */
function learn_press_get_chart_students( $from = null, $by = null, $time_ago ) {
	$labels   = array();
	$datasets = array();
	if ( is_null( $from ) ) {
		$from = current_time( 'mysql' );
	}
	// $by: days, months or years
	if ( is_null( $by ) ) {
		$by = 'days';
	}
	switch ( $by ) {
		case 'days':
			$date_format = 'M d';
			break;
		case 'months':
			$date_format = 'M Y';
			break;
		case 'years':
			$date_format = 'Y';
			break;
	}

	for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
		$labels[]              = date( $date_format, strtotime( "$i $by", strtotime( $from ) ) );
		$datasets[0]['data'][] = learn_press_get_order_by_time( $by, strtotime( "$i $by", strtotime( $from ) ) );
	}
	$colors                              = learn_press_get_admin_colors();
	$datasets[0]['fillColor']            = 'rgba(255,255,255,0.1)';
	$datasets[0]['strokeColor']          = $colors[0];
	$datasets[0]['pointColor']           = $colors[0];
	$datasets[0]['pointStrokeColor']     = $colors[2];
	$datasets[0]['pointHighlightFill']   = $colors[2];
	$datasets[0]['pointHighlightStroke'] = $colors[0];

	return array(
		'labels'   => $labels,
		'datasets' => $datasets
	);
}

/**
 * Get data about students to render in chart
 *
 * @param null $from
 * @param null $by
 * @param      $time_ago
 *
 * @return array
 */
function learn_press_get_chart_users( $from = null, $by = null, $time_ago ) {
	global $wpdb;

	$labels   = array();
	$datasets = array();
	if ( is_null( $from ) ) {
		$from = current_time( 'mysql' );
	}
	if ( is_null( $by ) ) {
		$by = 'days';
	}
	$results   = array(
		'all'         => array(),
		'instructors' => array()
	);
	$from_time = is_numeric( $from ) ? $from : strtotime( $from );

	switch ( $by ) {
		case 'days':
			$date_format = 'M d Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-d', strtotime( "{$_from} {$by}", $from_time ) );
			$_to         = date( 'Y-m-d', $from_time );
			$_sql_format = '%Y-%m-%d';
			$_key_format = 'Y-m-d';
			break;
		case 'months':
			$date_format = 'M Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-m-' . $days, $from_time );
			$_sql_format = '%Y-%m';
			$_key_format = 'Y-m';
			break;
		case 'years':
			$date_format = 'Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-01-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-12-' . $days, $from_time );
			$_sql_format = '%Y';
			$_key_format = 'Y';

			break;
	}
	$query = $wpdb->prepare( "
				SELECT count(u.ID) as c, DATE_FORMAT( u.user_registered, %s) as d
				FROM {$wpdb->users} u
				WHERE 1
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['all'][ $v->d ] = $v;
		}
	}
	$query = $wpdb->prepare( "
				SELECT count(u.ID) as c, DATE_FORMAT( u.user_registered, %s) as d
				FROM {$wpdb->users} u
				INNER JOIN {$wpdb->usermeta} um ON um.user_id = u.ID AND um.meta_key = %s AND ( um.meta_value LIKE %s OR um.meta_value LIKE %s )
				WHERE 1
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'wp_capabilities', '%' . $wpdb->esc_like( 's:13:"administrator"' ) . '%', '%' . $wpdb->esc_like( 's:10:"lp_teacher"' ) . '%', $_from, $_to );

	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['instructors'][ $v->d ] = $v;
		}
	}
	for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
		$date     = strtotime( "$i $by", $from_time );
		$labels[] = date( $date_format, $date );
		$key      = date( $_key_format, $date );

		$all         = ! empty( $results['all'][ $key ] ) ? $results['all'][ $key ]->c : 0;
		$instructors = ! empty( $results['instructors'][ $key ] ) ? $results['instructors'][ $key ]->c : 0;

		$datasets[0]['data'][] = $all;
		$datasets[1]['data'][] = $instructors;
		$datasets[2]['data'][] = $all - $instructors;
	}

	$dataset_params = array(
		array(
			'color1' => 'rgba(47, 167, 255, %s)',
			'color2' => '#FFF',
			'label'  => __( 'All', 'learnpress' )
		),
		array(
			'color1' => 'rgba(212, 208, 203, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Instructors', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Students', 'learnpress' )
		)
	);

	foreach ( $dataset_params as $k => $v ) {
		$datasets[ $k ]['fillColor']            = sprintf( $v['color1'], '0.2' );
		$datasets[ $k ]['strokeColor']          = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointColor']           = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointStrokeColor']     = $v['color2'];
		$datasets[ $k ]['pointHighlightFill']   = $v['color2'];
		$datasets[ $k ]['pointHighlightStroke'] = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['label']                = $v['label'];
	}

	return array(
		'labels'   => $labels,
		'datasets' => $datasets,
		'sql'      => $query
	);
}


/**
 * Get data about students to render in chart
 *
 * @param null $from
 * @param null $by
 * @param      $time_ago
 *
 * @return array
 */
function learn_press_get_chart_courses( $from = null, $by = null, $time_ago ) {
	global $wpdb;

	$labels   = array();
	$datasets = array();
	if ( is_null( $from ) ) {
		$from = current_time( 'mysql' );
	}
	if ( is_null( $by ) ) {
		$by = 'days';
	}
	$results   = array(
		'all'     => array(),
		'public'  => array(),
		'pending' => array(),
		'free'    => array(),
		'paid'    => array()
	);
	$from_time = is_numeric( $from ) ? $from : strtotime( $from );

	switch ( $by ) {
		case 'days':
			$date_format = 'M d Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-d', strtotime( "{$_from} {$by}", $from_time ) );
			$_to         = date( 'Y-m-d', $from_time );
			$_sql_format = '%Y-%m-%d';
			$_key_format = 'Y-m-d';
			break;
		case 'months':
			$date_format = 'M Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-m-' . $days, $from_time );
			$_sql_format = '%Y-%m';
			$_key_format = 'Y-m';
			break;
		case 'years':
			$date_format = 'Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-01-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-12-' . $days, $from_time );
			$_sql_format = '%Y';
			$_key_format = 'Y';

			break;
	}

	$query_where = '';
	if ( current_user_can( LP_TEACHER_ROLE ) ) {
		$user_id     = learn_press_get_current_user_id();
		$query_where .= $wpdb->prepare( " AND c.post_author=%d ", $user_id );
	}

	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				WHERE 1
				{$query_where}
				AND c.post_status IN('publish', 'pending') AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['all'][ $v->d ] = $v;
		}
	}
	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				WHERE 1
				{$query_where}
				AND c.post_status = %s AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'publish', 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['publish'][ $v->d ] = $v;
		}
	}

	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				INNER JOIN {$wpdb->postmeta} cm ON cm.post_id = c.ID AND cm.meta_key = %s AND cm.meta_value = %s
				WHERE 1
				{$query_where}
				AND c.post_status = %s AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, '_lp_payment', 'yes', 'publish', 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['paid'][ $v->d ] = $v;
		}
	}

	for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
		$date     = strtotime( "$i $by", $from_time );
		$labels[] = date( $date_format, $date );
		$key      = date( $_key_format, $date );

		$all     = ! empty( $results['all'][ $key ] ) ? $results['all'][ $key ]->c : 0;
		$publish = ! empty( $results['publish'][ $key ] ) ? $results['publish'][ $key ]->c : 0;
		$paid    = ! empty( $results['paid'][ $key ] ) ? $results['paid'][ $key ]->c : 0;

		$datasets[0]['data'][] = $all;
		$datasets[1]['data'][] = $publish;
		$datasets[2]['data'][] = $all - $publish;
		$datasets[3]['data'][] = $paid;
		$datasets[4]['data'][] = $all - $paid;
	}

	$dataset_params = array(
		array(
			'color1' => 'rgba(47, 167, 255, %s)',
			'color2' => '#FFF',
			'label'  => __( 'All', 'learnpress' )
		),
		array(
			'color1' => 'rgba(212, 208, 203, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Publish', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Pending', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Paid', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Free', 'learnpress' )
		)
	);

	foreach ( $dataset_params as $k => $v ) {
		$datasets[ $k ]['fillColor']            = sprintf( $v['color1'], '0.2' );
		$datasets[ $k ]['strokeColor']          = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointColor']           = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointStrokeColor']     = $v['color2'];
		$datasets[ $k ]['pointHighlightFill']   = $v['color2'];
		$datasets[ $k ]['pointHighlightStroke'] = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['label']                = $v['label'];
	}

	return array(
		'labels'   => $labels,
		'datasets' => $datasets,
		'sql'      => $query
	);
}


/**
 * Get data about students to render in chart
 *
 * @param null $from
 * @param null $by
 * @param      $time_ago
 *
 * @return array
 */
function learn_press_get_chart_orders( $from = null, $by = null, $time_ago ) {
	global $wpdb;
//	var_dump( current_user_can(LP_TEACHER_ROLE) );
//	exit();
	$report_sales_by = learn_press_get_request( 'report_sales_by' );
	$course_id       = learn_press_get_request( 'course_id' );
	$cat_id          = learn_press_get_request( 'cat_id' );

	$labels   = array();
	$datasets = array();
	if ( is_null( $from ) ) {
		$from = current_time( 'mysql' );
	}
	if ( is_null( $by ) ) {
		$by = 'days';
	}
	$results   = array(
		'all'       => array(),
		'completed' => array(),
		'pending'   => array()
	);
	$from_time = is_numeric( $from ) ? $from : strtotime( $from );

	switch ( $by ) {
		case 'days':
			$date_format = 'M d Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-d', strtotime( "{$_from} {$by}", $from_time ) );
			$_to         = date( 'Y-m-d', $from_time );
			$_sql_format = '%Y-%m-%d';
			$_key_format = 'Y-m-d';
			break;
		case 'months':
			$date_format = 'M Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-m-' . $days, $from_time );
			$_sql_format = '%Y-%m';
			$_key_format = 'Y-m';
			break;
		case 'years':
			$date_format = 'Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-01-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-12-' . $days, $from_time );
			$_sql_format = '%Y';
			$_key_format = 'Y';

			break;
	}

	$query_join  = '';
	$query_where = '';
	if ( 'course' === $report_sales_by ) {
		$sql_join .= " INNER JOIN `{$wpdb->prefix}learnpress_order_items` `lpoi` "
		             . " ON o.ID=lpoi.order_id "
		             . " INNER JOIN {$wpdb->prefix}learnpress_order_itemmeta loim "
		             . " ON lpoi.order_item_id=loim.learnpress_order_item_id "
		             . " AND loim.meta_key='_course_id' "
		             . " AND CAST(loim.meta_value AS SIGNED)=%d ";
		if ( current_user_can( LP_TEACHER_ROLE ) ) {
			$user_id  = learn_press_get_current_user_id();
			$sql_join .= $wpdb->prepare( " AND CAST(loim.meta_value AS SIGNED) IN "
			                             . " ( "
			                             . " SELECT ID FROM {$wpdb->posts} p WHERE p.ID = CAST(loim.meta_value AS SIGNED) AND `post_author`=" . intval( $user_id )
			                             . " ) " );
		}
		$query_join .= $wpdb->prepare( $sql_join, $course_id );

	} elseif ( 'category' === $report_sales_by ) {
		$sql_join   .= " INNER JOIN `{$wpdb->prefix}learnpress_order_items` `lpoi` "
		               . " ON o.ID=lpoi.order_id "
		               . " INNER JOIN {$wpdb->prefix}learnpress_order_itemmeta loim "
		               . " ON lpoi.order_item_id=loim.learnpress_order_item_id "
		               . " AND loim.meta_key='_course_id' "
		               . " AND CAST(loim.meta_value AS SIGNED) IN("
		               //sub query
		               . " SELECT tr.object_id "
		               . " FROM {$wpdb->prefix}term_taxonomy tt INNER JOIN {$wpdb->prefix}term_relationships tr "
		               . " ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy='course_category' "
		               . " WHERE tt.term_id=%d)";
		$query_join .= $wpdb->prepare( $sql_join, $cat_id );
	}
	if ( current_user_can( LP_TEACHER_ROLE ) ) {
		$user_id     = learn_press_get_current_user_id();
		$query_where .= $wpdb->prepare( " AND o.ID IN( SELECT oi.order_id
										FROM lptest.{$wpdb->prefix}learnpress_order_items oi
											inner join {$wpdb->prefix}learnpress_order_itemmeta oim
												on oi.order_item_id = oim.learnpress_order_item_id
													and oim.meta_key='_course_id'
													and cast(oim.meta_value as SIGNED) IN (
														SELECT sp.ID FROM {$wpdb->prefix}posts sp WHERE sp.post_author=%d and sp.ID=cast(oim.meta_value as SIGNED)
													)
										) ", $user_id );
	}

	$query = $wpdb->prepare( "
				SELECT count(o.ID) as c, DATE_FORMAT( o.post_date, %s) as d
				FROM {$wpdb->posts} o {$query_join}
				WHERE 1 {$query_where}
				AND o.post_status IN('lp-completed') AND o.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'lp_order', $_from, $_to );
//	echo $query;
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
//			$results['all'][$v->d] = $v;
			$results['completed'][ $v->d ] = $v;
		}
	}

	$query = $wpdb->prepare( "
				SELECT count(o.ID) as c, DATE_FORMAT( o.post_date, %s) as d
				FROM {$wpdb->posts} o {$query_join}
				WHERE 1 {$query_where}
				AND o.post_status IN('lp-pending', 'lp-processing') AND o.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'lp_order', $_from, $_to );
//	echo $query;
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
//			$results['completed'][$v->d] = $v;
			$results['pending'][ $v->d ] = $v;
		}
	}


	for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
		$date     = strtotime( "$i $by", $from_time );
		$labels[] = date( $date_format, $date );
		$key      = date( $_key_format, $date );

		$completed = ! empty( $results['completed'][ $key ] ) ? $results['completed'][ $key ]->c : 0;
		$pending   = ! empty( $results['pending'][ $key ] ) ? $results['pending'][ $key ]->c : 0;
		$all       = $completed + $pending;

		$datasets[0]['data'][] = $all;
		$datasets[1]['data'][] = $completed;
		$datasets[2]['data'][] = $pending;
	}

	$dataset_params = array(
		array(
			'color1' => 'rgba(47, 167, 255, %s)',
			'color2' => '#FFF',
			'label'  => __( 'All', 'learnpress' )
		),
		array(
			'color1' => 'rgba(212, 208, 203, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Completed', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Pending', 'learnpress' )
		)
	);

	foreach ( $dataset_params as $k => $v ) {
		$datasets[ $k ]['fillColor']            = sprintf( $v['color1'], '0.2' );
		$datasets[ $k ]['strokeColor']          = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointColor']           = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointStrokeColor']     = $v['color2'];
		$datasets[ $k ]['pointHighlightFill']   = $v['color2'];
		$datasets[ $k ]['pointHighlightStroke'] = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['label']                = $v['label'];
	}

	return array(
		'labels'   => $labels,
		'datasets' => $datasets,
		'sql'      => $query
	);
}

/**
 * Get data about courses to render in the chart
 * @return array
 */
function learn_press_get_chart_courses2() {
	$labels              = array(
		__( 'Pending Courses / Publish Courses', 'learnpress' ),
		__( 'Free Courses / Priced Courses', 'learnpress' )
	);
	$datasets            = array();
	$datasets[0]['data'] = array(
		learn_press_get_courses_by_status( 'pending' ),
		learn_press_get_courses_by_price( 'free' )
	);
	$datasets[1]['data'] = array(
		learn_press_get_courses_by_status( 'publish' ),
		learn_press_get_courses_by_price( 'not_free' )
	);

	$colors                     = learn_press_get_admin_colors();
	$datasets[0]['fillColor']   = $colors[1];
	$datasets[0]['strokeColor'] = $colors[1];
	$datasets[1]['fillColor']   = $colors[3];
	$datasets[1]['strokeColor'] = $colors[3];

	return array(
		'labels'   => $labels,
		'datasets' => $datasets
	);
}

/**
 * Get colors setting up by admin user
 * @return array
 */
function learn_press_get_admin_colors() {
	$admin_color = get_user_meta( get_current_user_id(), 'admin_color', true );
	global $_wp_admin_css_colors;
	$colors = array();
	if ( ! empty( $_wp_admin_css_colors[ $admin_color ]->colors ) ) {
		$colors = $_wp_admin_css_colors[ $admin_color ]->colors;
	}
	if ( empty ( $colors[0] ) ) {
		$colors[0] = '#000000';
	}
	if ( empty ( $colors[2] ) ) {
		$colors[2] = '#00FF00';
	}

	return $colors;
}

/**
 * Convert an array to json format and print out to browser
 *
 * @param array $chart
 */
function learn_press_process_chart( $chart = array() ) {
	$data = json_encode(
		array(
			'labels'   => $chart['labels'],
			'datasets' => $chart['datasets']
		)
	);
	echo $data;
}

/**
 * Print out the configuration for admin chart
 */
function learn_press_config_chart() {
	$colors = learn_press_get_admin_colors();
	$config = array(
		'scaleShowGridLines'      => true,
		'scaleGridLineColor'      => "#777",
		'scaleGridLineWidth'      => 0.3,
		'scaleFontColor'          => "#444",
		'scaleLineColor'          => $colors[0],
		'bezierCurve'             => true,
		'bezierCurveTension'      => 0.2,
		'pointDotRadius'          => 5,
		'pointDotStrokeWidth'     => 5,
		'pointHitDetectionRadius' => 20,
		'datasetStroke'           => true,
		'responsive'              => true,
		'tooltipFillColor'        => $colors[2],
		'tooltipFontColor'        => "#eee",
		'tooltipCornerRadius'     => 0,
		'tooltipYPadding'         => 10,
		'tooltipXPadding'         => 10,
		'barDatasetSpacing'       => 10,
		'barValueSpacing'         => 200

	);
	echo json_encode( $config );
}

function set_post_order_in_admin( $wp_query ) {
	global $pagenow;
	if ( isset( $_GET['post_type'] ) ) {
		$post_type = $_GET['post_type'];
	} else {
		$post_type = '';
	}
	if ( is_admin() && 'edit.php' == $pagenow && $post_type == LP_COURSE_CPT && ! isset( $_GET['orderby'] ) ) {
		$wp_query->set( 'orderby', 'date' );
		$wp_query->set( 'order', 'DSC' );
	}
}

add_filter( 'pre_get_posts', 'set_post_order_in_admin' );

function learn_press_copy_post_meta( $from_id, $to_id ) {
	global $wpdb;
	$course_meta = $wpdb->get_results(
		$wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $from_id )
	);
	if ( count( $course_meta ) != 0 ) {
		$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		$sql_query_sel = array();

		foreach ( $course_meta as $meta ) {
			$meta_key   = $meta->meta_key;
			$meta_value = addslashes( $meta->meta_value );

			$sql_query_sel[] = "SELECT {$to_id}, '$meta_key', '$meta_value'";
		}

		$sql_query .= implode( " UNION ALL ", $sql_query_sel );
		$wpdb->query( $sql_query );
	}
}

/**
 * Install a plugin
 *
 * @param string $plugin_name
 *
 * @return array
 */
function learn_press_install_add_on( $plugin_name ) {
	require_once( LP_PLUGIN_PATH . '/inc/admin/class-lp-upgrader.php' );
	$upgrader = new LP_Upgrader();
	global $wp_filesystem;
	$response = array();

	$package = 'http://thimpress.com/lprepo/' . $plugin_name . '.zip';

	$package = $upgrader->download_package( $package );
	if ( is_wp_error( $package ) ) {
		$response['error'] = $package;
	} else {
		$working_dir = $upgrader->unpack_package( $package, true, $plugin_name );
		if ( is_wp_error( $working_dir ) ) {
			$response['error'] = $working_dir;
		} else {

			$wp_upgrader = new WP_Upgrader();
			$options     = array(
				'source'            => $working_dir,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => false, // Do not overwrite files.
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'plugin',
					'action' => 'install'
				)
			);
			//$response = array();
			$result = $wp_upgrader->install_package( $options );

			if ( is_wp_error( $result ) ) {
				$response['error'] = $result;
			} else {
				$response         = $result;
				$response['text'] = __( 'Installed', 'learnpress' );
			}
		}
	}

	return $response;
}

function learn_press_accept_become_a_teacher() {
	$action  = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
	$user_id = ! empty( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : '';
	if ( ! $action || ! $user_id || ( $action != 'accept-to-be-teacher' ) ) {
		return;
	}

	if ( ! learn_press_user_maybe_is_a_teacher( $user_id ) ) {
		$be_teacher = new WP_User( $user_id );
		$be_teacher->set_role( LP_TEACHER_ROLE );
		delete_transient( 'learn_press_become_teacher_sent_' . $user_id );
		do_action( 'learn_press_user_become_a_teacher', $user_id );
		$redirect = add_query_arg( 'become-a-teacher-accepted', 'yes' );
		$redirect = remove_query_arg( 'action', $redirect );
		wp_redirect( $redirect );
	}
}

add_action( 'plugins_loaded', 'learn_press_accept_become_a_teacher' );

function learn_press_user_become_a_teacher_notice() {
	if ( $user_id = learn_press_get_request( 'user_id' ) && learn_press_get_request( 'become-a-teacher-accepted' ) == 'yes' ) {
		$user = new WP_User( $user_id );
		?>
        <div class="updated">
            <p><?php printf( __( 'The user %s has become a teacher', 'learnpress' ), $user->user_login ); ?></p>
        </div>
		<?php
	}
}

add_action( 'admin_notices', 'learn_press_user_become_a_teacher_notice' );

/**
 * Check to see if a plugin is already installed or not
 *
 * @param $plugin
 *
 * @return bool
 */
function learn_press_is_plugin_install( $plugin ) {
	$installed_plugins = get_plugins();

	return isset( $installed_plugins[ $plugin ] );
}

/**
 * Get plugin file that contains the information from slug
 *
 * @param $slug
 *
 * @return mixed
 */
function learn_press_plugin_basename_from_slug( $slug ) {
	$keys = array_keys( get_plugins() );
	foreach ( $keys as $key ) {
		if ( preg_match( '|^' . $slug . '/|', $key ) ) {
			return $key;
		}
	}

	return $slug;
}

function learn_press_one_click_install_sample_data_notice() {
	$courses = get_posts(
		array(
			'post_type'   => LP_COURSE_CPT,
			'post_status' => 'any'
		)
	);
	if ( ( 0 == sizeof( $courses ) ) && ( 'off' != get_transient( 'learn_press_install_sample_data' ) ) ) {
		printf(
			'<div class="updated" id="learn-press-install-sample-data-notice">
				<div class="install-sample-data-notice">
                <p>%s</p>
                <p>%s <strong>%s</strong> %s
                <p><a href="" class="button yes" data-action="yes">%s</a> <a href="" class="button disabled no" data-action="no">%s</a></p>
                </div>
                <div class="install-sample-data-loading">
                	<p>Importing...</p>
				</div>
            </div>',
			__( 'You haven\'t got any courses yet! Would you like to import sample data?', 'learnpress' ),
			__( 'If yes, please install add-on name', 'learnpress' ),
			__( 'LearnPress Import/Export', 'learnpress' ),
			__( 'but don\'t worry because it is completely automated.', 'learnpress' ),
			__( 'Import now', 'learnpress' ),
			__( 'No, thanks!', 'learnpress' )
		);
	}
}

//add_action( 'admin_notices', 'learn_press_one_click_install_sample_data_notice' );

function learn_press_request_query( $vars = array() ) {
	global $typenow, $wp_query, $wp_post_statuses;
	if ( LP_ORDER_CPT === $typenow ) {
		// Status
		if ( ! isset( $vars['post_status'] ) ) {
			$post_statuses = learn_press_get_order_statuses();

			foreach ( $post_statuses as $status => $value ) {
				if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
					unset( $post_statuses[ $status ] );
				}
			}

			$vars['post_status'] = array_keys( $post_statuses );

		}
	}

	return $vars;
}

add_filter( 'request', 'learn_press_request_query', 0 );

function _learn_press_reset_course_data() {
	if ( empty( $_REQUEST['reset-course-data'] ) ) {
		return false;
	}
	learn_press_reset_course_data( intval( $_REQUEST['reset-course-data'] ) );
	wp_redirect( remove_query_arg( 'reset-course-data' ) );
}

add_action( 'init', '_learn_press_reset_course_data' );

/***********************/
function learn_press_admin_section_loop_item_class( $item, $section ) {
	$classes   = array(
		'lp-section-item'
	);
	$classes[] = 'lp-item-' . $item->post_type;
	if ( ! absint( $item->ID ) ) {
		$classes[] = 'lp-item-empty lp-item-new focus';
	}
	$classes = apply_filters( 'learn_press_section_loop_item_class', $classes, $item, $section );
	if ( $classes ) {
		echo 'class="' . join( ' ', $classes ) . '"';
	}

	return $classes;
}

function learn_press_disable_checked_ontop( $args ) {

	if ( 'course_category' == $args['taxonomy'] ) {
		$args['checked_ontop'] = false;
	}

	return $args;
}

add_filter( 'wp_terms_checklist_args', 'learn_press_disable_checked_ontop' );

function learn_press_output_screen_id() {
	$screen = get_current_screen();
	if ( $screen ) {
		echo "<div style=\"position:fixed;top: 0; left:0; z-index: 99999999; background-color:#FFF;padding:4px;\">" . $screen->id . "</div>";
	}
}

//add_action( 'admin_head', 'learn_press_output_screen_id' );

function learn_press_get_screens() {
	$screen_id = sanitize_title( __( 'LearnPress', 'learnpress' ) );
	$screens   = array(
		'toplevel_page_' . $screen_id,
		$screen_id . '_page_learn-press-statistics',
		$screen_id . '_page_learn-press-addons',
		$screen_id . '_page_learn-press-settings',
		$screen_id . '_page_learn-press-tools'
	);
	foreach ( array( LP_COURSE_CPT, LP_LESSON_CPT, LP_QUIZ_CPT, LP_QUESTION_CPT, LP_ORDER_CPT ) as $post_type ) {
		$screens[] = 'edit-' . $post_type;
		$screens[] = $post_type;
	}

	return apply_filters( 'learn_press_screen_ids', $screens );
}

function learn_press_is_post_type_screen( $post_type, $union = 'OR' ) {
	if ( is_array( $post_type ) ) {
		$return = null;
		foreach ( $post_type as $_post_type ) {
			$check = learn_press_is_post_type_screen( $_post_type );
			if ( $union == 'OR' && $check ) {
				return true;
			}
			if ( $return == null ) {
				$return = $check;
			} else {
				$return = $return && $check;
			}
			if ( $union !== 'OR' ) {
				return $return;
			}
		}

		return $return;
	}
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}
	$screen_id = $screen->id;

	return in_array( $screen_id, array( $post_type, "edit-{$post_type}" ) );
}

function learn_press_get_notice_dismiss( $context, $type = 'transient' ) {
	if ( $type == 'transient' ) {
		return get_transient( 'learn_press_dismiss_notice_' . $context );
	}

	return get_option( 'learn_press_dismiss_notice_' . $context );
}

if ( ! function_exists( 'learn_press_course_insert_section' ) ) {

	function learn_press_course_insert_section( $section = array() ) {
		global $wpdb;
		$section = wp_parse_args(
			$section,
			array(
				'section_name'        => '',
				'section_course_id'   => 0,
				'section_order'       => 0,
				'section_description' => ''
			)
		);
		$section = stripslashes_deep( $section );
		extract( $section );

		$insert_data = compact( 'section_name', 'section_course_id', 'section_order', 'section_description' );
		$wpdb->insert(
			$wpdb->learnpress_sections,
			$insert_data,
			array( '%s', '%d', '%d' )
		);

		return $wpdb->insert_id;
	}

}

if ( ! function_exists( 'learn_press_course_insert_section_item' ) ) {

	function learn_press_course_insert_section_item( $item_data = array() ) {
		global $wpdb;
		$wpdb->insert(
			$wpdb->learnpress_section_items,
			array(
				'section_id' => $item_data['section_id'],
				'item_id'    => $item_data['item_id'],
				'item_order' => $item_data['item_order'],
				'item_type'  => $item_data['item_type']
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}

}

if ( ! function_exists( 'learn_press_duplicate_post' ) ) {
	/**
	 * Duplicate post.
	 *
	 * @since 3.0.0
	 *
	 * @param null  $post_id
	 * @param array $args
	 * @param bool  $meta
	 *
	 * @return bool|mixed
	 */
	function learn_press_duplicate_post( $post_id = null, $args = array(), $meta = true ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		$default = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => get_current_user_id(),
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . __( ' Copy', 'learnpress' ),
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
			'exclude_meta'   => array()
		);

		$args         = wp_parse_args( $args, $default );
		$exclude_meta = array();

		if ( ! empty( $args['exclude_meta'] ) ) {
			$exclude_meta = $args['exclude_meta'];
			unset( $args['exclude_meta'] );
		}

		$new_post_id = wp_insert_post( $args );


		if ( ! is_wp_error( $new_post_id ) && $meta ) {

			learn_press_duplicate_post_meta( $post_id, $new_post_id, $exclude_meta );
			// assign related tags/categories to new course
			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}
		}

		return apply_filters( 'learn_press_duplicate_post', $new_post_id, $post_id );
	}
}

if ( ! function_exists( 'learn_press_duplicate_post_meta' ) ) {
	/**
	 * duplicate all post meta just in two SQL queries
	 */
	function learn_press_duplicate_post_meta( $old_post_id, $new_post_id, $excerpt = array() ) {
		global $wpdb;
		$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$old_post_id" );
		if ( count( $post_meta_infos ) != 0 ) {
			$excerpt       = array_merge( array( '_edit_lock', '_edit_last' ), $excerpt );
			$excerpt       = apply_filters( 'learn_press_excerpt_duplicate_post_meta', $excerpt, $old_post_id, $new_post_id );
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			$sql_query_sel = array();
			foreach ( $post_meta_infos as $meta ) {
				if ( in_array( $meta->meta_key, $excerpt ) ) {
					continue;
				}
				if ( $meta->meta_key === '_lp_course_author' ) {
					$meta->meta_value = get_current_user_id();
				}
				$meta_key        = $meta->meta_key;
				$meta_value      = addslashes( $meta->meta_value );
				$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query .= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );
		}
	}

}

//add_filter( 'learn_press_question_types', 'learn_press_sort_questions', 99 );
if ( ! function_exists( 'learn_press_sort_questions' ) ) {
	function learn_press_sort_questions( $types ) {
		$user_id        = get_current_user_id();
		$question_types = get_user_meta( $user_id, '_learn_press_memorize_question_types', true );
		if ( ! empty( $question_types ) ) {
			$sort = array();
			// re-sort array descending
			arsort( $types );
			$new_types = array();
			$ktypes    = array_keys( $types );

			for ( $i = 0; $i < count( $ktypes ) - 1; $i ++ ) {
				$max = $i;
				if ( ! isset( $question_types[ $ktypes[ $i ] ] ) ) {
					$question_types[ $ktypes[ $i ] ] = 0;
				}
				for ( $j = $i + 1; $j < count( $ktypes ); $j ++ ) {
					if ( isset( $question_types[ $ktypes[ $j ] ], $question_types[ $ktypes[ $max ] ] )
					     && $question_types[ $ktypes[ $j ] ] > $question_types[ $ktypes[ $max ] ]
					) {
						$max = $j;
					}
				}
				$tmp            = $ktypes[ $i ];
				$ktypes[ $i ]   = $ktypes[ $max ];
				$ktypes[ $max ] = $tmp;
			}
			$ktypes = array_flip( $ktypes );
			$types  = array_merge( $ktypes, $types );
		}

		return $types;
	}
}

if ( ! function_exists( 'learn_press_duplicate_question' ) ) {

	function learn_press_duplicate_question( $question_id = null, $quiz_id = null, $args = array() ) {
		if ( ! $question_id || ! get_post( $question_id ) ) {
			return new WP_Error( sprintf( __( 'Question id %s does not exist.', 'learnpress' ), $question_id ) );
		}
		if ( $quiz_id && ! get_post( $quiz_id ) ) {
			return new WP_Error( sprintf( __( 'Quiz id %s does not exist.', 'learnpress' ), $quiz_id ) );
		}

		global $wpdb;
		$new_question_id = learn_press_duplicate_post( $question_id, $args );
		if ( $quiz_id ) {
			// learnpress_quiz_questions
			$sql                = $wpdb->prepare( "
                        SELECT * FROM $wpdb->learnpress_quiz_questions WHERE quiz_id = %d AND question_id = %d
                    ", $quiz_id, $question_id );
			$quiz_question_data = $wpdb->get_row( $sql );
			$max_order          = $wpdb->get_var( $wpdb->prepare( "SELECT max(question_order) FROM {$wpdb->prefix}learnpress_quiz_questions WHERE quiz_id = %d", $quiz_id ) );

			if ( $quiz_question_data ) {
				$wpdb->insert(
					$wpdb->learnpress_quiz_questions,
					array(
						'quiz_id'        => $quiz_id,
						'question_id'    => $new_question_id,
						'question_order' => $max_order + 1,
						'params'         => $quiz_question_data->params
					),
					array(
						'%d',
						'%d',
						'%d',
						'%s'
					)
				);
			}
		}
		// learnpress_question_answers
		$sql              = $wpdb->prepare( "
                    SELECT * FROM $wpdb->learnpress_question_answers WHERE question_id = %d
                ", $question_id );
		$question_answers = $wpdb->get_results( $sql );
		if ( $question_answers ) {
			foreach ( $question_answers as $q_a ) {
				$wpdb->insert(
					$wpdb->learnpress_question_answers,
					array(
						'question_id' => $new_question_id,
						/** @since 4.0 * */
						//'answer_data'  => $q_a->answer_data,
						'title'       => $q_a->title,
						'value'       => $q_a->value,
						'is_true'     => $q_a->is_true,
						'order'       => $q_a->order
					),
					array(
						'%d',
						'%s',
						'%s'
					)
				);
			}
		}

		return $new_question_id;
	}

}

if ( ! function_exists( 'learn_press_duplicate_quiz' ) ) {

	function learn_press_duplicate_quiz( $quiz_id = null, $args = array() ) {
		global $wpdb;
		$new_quiz_id = learn_press_duplicate_post( $quiz_id, $args, true );
		$quiz        = LP_Quiz::get_quiz( $quiz_id );
		$questions   = $quiz->get_questions();
		if ( $questions ) {
			$questions = array_keys( $questions );
			foreach ( $questions as $question_id ) {
				$new_question_id = learn_press_duplicate_post( $question_id );
				// learnpress_quiz_questions
				$sql                = $wpdb->prepare( "
                            SELECT * FROM $wpdb->learnpress_quiz_questions WHERE quiz_id = %d AND question_id = %d
                        ", $quiz_id, $question_id );
				$quiz_question_data = $wpdb->get_row( $sql );
				if ( $quiz_question_data ) {
					$wpdb->insert(
						$wpdb->learnpress_quiz_questions,
						array(
							'quiz_id'        => $new_quiz_id,
							'question_id'    => $new_question_id,
							'question_order' => $quiz_question_data->question_order,
							'params'         => $quiz_question_data->params
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s'
						)
					);
				}
				// learnpress_question_answers
				$sql              = $wpdb->prepare( "
                            SELECT * FROM $wpdb->learnpress_question_answers WHERE question_id = %d
                        ", $question_id );
				$question_answers = $wpdb->get_results( $sql );
				if ( $question_answers ) {
					foreach ( $question_answers as $q_a ) {
						$wpdb->insert(
							$wpdb->learnpress_question_answers,
							array(
								'question_id' => $new_question_id,
								/** @since 4.0 * */
								//'answer_data'  => $q_a->answer_data,
								'title'       => $q_a->title,
								'value'       => $q_a->value,
								'is_true'     => $q_a->is_true,
								'order'       => $q_a->order
							),
							array(
								'%d',
								'%s',
								'%s'
							)
						);
					}
				}
			}
		}

		return $new_quiz_id;
	}

}

/**
 * Get general data to render in chart
 *
 * @param null $from
 * @param null $by
 * @param      $time_ago
 *
 * @return array
 */
function learn_press_get_chart_general( $from = null, $by = null, $time_ago ) {
	global $wpdb;

	$labels   = array();
	$datasets = array();

	if ( is_null( $from ) ) {
		$from = current_time( 'mysql' );
	}

	if ( is_null( $by ) ) {
		$by = 'days';
	}

	$results = array(
		'all'     => array(),
		'public'  => array(),
		'pending' => array(),
		'free'    => array(),
		'paid'    => array()
	);

	$results = array(
		'course'  => array(),
		'lesson'  => array(),
		'quiz'    => array(),
		'student' => array(),
		'teacher' => array(),
		'revenue' => array()
	);

	$from_time   = is_numeric( $from ) ? $from : strtotime( $from );
	$_from       = '';
	$_to         = '';
	$_sql_format = '';
	$date_format = '';
	switch ( $by ) {
		case 'days':
			$date_format = 'M d Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-d', strtotime( "{$_from} {$by}", $from_time ) );
			$_to         = date( 'Y-m-d', $from_time );
			$_sql_format = '%Y-%m-%d';
			$_key_format = 'Y-m-d';
			break;

		case 'months':
			$date_format = 'M Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-m-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-m-' . $days, $from_time );
			$_sql_format = '%Y-%m';
			$_key_format = 'Y-m';
			break;

		case 'years':
			$date_format = 'Y';
			$_from       = - $time_ago + 1;
			$_from       = date( 'Y-01-01', strtotime( "{$_from} {$by}", $from_time ) );
			$days        = date( 't', mktime( 0, 0, 0, date( 'm', $from_time ), 1, date( 'Y', $from_time ) ) );
			$_to         = date( 'Y-12-' . $days, $from_time );
			$_sql_format = '%Y';
			$_key_format = 'Y';
			break;

	}

	$query_where = '';
	if ( current_user_can( LP_TEACHER_ROLE ) ) {
		$user_id     = learn_press_get_current_user_id();
		$query_where .= $wpdb->prepare( " AND c.post_author=%d ", $user_id );
	}

	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				WHERE 1
				{$query_where}
				AND c.post_status IN('publish', 'pending') AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['all'][ $v->d ] = $v;
		}
	}
	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				WHERE 1
				{$query_where}
				AND c.post_status = %s AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, 'publish', 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['publish'][ $v->d ] = $v;
		}
	}

	$query = $wpdb->prepare( "
				SELECT count(c.ID) as c, DATE_FORMAT( c.post_date, %s) as d
				FROM {$wpdb->posts} c
				INNER JOIN {$wpdb->postmeta} cm ON cm.post_id = c.ID AND cm.meta_key = %s AND cm.meta_value = %s
				WHERE 1
				{$query_where}
				AND c.post_status = %s AND c.post_type = %s
				GROUP BY d
				HAVING d BETWEEN %s AND %s
				ORDER BY d ASC
			", $_sql_format, '_lp_payment', 'yes', 'publish', 'lp_course', $_from, $_to );
	if ( $_results = $wpdb->get_results( $query ) ) {
		foreach ( $_results as $k => $v ) {
			$results['paid'][ $v->d ] = $v;
		}
	}

	for ( $i = - $time_ago + 1; $i <= 0; $i ++ ) {
		$date     = strtotime( "$i $by", $from_time );
		$labels[] = date( $date_format, $date );
		$key      = date( $_key_format, $date );

		$all     = ! empty( $results['all'][ $key ] ) ? $results['all'][ $key ]->c : 0;
		$publish = ! empty( $results['publish'][ $key ] ) ? $results['publish'][ $key ]->c : 0;
		$paid    = ! empty( $results['paid'][ $key ] ) ? $results['paid'][ $key ]->c : 0;

		$datasets[0]['data'][] = $all;
		$datasets[1]['data'][] = $publish;
		$datasets[2]['data'][] = $all - $publish;
		$datasets[3]['data'][] = $paid;
		$datasets[4]['data'][] = $all - $paid;
	}

	$dataset_params = array(
		array(
			'color1' => 'rgba(47, 167, 255, %s)',
			'color2' => '#FFF',
			'label'  => __( 'All', 'learnpress' )
		),
		array(
			'color1' => 'rgba(212, 208, 203, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Publish', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Pending', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Paid', 'learnpress' )
		),
		array(
			'color1' => 'rgba(234, 199, 155, %s)',
			'color2' => '#FFF',
			'label'  => __( 'Free', 'learnpress' )
		)
	);

	foreach ( $dataset_params as $k => $v ) {
		$datasets[ $k ]['fillColor']            = sprintf( $v['color1'], '0.2' );
		$datasets[ $k ]['strokeColor']          = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointColor']           = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['pointStrokeColor']     = $v['color2'];
		$datasets[ $k ]['pointHighlightFill']   = $v['color2'];
		$datasets[ $k ]['pointHighlightStroke'] = sprintf( $v['color1'], '1' );
		$datasets[ $k ]['label']                = $v['label'];
	}

	return array(
		'labels'   => $labels,
		'datasets' => $datasets,
		'sql'      => $query
	);
}

function learn_press_get_default_section( $section = null ) {
	if ( ! $section ) {
		$section = new stdClass();
	}
	foreach (
		array(
			'section_id'          => null,
			'section_name'        => '',
			'section_course_id'   => null,
			'section_order'       => null,
			'section_description' => ''
		) as $k => $v
	) {
		if ( ! property_exists( $section, $k ) ) {
			$section->{$k} = $v;
		}
	}

	return $section;
}

/**
 * Display time fields for a post in editing mode.
 *
 * @param int $edit
 * @param int $for_post
 * @param int $tab_index
 * @param int $multi
 */
function learn_press_touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale;
	$post = get_post();

	if ( $for_post ) {
		$edit = ! ( in_array( $post->post_status, array(
				'draft',
				'pending'
			) ) && ( ! $post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );
	}

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 ) {
		$tab_index_attribute = " tabindex=\"$tab_index\"";
	}

	$time_adj  = current_time( 'timestamp' );
	$post_date = ( $for_post ) ? $post->post_date : get_comment()->comment_date;
	$jj        = ( $edit ) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
	$mm        = ( $edit ) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
	$aa        = ( $edit ) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
	$hh        = ( $edit ) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
	$mn        = ( $edit ) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
	$ss        = ( $edit ) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

	$cur_jj = gmdate( 'd', $time_adj );
	$cur_mm = gmdate( 'm', $time_adj );
	$cur_aa = gmdate( 'Y', $time_adj );
	$cur_hh = gmdate( 'H', $time_adj );
	$cur_mn = gmdate( 'i', $time_adj );

	$map = array(
		'mm' => array( $mm, $cur_mm ),
		'jj' => array( $jj, $cur_jj ),
		'aa' => array( $aa, $cur_aa ),
		'hh' => array( $hh, $cur_hh ),
		'mn' => array( $mn, $cur_mn ),
	);
	foreach ( $map as $timeunit => $value ) {
		list( $unit, $curr ) = $value;

		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $unit . '" />' . "\n";
		$cur_timeunit = 'cur_' . $timeunit;
		echo '<input type="hidden" id="' . $cur_timeunit . '" name="' . $cur_timeunit . '" value="' . $curr . '" />' . "\n";
	}
}

/**
 * Filter to modal search items to void filter the posts by author.
 *
 * @since 3.0.4
 *
 * @param int|string $context_id
 * @param string     $context
 *
 * @return bool|int|string
 */
function learn_press_modal_search_items_context( $context_id, $context ) {
	if ( 'order-items' === $context ) {
		$context_id = false;
	}

	return $context_id;
}

add_filter( 'learn-press/modal-search-items/context-id', 'learn_press_modal_search_items_context', 10, 2 );

/**
 * Filter to post link to change the link if it is an item inside course.
 *
 * @since 3.0.0
 *
 * @param string  $link
 * @param WP_Post $post
 *
 * @return string
 */
function learn_press_preview_post_link( $link, $post ) {
	$items = learn_press_course_get_support_item_types( true );

	if ( in_array( $post->post_type, $items ) ) {
		$link = learn_press_course_item_type_link( $link, $post, false, false );
	}

	return $link;
}

add_filter( 'preview_post_link', 'learn_press_preview_post_link', 10, 2 );

/**
 * Sync post meta when saving post type.
 *
 * @since 3.2.0
 *
 * @param int $post_id
 */
function learn_press_maybe_sync_data( $post_id ) {
	$post_type = get_post_type( $post_id );

	switch ( $post_type ) {
		case LP_COURSE_CPT:
			LP_Repair_Database::instance()->sync_user_courses();
			break;
		case LP_LESSON_CPT:
			break;
		case LP_QUIZ_CPT:
			break;
		default:
	}
}

add_action( 'save_post', 'learn_press_maybe_sync_data' );

/**
 * Return id of current screen.
 *
 * @since 3.2.6
 *
 * @return bool|string
 */
function learn_press_get_screen_id() {
	global $current_screen;
	$screen_id = $current_screen ? $current_screen->id : false;

	return $screen_id;
}

/**
 * Check if current screen is a page of LP or
 * editing post type of LP such as course, lesson, etc...
 *
 * @since 3.2.6
 *
 * @return bool
 */
function learn_press_is_admin_page() {
	$screen_id     = learn_press_get_screen_id();
	$is_learnpress = false;

	// Is editing post-type of LP
	$post_types = apply_filters(
		'learn-press/admin-post-type-pages',
		array( LP_COURSE_CPT, LP_QUIZ_CPT, LP_LESSON_CPT, LP_QUESTION_CPT, LP_ORDER_CPT, 'lp_cert', 'lp_assignment' )
	);
	foreach ( $post_types as $post_type ) {
		if ( in_array( $screen_id, array( "edit-{$post_type}", $post_type ) ) ) {
			$is_learnpress = true;
			break;
		}
	}

	// Is sub-menu under LP?
	if ( strpos( $screen_id, 'learnpress_page_' ) === 0 ) {
		$is_learnpress = true;
	}

	return apply_filters( 'learn-press/is-admin-page', $is_learnpress, $screen_id );
}

function learn_press_get_orders_status_chart_data() {
	$data = array(
		'type'    => 'pie',
		'data'    => array(
			'labels'   => array(),//[ 'Pending', 'Processing', 'Completed', 'Failed', 'Cancelled' ],
			'datasets' => [
				array(
					'label'           => '# of Votes',
					'data'            => array(), //[ 12, 19, 3, 5, 2 ],
					//'backgroundColor' => array(),
					//'borderColor'     => array(),
					'backgroundColor' => array(
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)'
					),
					'borderColor'     => array(
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
					),
					'borderWidth'     => 1
				)
			]
		),
		'options' => array(
			'scales' => array(
				'yAxes' => array(
					array(
						'ticks' => array(
							'beginAtZero' => true
						)
					)
				)
			),
//			'legend' => array(
//				'display' => false
//			)
		)
	);

	$order_statuses    = learn_press_get_order_statuses( true, true );
	$specific_statuses = array( 'lp-completed', 'lp-failed'/*, 'lp-on-hold'*/ );

	foreach ( $order_statuses as $status ) {
		if ( ! in_array( $status, $specific_statuses ) ) {
			$specific_statuses[] = $status;
		}
	}

	$labels = learn_press_get_order_statuses();
	$counts = learn_press_count_orders( array( 'status' => $specific_statuses ) );

	foreach ( $counts as $k => $v ) {
		$data['data']['labels'][]              = isset( $labels[ $k ] ) ? $labels[ $k ] : 'Untitled';
		$data['data']['datasets'][0]['data'][] = $v;
		//$data['data']['datasets'][0]['backgroundColor'][] = 'rgba(54, 162, 235, 0.2)';
		//$data['data']['datasets'][0]['borderColor'][]     = 'rgba(54, 162, 235, 1)';
	}

	return $data;
}

/**
 * Callback function to filter outer html of meta box field.
 * If option 'roles' is passed to field and current user does
 * not have the role in that then do not show the field.
 *
 * @since 3.x.x
 *
 * @param string $outer_html
 * @param array  $field
 * @param mixed  $value
 *
 * @return string
 */
function learn_press_meta_box_field_outer_html( $outer_html, $field, $value ) {

	if ( ! empty( $field['roles'] ) ) {
		$roles = $field['roles'];
		settype( $roles, 'array' );

		global $current_user;
		$user_roles = $current_user->roles;

		if ( ! array_intersect( $roles, $user_roles ) ) {
			return '';
		}
	}

	return $outer_html;
}

add_filter( 'rwmb_outer_html', 'learn_press_meta_box_field_outer_html', 10, 3 );

function learn_press_option_course_evaluation_method( $method ) {
	global $post;
	$evaluation_by = get_post_meta( $post->ID, '_lp_course_result_quiz', true );
	switch ( $method ) {
		case 'evaluate_quiz':
			?>
            <p id="course_evaluation_method_quiz_options">
                <input type="checkbox" id="lp-course-result-evaluate-final-quiz" name="_lp_course_result_quiz"
                       value="final_quiz" <?php checked( $evaluation_by === 'final_quiz' ); ?> />
                <label for="lp-course-result-evaluate-final-quiz">
					<?php esc_html_e( 'Only the final quiz', 'learnpress' ); ?>
                </label>
            </p>
			<?php
	}
}

add_action( 'learn-press/option-course-evaluation-method', 'learn_press_option_course_evaluation_method' );

include_once "class-lp-post-type-actions.php";