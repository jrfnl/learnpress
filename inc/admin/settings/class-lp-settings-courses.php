<?php

/**
 * Class LP_Settings_Courses
 */
class LP_Settings_Courses extends LP_Abstract_Settings_Page {
	/**
	 * LP_Settings_Courses constructor.
	 */
	public function __construct() {
		$this->id   = 'courses';
		$this->text = __( 'Courses', 'learnpress' );

		parent::__construct();
	}

	public function save() {
		if ( ! empty( $_POST['learn_press_course_base'] ) ) {
			$course_permalink = $_POST['learn_press_course_base'];
			if ( $course_permalink == 'custom' ) {
				$course_permalink = trim( $_POST['course_permalink_structure'], '/' );

				if ( '%course_category%' == $course_permalink ) {
					$course_permalink = _x( 'courses', 'slug', 'learnpress' ) . '/' . $course_permalink;
				}

				$course_permalink = '/' . $course_permalink;
				update_option( 'learn_press_course_base_type', 'custom' );

			} else {
				delete_option( 'learn_press_course_base_type' );
			}

			$course_base = untrailingslashit( $course_permalink );

			update_option( 'learn_press_course_base', $course_base );
			$courses_page_id   = learn_press_get_page_id( 'courses' );
			$courses_permalink = ( $courses_page_id > 0 && get_post( $courses_page_id ) ) ? get_page_uri( $courses_page_id ) : _x( 'courses', 'default-slug', 'learnpress' );

			if ( $courses_page_id && trim( $course_base, '/' ) === $courses_permalink ) {
				update_option( 'learn_press_use_verbose_page_rules', 'yes' );
			} else {
				delete_option( 'learn_press_use_verbose_page_rules' );
			}
		}
	}

	/**
	 * @param string $section
	 * @param string $tab
	 *
	 * @return array
	 */
	public function get_settings( $section = null, $tab = null ) {

		$generate_course_thumbnail = get_option( 'learn_press_generate_course_thumbnail' ) ? get_option( 'learn_press_generate_course_thumbnail' ) : 'no';

		$settings = apply_filters(
			'learn-press/courses-settings-fields',
			array_merge(
			// General
				apply_filters(
					'learn-press/course-settings-fields/general',
					array(
						array(
							'title' => __( 'General', 'learnpress' ),
							'type'  => 'heading',
						),
//						array(
//							'title'   => __( 'Review course before publishing', 'learnpress' ),
//							'desc'    => __( 'The course needs to be reviewed by admin before it can be published.', 'learnpress' ),
//							'id'      => 'required_review',
//							'default' => 'yes',
//							'type'    => 'yes-no'
//						),
						array(
							'title'   => __( 'Review Courses', 'learnpress' ),
							'desc'    => __( 'Courses created by instructors will be pending in review first.', 'learnpress' ),
							'id'      => 'required_review',
							'default' => 'yes',
							'type'    => 'yes-no'
						),
						array(
							'title'             => __( 'Courses Per Page', 'learnpress' ),
							'desc'              => __( 'Number of courses displayed per page.', 'learnpress' ),
							'id'                => 'archive_course_limit',
							'default'           => '10',
							'type'              => 'number',
							'custom_attributes' => array(
								'min' => '1'
							),
							'placeholder'       => '10'
						),
//						array(
//							'title'      => __( 'Enable editing published course', 'learnpress' ),
//							'desc'       => __( 'Allow instructors to edit the course which were published without review.<br /> If this option is disabled, the course status will be changed to Pending Review when the instructor update course.', 'learnpress' ),
//							'id'         => 'enable_edit_published',
//							'default'    => 'yes',
//							'type'       => 'yes-no',
//							'visibility' => array(
//								'state'       => 'show',
//								'conditional' => array(
//									'field'   => 'required_review',
//									'compare' => '=',
//									'value'   => 'yes'
//								)
//							)
//						),
						// @since 3.3.0
//						array(
//							'name'    => __( 'Auto finish course', 'learnpress' ),
//							'id'      => 'auto_finish_course',
//							'type'    => 'yes-no',
//							'desc'    => __( 'Auto finish course if duration of course expire.', 'learnpress' ),
//							'default' => 'yes',
//							'inline'  => false
//						),
//						array(
//							'name'    => __( 'Force complete items', 'learnpress' ),
//							'id'      => 'force_complete_course_items',
//							'type'    => 'yes-no',
//							'desc'    => __( 'Force to complete items (e.g quizzes) in current progress before finish course.', 'learnpress' ),
//							'default' => 'no'
//						),
//						array(
//							'name'    => __( 'Block course', 'learnpress' ),
//							'id'      => 'course_blocking',
//							'type'    => 'radio',
//							'options' => array(
//								'no'                                 => __( 'No.', 'learnpress' ),
//								'duration_expire'                    => __( 'Block if duration expire.', 'learnpress' ),
//								'course_finished'                    => __( 'Block if course is finished.', 'learnpress' ),
//								'duration_expire_or_course_finished' => __( 'Block if duration expire or course is finished.', 'learnpress' ),
//							),
//							'desc'    => __( 'Action when course is finished.', 'learnpress' ),
//							'default' => 'no',
//							'std'     => 'no',
//							'inline'  => false
//						),
//						array(
//							'name'       => __( 'Block content', 'learnpress' ),
//							'id'         => 'course_content_blocking',
//							'type'       => 'radio',
//							'options'    => array(
//								'content_items'     => __( 'Block content of items.', 'learnpress' ),
//								'course_curriculum' => __( 'Block course curriculum.', 'learnpress' )
//							),
//							'default'    => 'content_items',
//							'std'        => 'content_items',
//							'inline'     => false,
//							'visibility' => array(
//								'state'       => 'hide',
//								'conditional' => array(
//									'field'   => 'course_blocking',
//									'compare' => '=',
//									'value'   => 'no'
//								)
//							)
//						),
					)
				),
				// Archive
//				apply_filters(
//					'learn-press/course-settings-fields/archive',
//					array(
//						array(
//							'title' => __( 'Archive', 'learnpress' ),
//							'type'  => 'heading',
//							'desc'  => __( 'Those settings are applied to archive course page.', 'learnpress' )
//						),
////						array(
////							'title'   => __( 'Courses Page', 'learnpress' ),
////							'id'      => 'courses_page_id',
////							'default' => '',
////							'type'    => 'pages-dropdown'
////						),
//						array(
//							'title'             => __( 'Courses per page', 'learnpress' ),
//							'desc'              => __( 'Number of courses displayed per page.', 'learnpress' ),
//							'id'                => 'archive_course_limit',
//							'default'           => '10',
//							'type'              => 'number',
//							'custom_attributes' => array(
//								'min' => '1'
//							)
//						)
//					)
//				),
				// Single course
				apply_filters(
					'learn-press/course-settings-fields/single',
					array(
						array(
							'title' => __( 'Permalinks', 'learnpress' ),
							'type'  => 'heading',
						),
						array(
							'title'   => __( 'Course', 'learnpress' ),
							'type'    => 'course-permalink',
							'default' => '',
							'id'      => 'course_base'
						),
						array(
							'title'       => __( 'Lesson', 'learnpress' ),
							'type'        => 'text',
							'id'          => 'lesson_slug',
							'desc'        => __( sprintf( 'e.g. %s/course/sample-course/<code>lessons</code>/sample-lesson/', home_url() ), 'learnpress' ),
							'default'     => 'lessons',
							'placeholder' => 'lesson'
						),
						array(
							'title'       => __( 'Quiz', 'learnpress' ),
							'type'        => 'text',
							'id'          => 'quiz_slug',
							'desc'        => __( sprintf( 'e.g. %s/course/sample-course/<code>quizzes</code>/sample-quiz/', home_url() ), 'learnpress' ),
							'default'     => 'quizzes',
							'placeholder' => 'quizzes'
						),
						array(
							'title'   => __( 'Category Base', 'learnpress' ),
							'id'      => 'course_category_base',
							'default' => 'course-category',
							'type'    => 'text',
							'placeholder' => 'course-category',
							'desc'        => __( sprintf( 'e.g. %s/course/%s/sample-course/', home_url(), '<code>course-category</code>' ), 'learnpress' ),
						),
						array(
							'title'   => __( 'Tag Base', 'learnpress' ),
							'id'      => 'course_tag_base',
							'default' => 'course-tag',
							'type'    => 'text',
							'placeholder' => 'course-tag',
							'desc'        => __( sprintf( 'e.g. %s/course/%s/sample-course/', home_url(), '<code>course-tag</code>' ), 'learnpress' ),
						),
//						array(
//							'title'   => __( 'Lesson', 'learnpress' ),
//							'type'    => 'text',
//							'id'      => 'lesson_slug',
//							'desc'    => __( sprintf( '%s/course/sample-course/<code>lessons</code>/sample-lesson/', home_url() ), 'learnpress' ),
//							'default' => 'lessons'
//						),
//						array(
//							'title'   => __( 'Quiz', 'learnpress' ),
//							'type'    => 'text',
//							'id'      => 'quiz_slug',
//							'desc'    => __( sprintf( '%s/course/sample-course/<code>quizzes</code>/sample-quiz/', home_url() ), 'learnpress' ),
//							'default' => 'quizzes'
//						),
//						array(
//							'title'   => __( 'Enrolled students number', 'learnpress' ),
//							'type'    => 'yes_no',
//							'id'      => 'enrolled_students_number',
//							'desc'    => __( 'Displays a fake numbers of enrolled students. Disable to show the real value.', 'learnpress' ),
//							'default' => 'quizzes'
//						),
					)
				)
				// Thumbnail
//				apply_filters( 'learn-press/course-settings-fields/thumbnails', array(
//						array(
//							'title' => __( 'Course thumbnails', 'learnpress' ),
//							'type'  => 'heading',
//							'desc'  => __( 'Thumbnail generation for archive/single course.', 'learnpress' )
//						),
////						array(
////							'title'   => __( 'Single course', 'learnpress' ),
////							'id'      => 'generate_course_thumbnail',
////							'default' => 'yes',
////							'type'    => 'yes-no',
////							'desc'    => __( 'Turn on/off courses extra thumbnail.', 'learnpress' ),
////						),
////						array(
////							'title'      => __( 'Thumbnail dimensions', 'learnpress' ),
////							'id'         => 'single_course_image_size',
////							'default'    => array( 800, 450, 'yes' ),
////							'type'       => 'image-dimensions',
////							'visibility' => array(
////								'state' => 'show',
////
////								'conditional' => array(
////									'field'   => 'generate_course_thumbnail',
////									'compare' => '=',
////									'value'   => 'yes'
////								)
////							)
////						),
//						array(
//							'title'   => __( 'Archive course', 'learnpress' ),
//							'id'      => 'archive_course_thumbnail',
//							'default' => 'yes',
//							'type'    => 'yes-no',
//							'desc'    => __( 'Turn on/off courses extra thumbnail.', 'learnpress' ),
//						),
//						array(
//							'title'      => __( 'Thumbnail dimensions', 'learnpress' ),
//							'id'         => 'course_thumbnail_image_size',
//							'default'    => array( 400, 250, 'yes' ),
//							'type'       => 'image-dimensions',
//							'visibility' => array(
//								'state' => 'show',
//
//								'conditional' => array(
//									array(
//										'field'   => 'archive_course_thumbnail',
//										'compare' => '=',
//										'value'   => 'yes'
//									)
//								)
//							)
//						)
//					)
			)
		);


		// Removed from 2.1.4
		/*array(
			'title'   => __( 'Show list of question in quiz', 'learnpress' ),
			'desc'    => __( 'Show/Hide list questions in quiz.', 'learnpress' ),
			'id' => 'disable_question_in_quiz',
			'default' => 'yes',
			'type'    => 'checkbox'
		),*/

		/* Temporary remove from 2.1.4
		array(
			'title'   => __( 'Auto redirect next lesson', 'learnpress' ),
			'desc'    => __( 'Redirect to the next lesson after completed the lesson', 'learnpress' ),
			'id' => 'auto_redirect_next_lesson',
			'default' => 'no',
			'type'    => 'checkbox'
		),
		array(
			'title'             => __( 'Time delay redirect', 'learnpress' ),
			'desc'              => __( 'The item will be redirected after certain amount of time, unit: seconds (s)', 'learnpress' ),
			'id' => 'auto_redirect_time',
			'default'           => '3',
			'type'              => 'number',
			'custom_attributes' => array(
				'min' => '0'
			)
		),
		array(
			'title'   => __( 'Auto redirect message ', 'learnpress' ),
			'desc'    => '',
			'id' => 'auto_redirect_message',
			'default' => 'Redirecting to the next item ... ',
			'type'    => 'text'
		),*/

		// Deprecated hook.
		return apply_filters( 'learn_press_courses_settings', $settings );
	}
}

return new LP_Settings_Courses();