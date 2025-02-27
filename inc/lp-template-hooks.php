<?php
/**
 * Build courses content
 */

/*****************************************/
/**                                      */
/**            DOCUMENTATION             */
/**                                      */
/*****************************************/

/**
 * Core template classes: LP_Template_General, LP_Template_Profile, LP_Template_Course.
 *
 * + Get instance of a template: LP()->template( TYPE ) e.g: LP()->template( 'course' )
 * + LP()->template( TYPE )->func(CALLBACK) => hook to an action with function CALLBACK of TYPE class
 * + LP()->template( TYPE )->callback( TEMPLATE ) => hook to an action to c
 */


defined( 'ABSPATH' ) || exit();

/**
 * New functions since 3.0.0
 */

/**
 * Header and Footer
 *
 * @see LP_Template_General::template_header()
 * @see LP_Template_General::template_footer()
 */
add_action( 'learn-press/template-header', LP()->template( 'general' )->func( 'template_header' ) );
add_action( 'learn-press/template-footer', LP()->template( 'general' )->func( 'template_footer' ) );

/**
 * Course breadcrumb
 *
 * @see LP_Template_General::breadcrumb()
 */
add_action( 'learn-press/before-main-content', LP()->template( 'general' )->text('<div class="lp-archive-courses">') ,-100);
add_action( 'learn-press/before-main-content', LP()->template( 'general' )->func( 'breadcrumb' ) );

add_action( 'learn-press/after-main-content', LP()->template( 'general' )->text('</div>'), 100 );


/**
 * Course buttons
 *
 * @see learn_press_course_purchase_button
 * @see learn_press_course_enroll_button
 * @see learn_press_course_retake_button
 * @see learn_press_course_continue_button
 * @see learn_press_course_finish_button
 * @see learn_press_course_external_button
 */
learn_press_add_course_buttons();


/** BEGIN: Archive course */
add_action( 'learn-press/before-courses-loop', LP()->template( 'course' )->func( 'courses_top_bar' ), 10 );

/** BEGIN: Archive course loop item **/
add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->text( '<div class="course-wrap-thumbnail">' ), 1 );
add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->callback( 'loop/course/badge-featured' ), 5 );
add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->callback( 'loop/course/thumbnail.php' ), 10 );
add_action( 'learn-press/before-courses-loop-item',  LP()->template( 'course' )->text( '</div>' ), 1000);

add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->text( '<!-- START .course-content --> <div class="course-content">' ), 1000 );
add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->callback( 'loop/course/categories' ), 1010 );
add_action( 'learn-press/before-courses-loop-item', LP()->template( 'course' )->callback( 'loop/course/instructor' ), 1010 );
add_action( 'learn-press/courses-loop-item-title', LP()->template( 'course' )->callback( 'loop/course/title.php' ), 20 );

/**
 * @see LP_Template_Course::courses_loop_item_meta()
 * @see LP_Template_Course::courses_loop_item_info_begin()
 * @see LP_Template_Course::clearfix()
 * @see LP_Template_Course::courses_loop_item_students()
 * @see LP_Template_Course::courses_loop_item_price()
 * @see LP_Template_Course::courses_loop_item_info_end()
 * @see LP_Template_Course::loop_item_user_progress()
 */
/* */
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->text( '<!-- START .course-content --> <div class="course-wrap-meta">' ), 20 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->callback( 'single-course/meta/duration' ), 20 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->callback( 'single-course/meta/level' ), 20 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'count_object' ), 20 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->text( '</div> <!-- END .course-content -->' ), 20 );

add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'courses_loop_item_meta' ), 25 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'courses_loop_item_info_begin' ), 20 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'clearfix' ), 30 );

add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->text( '<!-- START .course-content --> <div class="course-footer">' ), 40 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'courses_loop_item_students' ), 40 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'courses_loop_item_price' ), 50 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->text( '</div> <!-- END .course-content -->' ), 50 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'course_readmore' ), 55 );

add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'courses_loop_item_info_end' ), 60 );
add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->func( 'loop_item_user_progress' ), 70 );

add_action( 'learn-press/after-courses-loop-item', LP()->template( 'course' )->text( '</div> <!-- END .course-content -->' ), 1000 );

/** END: Archive course loop item */

/** Archive course pagination */
add_action( 'learn-press/after-courses-loop', LP()->template( 'course' )->callback( 'loop/course/pagination.php' ), 10 );
/** END: Archive course */

/** BEGIN: Main content of single course */


// Sidebar and content
add_action( 'learn-press/single-course-summary', LP()->template( 'course' )->callback( 'single-course/content' ), 10 );
//add_action( 'learn-press/single-course-summary', LP()->template( 'course' )->callback( 'single-course/sidebar' ), 20 );

// Content
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( '<div class="course-detail-info"> <div class="content-area"> <div class="course-info-left">' ), 10 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/meta-primary' ), 10 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/title' ), 10 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/meta-secondary' ), 10 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( ' </div> </div> </div>' ), 15 );

add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( '<div class="entry-content content-area">' ), 30 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( '<div class="entry-content-left">' ), 35 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->func( 'course_extra_boxes' ), 40 );
//add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/progress' ), 40 );
//add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->func( 'remaining_time' ), 50 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/tabs/tabs' ), 60 );
//add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/buttons' ), 70 );
//add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/tags' ), 80 );
add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( '<!-- end entry content left --> </div>' ), 80 );

add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->callback( 'single-course/sidebar' ), 85 );

add_action( 'learn-press/course-content-summary', LP()->template( 'course' )->text( ' </div>' ), 100 );

// Meta
add_action( 'learn-press/course-meta-primary-left', LP()->template( 'course' )->callback( 'single-course/meta/instructor' ), 10 );
add_action( 'learn-press/course-meta-primary-left', LP()->template( 'course' )->callback( 'single-course/meta/category' ), 20 );

add_action( 'learn-press/course-meta-secondary-left', LP()->template( 'course' )->callback( 'single-course/meta/duration' ), 10 );
add_action( 'learn-press/course-meta-secondary-left', LP()->template( 'course' )->callback( 'single-course/meta/level' ), 20 );
add_action( 'learn-press/course-meta-secondary-left', LP()->template( 'course' )->func( 'count_object' ), 20 );

//add_action( 'learn-press/course-meta-primary-right', LP()->template( 'course' )->callback( 'single-course/meta/category' ) );
//add_action( 'learn-press/course-meta-primary-right', LP()->template( 'course' )->callback( 'single-course/meta/category' ) );



// Sidebar content
/**
 * @see LP_Template_Course::course_sidebar_preview()
 * @see LP_Template_Course::course_extra_key_features()
 * @see LP_Template_Course::course_extra_requirements()
 */
add_action( 'learn-press/course-summary-sidebar', LP()->template( 'course' )->func( 'course_sidebar_preview' ), 10 );
add_action( 'learn-press/course-summary-sidebar', LP()->template( 'course' )->func( 'course_featured_review' ), 10 );
//add_action( 'learn-press/course-summary-sidebar', LP()->template( 'course' )->func( 'course_extra_key_features' ), 20 );
//add_action( 'learn-press/course-summary-sidebar', LP()->template( 'course' )->func( 'course_extra_requirements' ), 30 );

/** END: Main content of single course */

/** BEGIN: Course section */
add_action( 'learn-press/section-summary', LP()->template( 'course' )->callback( 'single-course/section/title.php', array( 'section' ) ), 10 );
add_action( 'learn-press/section-summary', LP()->template( 'course' )->callback( 'single-course/section/content.php', array( 'section' ) ), 20 );

add_action( 'learn-press/after-section-loop-item-title', LP()->template( 'course' )->callback( 'single-course/section/item-meta.php', array(
	'item',
	'section'
) ), 10, 2 );

/** BEGIN: Quiz item */

/**
 * @see LP_Template_Course::quiz_meta_questions()
 * @see LP_Template_Course::item_meta_duration()
 * @see LP_Template_Course::quiz_meta_final()
 */
add_action( 'learn-press/course-section-item/before-lp_quiz-meta', LP()->template( 'course' )->func( 'quiz_meta_questions' ), 10 );
add_action( 'learn-press/course-section-item/before-lp_quiz-meta', LP()->template( 'course' )->func( 'item_meta_duration' ), 20 );
add_action( 'learn-press/course-section-item/before-lp_quiz-meta', LP()->template( 'course' )->func( 'quiz_meta_final' ), 30 );
/** END: Quiz item */

/** BEGIN: Lesson item */
add_action( 'learn-press/course-section-item/before-lp_lesson-meta', LP()->template( 'course' )->func( 'item_meta_duration' ), 10 );
/** END: Lesson item */

/** END: Course section */

/** BEGIN: Popup */

/**
 * @see LP_Template_Course::popup_header()
 * @see LP_Template_Course::popup_sidebar()
 * @see LP_Template_Course::popup_content()
 * @see LP_Template_Course::popup_footer()
 */
add_action( 'learn-press/single-item-summary', LP()->template( 'course' )->func( 'popup_header' ), 10 );
add_action( 'learn-press/single-item-summary', LP()->template( 'course' )->func( 'popup_sidebar' ), 20 );
add_action( 'learn-press/single-item-summary', LP()->template( 'course' )->func( 'popup_content' ), 30 );
add_action( 'learn-press/single-item-summary', LP()->template( 'course' )->func( 'popup_footer' ), 40 );

/**
 * @see LP_Template_Course::popup_footer_nav()
 */
add_action( 'learn-press/popup-footer', LP()->template( 'course' )->func( 'popup_footer_nav' ), 10 );
/** END: Popup */

/** BEGIN: Popup quiz */
/**
 * @see LP_Template_Course::course_finish_button()
 */
add_action( 'learn-press/quiz-buttons', LP()->template( 'course' )->func( 'course_finish_button' ), 10 );
/** END: Popup quiz */

/** BEGIN: Popup lesson */

/**
 * @see LP_Template_Course::item_lesson_title()
 * @see LP_Template_Course::item_lesson_content()
 * @see LP_Template_Course::item_lesson_content_blocked()
 * @see LP_Template_Course::item_lesson_complete_button()
 * @see LP_Template_Course::course_finish_button()
 */
add_action( 'learn-press/before-content-item-summary/lp_lesson', LP()->template( 'course' )->func( 'item_lesson_title' ), 10 );
add_action( 'learn-press/content-item-summary/lp_lesson', LP()->template( 'course' )->func( 'item_lesson_content' ), 10 );
add_action( 'learn-press/content-item-summary/lp_lesson', LP()->template( 'course' )->func( 'item_lesson_content_blocked' ), 15 );
add_action( 'learn-press/after-content-item-summary/lp_lesson', LP()->template( 'course' )->func( 'item_lesson_complete_button' ), 10 );
add_action( 'learn-press/after-content-item-summary/lp_lesson', LP()->template( 'course' )->func( 'course_finish_button' ), 15 );
/** END: Popup lesson */

/**
 * @see LP_Template_Course::course_item_content()
 */
add_action( 'learn-press/course-item-content', LP()->template( 'course' )->func( 'course_item_content' ), 5 );
//add_action( 'learn-press/after-course-item-content', 'learn_press_content_item_nav', 5 );

/**
 * @see LP_Template_Course::lesson_comment_form()
 */
//add_action( 'learn-press/after-course-item-content', LP()->template( 'course' )->func( 'lesson_comment_form' ), 10 );

/** BEGIN: User profile */

/**
 * @see LP_Template_Profile::header()
 * @see LP_Template_Profile::tabs()
 * @see LP_Template_Profile::content()
 */
add_action( 'learn-press/before-user-profile', LP()->template( 'profile' )->func( 'header' ), 5 );
add_action( 'learn-press/user-profile', LP()->template( 'profile' )->func( 'tabs' ), 5 );
add_action( 'learn-press/user-profile', LP()->template( 'profile' )->func( 'content' ), 10 );

add_action( 'learn-press/profile/orders', LP()->template( 'profile' )->callback( 'profile/tabs/orders/list.php' ), 5 );
add_action( 'learn-press/profile/orders', LP()->template( 'profile' )->callback( 'profile/tabs/orders/recover-order.php' ), 10 );

/**
 * @see LP_Template_Profile::order_details()
 * @see LP_Template_Profile::order_recover()
 * @see LP_Template_Profile::order_message()
 */
add_action( 'learn-press/profile/order-details', LP()->template( 'profile' )->func( 'order_details' ), 5 );
add_action( 'learn-press/profile/order-details', LP()->template( 'profile' )->func( 'order_recover' ), 10 );
add_action( 'learn-press/profile/order-details', LP()->template( 'profile' )->func( 'order_message' ), 15 );

/**
 * @see LP_Template_Profile::dashboard_logged_in()
 * @see LP_Template_Profile::dashboard_user_bio()
 */
add_action( 'learn-press/profile/dashboard-summary', LP()->template( 'profile' )->func( 'dashboard_logged_in' ), 5 );
add_action( 'learn-press/profile/dashboard-summary', LP()->template( 'profile' )->func( 'dashboard_user_bio' ), 10 );

/**
 * @see LP_Template_Profile::dashboard_not_logged_in()
 * @see LP_Template_Profile::login_form()
 * @see LP_Template_Profile::register_form()
 */
add_action( 'learn-press/user-profile', LP()->template( 'profile' )->func( 'dashboard_not_logged_in' ), 5 );
add_action( 'learn-press/user-profile', LP()->template( 'profile' )->func( 'login_form' ), 10 );
add_action( 'learn-press/user-profile', LP()->template( 'profile' )->func( 'register_form' ), 15 );

add_action( 'learn-press/before-profile-nav', LP()->template( 'profile' )->callback( 'profile/mobile-menu.php' ), 5 );
/** END: User profile */

/** BEGIN: Become teacher form */

/**
 * @see LP_Template_General::become_teacher_messages()
 * @see LP_Template_General::become_teacher_heading()
 * @see LP_Template_General::become_teacher_form_fields()
 * @see LP_Template_General::become_teacher_button()
 */
add_action( 'learn-press/before-become-teacher-form', LP()->template( 'general' )->func( 'become_teacher_messages' ), 10 );
add_action( 'learn-press/before-become-teacher-form', LP()->template( 'general' )->func( 'become_teacher_heading' ), 20 );
add_action( 'learn-press/become-teacher-form', LP()->template( 'general' )->func( 'become_teacher_form_fields' ), 10 );
add_action( 'learn-press/after-become-teacher-form', LP()->template( 'general' )->func( 'become_teacher_button' ), 10 );
/** END: Become teacher form */

/**
 * @see LP_Template_General::checkout_form_login()
 * @see LP_Template_General::checkout_form_register()
 */
add_action( 'learn-press/before-checkout-form', LP()->template( 'general' )->func( 'checkout_form_login' ), 5 );
add_action( 'learn-press/before-checkout-form', LP()->template( 'general' )->func( 'checkout_form_register' ), 10 );
add_action( 'learn-press/checkout-order-review', LP()->template( 'general' )->callback( 'checkout/review-order.php' ), 5 );

add_action( 'learn-press/after-checkout-order-review', LP()->template( 'general' )->callback( 'checkout/order-comment.php' ), 5 );
add_action( 'learn-press/after-checkout-order-review', LP()->template( 'general' )->func( 'order_payment' ), 10 );

/**
 * @see LP_Template_General::order_guest_email()
 * @see LP_Template_General::term_conditions_template()
 * @see LP_Template_Course::back_to_class_button()
 */
add_action( 'learn-press/payment-form', LP()->template( 'general' )->func( 'order_guest_email' ), 15 );
add_action( 'learn-press/after-payment-methods', LP()->template( 'general' )->func( 'term_conditions_template' ) );
add_action( 'learn-press/after-checkout-form', LP()->template( 'general' )->func( 'back_to_class_button' ) );
add_action( 'learn-press/after-empty-cart-message', LP()->template( 'general' )->func( 'back_to_class_button' ) );


// ******************************************************************************************************************* //

//add_action( 'learn-press/course-buttons', function () {
////	$user = LP_Global::user();
////	if ( $user->has_finished_course( get_the_ID() ) ) {
////		echo 'You finished course';
////	}
////} );

add_action( 'learn-press/content-item-summary-class', 'learn_press_content_item_summary_classes', 15 );
add_action( 'learn-press/before-content-item-summary/lp_quiz', LP()->template( 'course' )->callback( 'content-quiz/title.php' ), 5 );
add_action( 'learn-press/content-item-summary/lp_quiz', LP()->template( 'course' )->callback( 'content-quiz/js' ), 25 );
add_action( 'learn-press/parse-course-item', 'learn_press_control_displaying_course_item', 5 );
add_action( 'learn-press/after-single-course', 'learn_press_single_course_args', 5 );
add_filter( 'document_title_parts', 'learn_press_single_document_title_parts', 5 );

add_filter( 'body_class', 'learn_press_body_classes', 10 );
add_filter( 'post_class', 'learn_press_course_class', 15, 3 );
add_action( 'learn-press/after-main-content', LP()->template( 'course' )->callback( 'global/after-main-content.php' ), 5 );
add_action( 'wp_head', 'learn_press_single_course_args', 5 );
add_action( 'learn-press/before-checkout-order-review', LP()->template( 'course' )->callback( 'checkout/form-logged-in.php' ), 10 );
add_filter( 'comments_template_query_args', 'learn_press_comments_template_query_args' );
add_filter( 'get_comments_number', 'learn_press_filter_get_comments_number' );

add_filter( 'excerpt_length', 'learn_press_custom_excerpt_length', 999 );
add_filter( 'learn_press_get_template', LP()->template( 'general' )->func( 'filter_block_content_template' ), 10, 5 );

/**
 * Filter to hide the section if there is no item.
 *
 * @since 4.0.0
 *
 * @param bool              $visible
 * @param LP_Course_Section $section
 * @param LP_Course         $course
 *
 * @return bool
 */
function learn_press_filter_section_visible( $visible, $section, $course ) {
	if ( ! $section->get_items() ) {
		$visible = false;
	}

	return $visible;
}

add_filter( 'learn-press/section-visible', 'learn_press_filter_section_visible', 10, 3 );
