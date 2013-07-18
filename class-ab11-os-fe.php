<?php
//Change for PRODUCTION
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
// include_once($_SERVER['DOCUMENT_ROOT'].'www.wp.dev/wp-load.php' );

class AB11_OS_FE {
	protected	$wpdb,
						$ab11_os_db_table,
						$ab11_os_published_semesters,
						$ab11_os_blog_id,
						$semester_list,
						$subject_list,
						$subject_map,
						$career,
						$courses,
						$semester_id,
						$readable_vars,
						$writable_vars;

	protected static $instance = NULL;

	protected $slug = 'ab11-os-fe';

	protected $version = '1.1.0';

	private function __construct() {
		add_action( 'wp_ajax_nonpriv_ab11_os_show_semesters', array ( $this, 'show_semesters' ), 10, 0 );
		add_action( 'wp_ajax_ab11_os_show_semesters', array ( $this, 'show_semesters' ), 10, 0 );

		add_action( 'wp_ajax_nonpriv_ab11_os_set_semester', array ( $this, 'set_semester' ), 10, 0 );
		add_action( 'wp_ajax_ab11_os_set_semester', array ( $this, 'set_semester' ), 10, 0 );

		add_action( 'wp_ajax_nonpriv_ab11_os_get_subjects', array ( $this, 'get_subjects' ), 10, 1 );
		add_action( 'wp_ajax_ab11_os_get_subjects', array ( $this, 'get_subjects' ), 10, 1 );

		add_action( 'wp_ajax_nonpriv_ab11_os_get_calendar', array ( $this, 'get_calendar' ), 10, 1 );
		add_action( 'wp_ajax_ab11_os_get_calendar', array ( $this, 'get_calendar' ), 10, 1 );

		add_action( 'wp_ajax_nonpriv_ab11_os_get_courses', array ( $this, 'get_courses' ), 10, 1 );
		add_action( 'wp_ajax_ab11_os_get_courses', array ( $this, 'get_courses' ), 10, 1 );

		add_action( 'wp_ajax_nonpriv_ab11_os_test', array ( $this, 'test' ), 10, 0 );
		add_action( 'wp_ajax_ab11_os_test', array ( $this, 'test' ), 10, 0 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->ab11_os_blog_id = $this->wpdb->prefix;

		$this->semester_list = $this->get_semesters();

		$this->subject_map = array(
		'ALL' => 'All Subjects',
		'ACC' => 'Accounting',
		'ADJ' => 'Administration of Justice',
		'AGR' => 'Agriculture',
		'AIR' => 'Air Conditioning and Refrigeration',
		'ARC' => 'Architecture',
		'ART' => 'Art',
		'ASL' => 'American Sign Language',
		'AST' => 'Administrative Support Technology',
		'BIO' => 'Biology',
		'BLD' => 'Building',
		'BUS' => 'Business Management and Administration',
		'CAD' => 'Computer Aided Drafting and Design',
		'CHD' => 'Child Care',
		'CHI' => 'Chinese',
		'CHM' => 'Chemistry',
		'CST' => 'Communication Studies and Theatre',
		'CSC' => 'Computer Science',
		'SCS' => 'Computer Science',
		'DRF' => 'Drafting',
		'ECO' => 'Economics',
		'EDU' => 'Education',
		'ELE' => 'Electrical Technology',
		'ETR' => 'Electronics Technology',
		'EGR' => 'Engineering',
		'EMS' => 'Emergency Medical Services',
		'EMT' => 'Emergency Medical Technology',
		'ENF' => 'English Fundamentals',
		'ENG' => 'English',
		'ENV' => 'Environmental Science',
		'ESL' => 'English as a Second Language',
		'FIN' => 'Financial Services',
		'FRE' => 'French',
		'FST' => 'Fire Science Technology',
		'GEO' => 'Geography',
		'GER' => 'German',
		'GIS' => 'Geograph Info Systems',
		'GOL' => 'Geology',
		'HLT' => 'Health',
		'HIM' => 'Health Information Management',
		'HIS' => 'History',
		'HMS' => 'Human Services',
		'HRI' => 'Hotel-Restaurant-Institutional Management',
		'HRT' => 'Horticulture',
		'HUM' => 'Humanities',
		'IND' => 'Industrial Engineering Technology',
		'ITD' => 'Information Technology Database Processing',
		'ITE' => 'Information Technology Essentials',
		'ITN' => 'Information Technology Networking',
		'ITP' => 'Information Technology Programming',
		'JPN' => 'Japanese',
		'LGL' => 'Legal Administration',
		'MAC' => 'Machine Technology',
		'MAR' => 'Marine Science',
		'MDA' => 'Medical Assisting',
		'MDL' => 'Medical Laboratory',
		'MEC' => 'Mechanical Engineering Technology',
		'MEN' => 'Mental Health',
		'MKT' => 'Marketing',
		'MTH' => 'Math',
		'MTT' => 'Math Taught Through Technology',
		'MUS' => 'Music',
		'NAS' => 'Natural Science',
		'NUR' => 'Nursing',
		'PBS' => 'Public Service',
		'PED' => 'Physical Education and Recreation',
		'PHI' => 'Philosophy',
		'PHT' => 'Photography',
		'PHY' => 'Physics',
		'PLS' => 'Political Science',
		'PNE' => 'Practical Nursing',
		'PPT' => 'Pulp and Paper Technology',
		'PSY' => 'Psychology',
		'REA' => 'Real Estate',
		'REL' => 'Religion',
		'RUS' => 'Russian',
		'SCM' => 'Sign Communications',
		'SDV' => 'Student Development',
		'SOC' => 'Sociology',
		'SPA' => 'Spanish',
		'SPD' => 'Speech and Drama',
		'SSC' => 'Social Science',
		'TEL' => 'Telecommunications Management',
		'TRV' => 'Travel and Tourism',
		'VEN' => 'Viticulture and Enology',
		'WEL' => 'Welding',
		);

		$this->readable_vars = array(
			'semester_list',
			'subject_list',
			'subject_map',
			'career',
			'courses',
			'semester_id'
			);
		$this->writable_vars = array(
			'semester_list',
			'subject_list',
			'subject_map',
			'career',
			'courses',
			'semester_id'
			);

	}



	// public static function init() {
	// 	add_action('plugins_loaded', array(self::instance(), 'setup'));

	// }

	public static function get_instance() {
		is_null(self::$instance) && self::$instance = new self;
		return self::$instance;

	}

	public function enqueue_scripts(){
		if( is_page_template( 'template-online-schedule.php' ) ) {
			wp_enqueue_script(
				$this->slug . '_scripts',
				plugins_url( 'scripts.js', __FILE__ ),
				array( 'jquery' ),
				$this->version,
				true
			);

			wp_localize_script( $this->slug . '_scripts', 'ab11_os_ajax_request', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Public getter for protected variables
	 * @mvc Model
	 * @author Ian Dunn <ian@iandunn.name>
	 * @param string $variable
	 * @return mixed
	 */
	public function __get( $variable )
	{
		if( in_array( $variable, $this->readable_vars ) )
			return $this->$variable;
		else
			throw new Exception( __METHOD__ . " error: $". $variable ." doesn't exist or isn't readable." );
	}

	/**
	 * Public setter for protected variables
	 * @mvc Model
	 * @author Ian Dunn <ian@iandunn.name>
	 * @param string $variable
	 * @param mixed $value
	 */
	public function __set( $variable, $value )
	{
		if( in_array( $variable, $this->writable_vars ) )
		{
			$this->$variable = $value;
			
			if( !$this->isValid( $variable ) )
				throw new Exception( __METHOD__ . ' error: $'. $value .' is not valid.' );
		}
		else
			throw new Exception( __METHOD__ . " error: $". $variable ." doesn't exist or isn't writable." );
	}
	public function test( ) {
		$args = $_POST;
		echo 'hello'; //. var_dump( $this->subject_map );
		die();
	}

	public function show_semesters() {
		echo $this->semester_list;
		die();
	}

	public function set_semester() {
		$args = $_POST;

		self::$semester_id = isset( $args['semester_id'] ) ? $args['semester_id'] : NULL;
		$this->career	=	isset( $args['career'] ) ? $args['career'] : 'CRED';

		self::get_courses();

		self::set_subjects();
	}

	public function get_subjects() {
		echo $this->subject_list;
		die();
	}

	private function set_subjects() {
		$subjects_unique = [];

		foreach ($this->courses as $course ) {
				$subjects_unique[] = $course['subject'];

		}
		$subjects_unique = array_unique($subjects_unique);
		array_unshift( $subjects_unique, 'ALL');
		$columns = ceil(count($subjects_unique)/16);
		$i=0;
		$j=1;
		$output .='<div class="total-columns-' . $columns . ' col col-' . $j . '">';

		foreach($subjects_unique as $subject) {
			if ( $i < 16) {

			} elseif ( $i == 16 ) {
				$j++;
				$output .= '</div><div class="total-columns-' . $columns . ' col col-' . $j . '">';

				$i=0;
			} elseif ( $i > 16) {
				$output .= '<li>Error processing subjects</li>';
			}
			$output .= '<li><div class="is-hidden tooltip"><span>' . $this->subject_map[$subject] . '</span>';
			$output .= '</div><a href="#" class="select-subject" data-subject="' . $subject . '">' . $subject . '</a></li>';


			$i++;
		}
		$output .= '</div>';
		$this->subject_list = $output;

	}

	private function set_subject_list( ) {
		// $args = $_POST;

		// $this->semester_id = ( isset( $args['semester_id'] ) ) ? $args['semester_id'] : NULL;


		$output = '';

		$db_table = $this->wpdb->base_prefix . 'ab11_os_' . $this->semester_id;

		$subjects_full = $this->wpdb->get_results(
			"SELECT subject FROM $db_table WHERE career = '" . $this->career . "'", ARRAY_N );
		$subjects_unique = [];

		foreach ($subjects_full as $subject ) {
			foreach ($subject as $value ){
				$subjects_unique[] = $value;
			}
		}
		$subjects_unique = array_unique($subjects_unique);
		array_unshift( $subjects_unique, 'ALL');
		$columns = ceil(count($subjects_unique)/16);
		$i=0;
		$j=1;
		$output .='<div class="total-columns-' . $columns . ' col col-' . $j . '">';

		foreach($subjects_unique as $subject) {
			if ( $i < 16) {

			} elseif ( $i == 16 ) {
				$j++;
				$output .= '</div><div class="total-columns-' . $columns . ' col col-' . $j . '">';

				$i=0;
			} elseif ( $i > 16) {
				$output .= '<li>Error processing subjects</li>';
			}
			$output .= '<li><div class="is-hidden tooltip"><span>' . $this->subject_map[$subject] . '</span>';
			$output .= '</div><a href="#" class="select-subject" data-subject="' . $subject . '">' . $subject . '</a></li>';


			$i++;
		}
		$output .= '</div>';
		$this->subject_list = $output;
		die();
	}

	public function get_calendar (  ) {
		$args = $_POST;
				$this->semester_id = ( isset( $args['semester_id'] ) ) ? $args['semester_id'] : NULL;

		$chunks = str_split( $this->semester_id, 1 );
		$output = '';

		switch ($chunks[3]) {
			case '2':
				$page_id = 8;
		 		break;

		 	case '3':
				$page_id = 10;
		 		break;

		 	case '4':
		 		$page_id = 105;
		 		break;

		 	default:
		 		$page_id = 6; //defaults to Fall
		 		break;
		}

		$page = $this->wpdb->get_row("SELECT * FROM " . $this->wpdb->posts . " WHERE id = $page_id");
		$output .= $page->post_content;
		apply_filters('the_content', $output);
		echo $output;
		die();
	}

	public function get_courses (  ) {
		$args = $_POST;
		$this->semester_id = ( isset( $args['semester_id'] ) ) ? $args['semester_id'] : NULL;
		$this->career	=	isset( $args['career'] ) ? $args['career'] : 'CRED';
		$db_table = $this->wpdb->base_prefix . 'ab11_os_' . $this->semester_id;

		$this->courses = $this->wpdb->get_results(
			"SELECT * FROM $db_table WHERE career = '" . $this->career . "'", ARRAY_A );

	}


	private function get_semesters() {
		$db_tables = $this->wpdb->get_results( "SELECT * FROM " . AB11_OS_DB_ADMIN_TABLE . " WHERE (status = 'published') ORDER BY semester_id DESC");

		$output = '';

		foreach ($db_tables as $semester) {
				$semester_name = self::decrypt_semester_id( $semester->semester_id );
				$output .= '<li><a href="#" class="select-semester" data-career="CRED" data-semester-id="';
				$output .= $semester->semester_id . '">' . $semester_name . '</a></li>' . "\n";

				$output .= '<li><a href="#" class="select-semester" data-career="CNED" data-semester-id="';
				$output .= $semester->semester_id . '">' . $semester_name . ' Workforce</a></li>' . "\n";
		}

		return $output;
	}

	private function decrypt_semester_id( $semester_id ) {
		$chunks = str_split( $semester_id, 1 );
		$output = '';

		switch ($chunks[3]) {
		 	case '2':
			 	$output .= 'Spring 201' . $chunks[2];
		 		break;

		 	case '3':
			 	$output .= 'Summer 201' . $chunks[2];
		 		break;

		 	case '4':
			 	$output .= 'Fall 201' . $chunks[2];
		 		break;

		 	default:
		 		$output .= 'Semester 201' . $chunks[2];
		 		break;
		 }
		 return $output;
	}
}

?>