<?php
/**
* @version 1.0 $
* @package Quiz
* @copyright (C) 2009 Therese Lung
*/
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );
require ( JPATH_SITE .'/includes/hd2/constants.php' );
require ( JPATH_SITE .'/modules/mod_quiz/mod_quiz.html.php' );
?>

<script language="javascript" type="text/javascript">
function processQuiz(form) {
	var flag = false;
	for (var i=0; i < form.question1.length; i++){
		if (form.question1[i].checked) flag = true;
	}
	
	if (flag == false) {
		alert ('<?php echo _HCC_TKG_QUIZ_JS ?>');
		return flag;
	}
	flag = false;
	for (var i=0; i < form.question2.length; i++){
		if (form.question2[i].checked) flag = true;
	}
	if (flag == false) {
		alert ('<?php echo _HCC_TKG_QUIZ_JS ?>');
		return flag;
	}
	flag = false;

	for (var i=0; i < form.question3.length; i++){
		if (form.question3[i].checked) flag = true;
	}
	if (flag == false) {
		alert ('<?php echo _HCC_TKG_QUIZ_JS ?>');
		return flag;
	}
	flag = false;

	for (var i=0; i < form.question4.length; i++){
		if (form.question4[i].checked) flag = true;
	}
	if (flag == false) {
		alert ('<?php echo _HCC_TKG_QUIZ_JS ?>');
		return flag;
	}
	return true;
}
</script>


<?php

// Retrieve page params so we can reconstruct it
global $option;
global $view;
global $itemid;
global $id;

// Retrieve URL parameters
// Joomla
$option = trim( JRequest::getVar(  'option'));
$id = trim( JRequest::getVar(  'id'));
$itemid = trim( JRequest::getVar(  'Itemid'));
$view = trim( JRequest::getVar(  'view', null));
$op = trim( JRequest::getVar(  'op', null));
$errmsg = JRequest::getVar( 'errmsg', '');


switch ($op) {
	case 'score':
	
		$values = array();	// will hold results for values[1..3]
		
		$answers[] = JRequest::getVar( 'question1', 0);
		$answers[] = JRequest::getVar( 'question2', 0);
		$answers[] = JRequest::getVar( 'question3', 0);
		$answers[] = JRequest::getVar( 'question4', 0);
		
		// print_r($answers);
		foreach ( $answers as $key => $answer) {
			if ($answer == 0) {				
				break;
			}
			$values[$answer]++;
		}
		if ($answer != 0) {				
        	HTML_quiz::scoreQuizQuestions( $values);
		}
		else {
		   	HTML_quiz::displayErrorMsg(_HCC_TKG_QUIZ);
	      	HTML_quiz::displayQuizQuestions();
		}
		break;
	case '':
	default:
        HTML_quiz::displayQuizQuestions();
}
?>


