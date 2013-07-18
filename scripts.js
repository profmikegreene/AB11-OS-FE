 /*
 Author: Michael Greene
*/
// JavaScript Document
/* <![CDATA[ */

/* ==================================================================
*
*   //Begin jQuery
*
* -----------------------------------------------------------------*/
jQuery(document).ready(function($){

/* ==================================================================
*
*   Variables
*
* -----------------------------------------------------------------*/
var PLUGIN_DIR = '/wp-content/plugins/ab11-online-schedule';
var THEME_DIR = '/wp-content/themes/AwakenBenehimeV11';
var AJAX_URL = ab11_os_ajax_request.ajaxurl;

var $body = $('body');
$body.data('ab11-os', {} );
var $data = $body.data('ab11-os');

var $options_bar = $('#horz-menu--options-bar');

var $button_semesters = $options_bar.find('#horz-menu-list-item--semesters');
var $list_semesters = $options_bar.find('#horz-menu-sublist--semesters');
var $dropdown_semesters = $options_bar.find('#hidden-semesters');

var $button_subjects = $options_bar.find('#horz-menu-list-item--subjects');
var $list_subjects = $options_bar.find('#horz-menu-sublist--subjects');
var $dropdown_subjects = $options_bar.find('#hidden-subjects');


var $button_options = $options_bar.find('#horz-menu-list-item--options');
var $list_options = $options_bar.find('#horz-menu-sublist--options');
var $dropdown_options = $options_bar.find('#hidden-options');

var $container_content = $('#container-content');

var $breadcrumbs_schedule = $container_content.find('#breadcrumbs-schedule');
var $list_classes = $container_content.find('#class-list');
var $container_calendar = $container_content.find('#container-calendar');
var $container_cubes = $('#container-cubes');

/* ==================================================================
*
*   Delegations
*
* -----------------------------------------------------------------*/
$(document.body).delegate('#horz-menu-sublist--semesters .select-semester', 'click', select_semester);
$(document.body).delegate('#horz-menu-sublist--subjects .select-subject', 'click', select_subject);

if($list_subjects.length){
		$(document.body).delegate('#hidden-subjects li', {
			mouseenter: tooltip_enter,
			mouseleave: tooltip_leave
		});
	}
/* ==================================================================
*
*   Call the init function to get things rolling
*
* -----------------------------------------------------------------*/
init();


/* ==================================================================
*
*   Debugging for #data
*   REMOVE FOR PRODUCTION
*
* -----------------------------------------------------------------*/
$body.click(function() {
	console.log( '$data ' + $.type($data) );
	$.each($data, function (key, value) {
		console.log( key + ' : ' + value );
	});
	console.log( ' ' );
});


/* ==================================================================
*
*   Initialize the web app
*
* -----------------------------------------------------------------*/
function init(e) {
	//grab URL variables and save to the $data object
	get_URL_vars();

	$data['action'] = 'ab11_os_test';
		$.ajax({
			type: 'POST',
			url: AJAX_URL,
			data: $data,
			success: function (output) {
				$body.html(output);
			}
		});

	$data['action'] = 'ab11_os_show_semesters';
	$.ajax({
		type: 'POST',
		url: AJAX_URL,
		data: $data,
		success: function (output) {

			$list_semesters.html(output);

		}
	});



}

/* ==================================================================
*
*   Get URL Variables
*
* -----------------------------------------------------------------*/
function get_URL_vars () {
	//Gather options from URL
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&#]*)/gi, function(m,key,value) {
		$data[key] = value;
	});
}

/* ==================================================================
*
*   Select a Semester
*
* -----------------------------------------------------------------*/
function select_semester(e) {
	e.preventDefault();

	var $text = $(this).text();

	$data['semester_id'] = $(this).data('semester-id');
	$data['career'] = $(this).data('career');

	$data['action'] = 'ab11_os_set_semester';
	$.ajax({
		type: 'POST',
		url: AJAX_URL,
		data: $data,
		success: function (output) {
			$breadcrumbs_schedule.html('<li>' + $text + '</li>');
		}
	});


	
	$data['action'] = 'ab11_os_get_subjects';
	$.ajax({
		type: 'POST',
		url: AJAX_URL,
		data: $data,
		success: function (output) {
			$button_semesters.removeClass('is-hover');
			$button_subjects.removeClass('is-disabled');
			$list_subjects.html(output).hide();
			$button_subjects.addClass('is-hover');
			$list_subjects.delay(500).slideDown();
		}
	});

	$data['action'] = 'ab11_os_get_calendar';
	$.ajax({
		type: 'POST',
		url: AJAX_URL,
		data: $data,
		success: function (output) {
			$container_calendar.html(output);
			$container_cubes.hide();
			$breadcrumbs_schedule.append('<li>' + $text + ' Calendar</li>');

		}
	});
}

/* ==================================================================
*
*   Select a Subject
*
* -----------------------------------------------------------------*/
function select_subject(e) {
	e.preventDefault();
	$data['subject'] = $(this).data('subject');
	$data['action'] = 'ab11_os_get_courses';
	$.ajax({
		type: 'POST',
		url: AJAX_URL,
		data: $data,
		success: function (output) {
			$list_classes.html(output);
			$container_calendar.fadeOut();
			$list_classes.fadeIn().removeClass('hidden');
		}
	});
}

/* ==================================================================
*
*   Hover on tooltip
*
* -----------------------------------------------------------------*/
function tooltip_enter(e){
	var $node = $(e.target);
	var top = $node.position().top;

	$list_subjects.find('div.tooltip').addClass('is-hidden');
	$node.parent().find('.tooltip').css({'top': top}).removeClass('is-hidden');

}

function tooltip_leave(e){

}

});//end jQuery