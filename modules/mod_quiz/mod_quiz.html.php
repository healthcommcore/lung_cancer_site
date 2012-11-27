<?php
/**
* @version 1.0 $
* @package Quiz
* @copyright (C) 2009 Therese Lung
*/
 
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access.' );


class HTML_quiz {

	function displayErrorMsg($errmsg) {
		echo '<span class="alert">'. $errmsg. '</span>';
	}

	function displayQuizQuestions () {
	global $option;
	global $view;
	global $itemid, $id;

		?>

<div>
  <p class="contentheading">Buddy Quiz</p>
  <p>Everybody has their own style when it comes to getting help with changes. Take this quiz to find out what yours is! Click on the button that matches what you would do in these situations. </p>
  <?php
		
	echo '<form action="'. JRoute::_('index.php?option='. $option. '&view='. $view.'&op=score&id='.$id. '&Itemid='. $itemid) .'" method="post" name="quiz" onSubmit="return processQuiz (this)">'; 
        ?>
  <h4>1. You decide that you want to become more active. You've been thinking about starting to walk regularly every evening. You:</h4>
  <input name="question1" value="1" type="radio">
  ask a family member or neighbor if she will watch your kids so you can walk<br>
  <input name="question1" value="2" type="radio">
  are afraid that people you know will laugh at you if you walk<br>
  <input name="question1" value="3" type="radio">
  plan to start walking on your own soon
  <h4>2. You really want to try cooking new recipes that have more vegetables and less red meat. You:</h4>
  <input name="question2" value="1" type="radio">
  ask your friends to pick out a few recipes. Then you all cook them together. <br>
  <input name="question2" value="2" type="radio">
  worry that your family members won't try new foods<br>
  <input name="question2" value="3" type="radio">
  cook separate meals for yourself and give your family the same old thing
  <h4>3.	You just came from the doctor's office. Your blood pressure is a little bit high and your doctor wants you to try and eat better. You:</h4>
  <input name="question3" value="1" type="radio">
  tell a friend or family member what the doctor said<br>
  <input name="question3" value="2" type="radio">
  worry that people will people will find out and bother you about it<br>
  <input name="question3" value="3" type="radio">
  decide to deal with it by yourself
  <h4>4.	You've made some healthy changes in your life. Still, you sometimes find it hard to stick with your health goals. You:</h4>
  <input name="question4" value="1" type="radio">
  ask a co-worker, friend, or neighbor to help you<br>
  <input name="question4" value="2" type="radio">
  think about giving up<br>
  <input name="question4" value="3" type="radio">
  just keep trying by yourself
  <p></p>
  <?php
			echo '<input class="hd_button" type="submit" value="Submit">';
		
?>
</div>
<?php	
	}
	
	function scoreQuizQuestions ( $values) {
	
	?>
<p class="contentheading">Buddy Quiz Score</p>
<p>
  <?php 
		// Totals
		$top1 = $top2 = 0;

		// Handle special 2 x 2 cases first
		//	2 1's and 2 2's
		//	2 1's and 2 3's
		//	2 2's and 2 3's
		if ( ($values[1] == 2) && ($values[2] == 2)) {
							?>
<p><b>Sometimes you like to get support, but other times it can be hard to ask for help. </b>Maybe you only like to reach out to others in some situations. Although this is OK, remember that health habits are easier to change if you include family and friends. When you&rsquo;re feeling unsure about asking for help, try taking a few small steps. You can: </p>
<ul>
  <li>invite a friend over for dinner to try a new, healthy recipe</li>
  <li>ask a co-worker to walk with you on break</li>
  <li>ask a few people to visit <a href="http://buddies.trackmychanges.org" target="_blank">buddies.trackmychanges.org</a>, where they&rsquo;ll learn how they can help you make changes </li>
</ul>
<?php
		
		} 
		else if ( ($values[1] == 2) && ($values[3] == 2)) {
							?>
<p><b>Sometimes you feel good about getting support. Other times, you want to go it alone. </b>Even though you have a good support system, maybe there have been times where you&rsquo;ve done things on your own. But remember that health habits are always easier to change if you include your family and friends. Take a few small steps to get support from others. You can: </p>
<ul>
  <li>invite a friend over for dinner to try a new, healthy recipe</li>
  <li>ask a co-worker to walk with you on break</li>
  <li>ask a few people to visit <a href="http://buddies.trackmychanges.org">buddies.trackmychanges.org</a>, where they&rsquo;ll learn how they can help you make changes &ndash; on your terms</li>
</ul
							>
<?php
		} 
		
		else if ( ($values[2] == 2) && ($values[3] == 2)) {
							?>
<p><b>Sometimes it&rsquo;s hard to ask for help, so you may feel it&rsquo;s easier to do it on your own. </b>Maybe it&rsquo;s hard to reach out to friends and family for help, and doing it alone seems like the way to go. But remember that health habits are easier to change if you include your family and friends. Take a few small steps to get support from others. You can:</p>
<ul>
  <li>invite a friend over for dinner to try a new, healthy recipe</li>
  <li>ask a co-worker to walk with you on break</li>
  <li>ask a few people to visit <a href="http://buddies.trackmychanges.org">buddies.trackmychanges.org</a>, where they&rsquo;ll learn how they can help you make changes &ndash; on your terms</li>
</ul>
<?php
		} 

		else {
				
		foreach ($values as $response => $quantity) {
				// If any = 0, then 2 sets of 2
				if ($quantity == 0) {
					?>
<p>You circled two sets</p>
<?php
					break;
				}
				if ($quantity >= 2) {
					switch ($response) {	
	
						case  1: 
							?>
<p><strong>You feel good about getting support!</strong> You may have even seen first-hand that it&rsquo;s easier to make health changes when you have help. Now think about new ways you and your friends and family can help one another. Here are a few ideas:</p>
<ul>
  <li> host a neighborhood block party with active games and healthy, tasty snacks </li>
  <li>start a walking club at work</li>
  <li>ask friends and family to visit <a href="http://buddies.trackmychanges.org" target="_blank">buddies.trackmychanges.org</a> for even more ideas</li>
</ul>
<?php
							break;
											
						case  2: 
							?>
<p><strong>Sometimes it&rsquo;s hard for you to ask for help from friends and family.</strong> Maybe you&rsquo;re worried about what they will think, or you don&rsquo;t know if they are ready to help. Here are a few ways to get others involved:</p>
<ul>
  <li> explain to your friends and family how important your health goals are to you</li>
  <li>ask someone to walk with you after dinner &ndash; it&rsquo;s a great way to unwind</li>
  <li>take turns making healthy meals with friends</li>
  <li>ask friends and family to visit <a href="http://buddies.trackmychanges.org" target="_blank">buddies.trackmychanges.org</a></li>
</ul>
<?php
							break;
		
						case  3: 
							?>
<p><strong>It&rsquo;s important for you to do it on your own.</strong> When you want to make a change, you do it alone. But remember that health habits are easier to change if you include your family and friends. Take a few small steps to get support from others. You can:</p>
<ul>
  <li>invite a friend over for dinner to try a new, healthy recipe</li>
  <li>ask a co-worker to walk with you on break</li>
  <li>ask a few people to visit <a href="http://buddies.trackmychanges.org" target="_blank">buddies.trackmychanges.org</a>, where they&rsquo;ll learn how they can help you make health changes &ndash; on your terms</li>
</ul>
<?php
							break;
						default:
					}					
					break;
				}
				
		}	// end foreach	
		
		} // end else
		
  
  ?>
</p>
<?php
	}	// end function
} // end class
?>
