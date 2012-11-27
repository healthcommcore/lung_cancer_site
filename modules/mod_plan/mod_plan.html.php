<?php
require ( JPATH_SITE .'/includes/hd2/strategytips.php' );


class HTML_plan {

// Error message if error after processing previous instance of this page >> Consider separate function to do this?
function displayStatusMsg($statusMsg) {

		// Display error messages if any
		if ($statusMsg != '') {
			echo '<span class="alert">'. $statusMsg . '</span>';
		}
}

function displayPage1Intro($urlopt) {
global $option, $view, $id, $itemid;


?>

<h2>Are you ready to make a Healthy Directions Plan?</h2>
<h2>Or are you ready to make a new plan?</h2>
      <p> It can be hard to make health changes. Everybody can use some support when they work on a new health habit. That&rsquo;s what a Healthy Directions Plan is for! It lets you pick a health goal. Then it helps you decide on specific things you can do to reach that goal. Your Healthy Directions Plan will also show you what might get in the way of your goal, and what you can do about it. </p>
      <p> It will take about 10 minutes to make your Healthy Directions Plan. We will walk you through every step. When you are finished, you can print it out. Then you can put it on your refrigerator or carry it with you to remind you how to reach your goal. Want to get started?</p>
      <a class="hd_button"
	 href="<?php echo "index.php?option=$option&view=$view&id=$id&Itemid=$itemid$urlopt" ?>"><?php echo 'Next' ?></a>
<?php 	
}

//	Form for user to state reason
function displayPage2Reason( $urlopt  ) {

global $planReasons;
global $option, $view, $id, $itemid;
?>
<?php

		echo '<form action="' .JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt ). '" method="post" name="plan1" onSubmit="return processPage2Reason (this,\''. _HCC_PP_ERR_REASON. '\')">'; 
		?>
<h2>Why did you join Healthy Directions?</h2>
      <p>People have many different reasons for making health changes. What&rsquo;s yours? Type in your answer below, or pick a reason from the list. </p>
	  <p>My reason for making health changes: </p>
      <input id="reasontext" name="reasonopt" value="0" checked="checked" type="radio">
      <input name="reason" value="" size="50" maxlength="255" onfocus="checkTextGoal()" type="text"><br />
      <?php
			foreach ($planReasons as $key => $val ) {
				echo '<input type="radio" name="reasonopt" value="' . $key . '">' . $val . '<br>';
			}
		?>
      <br><br />
    <input class="hd_button" value="Next" type="submit">
</form>
<?php
}

function displayPage3Goals($urlopt) {
global $option, $view, $id, $itemid;
global $Plan;

		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt) . '" method="post" name="plan1" >'; 
?>
<h2>What We Recommend for Good Health</h2>
      <p>It&rsquo;s great that you want to make some health changes! Healthy Directions recommends these 4 habits:</p>
      <ul>
        <li>Walk 10,000 or more steps a day. </li>
        <li>Eat 5 to 9 servings of fruits and vegetables a day.</li>
        <li>Eat no more than 3 servings of red meat a week.</li>

<!-- Get rid of Multivitamin for Lung Cancer study: Dave Rothfarb, 8-13-12
        <li>Take a multivitamin every day.</li>
-->
        <li>Do not smoke.</li>
      </ul>
      <p>You may do some of these things already. Healthy Directions can help you work on as many more as you want to! You can even work on changing many health habits at the same time. </p>
      <input type="hidden" name="reason" value="<?php echo $Plan->reason ?>" size="50" maxlength="125">
      <input type="hidden" name="reasonopt" value="<?php echo $Plan->reasonopt ?>" >
      <input class="hd_button" value="Next" type="submit">
</form>
<?php 	
}



//	Form for user to select behaviors
function displayPage4ChooseBeh( $urlopt  ) {
global $Plan;
global $user;
global $option, $view, $id, $itemid;
?>
<?php

		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid&". $urlopt) . '" method="post" name="plan1" onSubmit="return processPage4ChooseBeh (this,\''. _HCC_PP_ERR_HABITS. '\')">'; 
		?>
    <h2>Which health habits do you want to work on?</h2>
      <p>Please click on the health habits you want to start working on. You can pick as many as you want. Remember that it is easier to change more than one habit at a time! <a href="index.php?option=com_content&view=article&id=48&Itemid=0" target="_blank">Learn why here.</a></p>
	  
    <div class="checkindent">
        <input name="groupID[]" value="1" type="checkbox">
        <label for="habits">I want to walk 10,000 or more steps a day.</label>
      </div>
      <div class="checkindent">
        <input name="groupID[]" value="2" type="checkbox">
        <label for="habits">I want to eat 5 to 9 servings of fruits and vegetables a day.</label>
      </div>
      <div class="checkindent">
        <input name="groupID[]" value="3" type="checkbox">
        I want to eat no more than 3 servings of red meat a week.</div>

<!-- Get rid of Multivitamin for Lung Cancer study: Dave Rothfarb, 8-13-12
      <div class="checkindent">
        <input name="groupID[]" value="4" type="checkbox">
        <label for="habits">I want to take a multivitamin every day.</label>
      </div>
-->
      <?php if (!$user->nonSmoker) { ?>
      <div class="checkindent">
        <input name="groupID[]" value="5" type="checkbox">
        <label for="habits">I want to quit smoking.</label>
      </div>
      <?php } ?>
      <input type="hidden" name="reason" value="<?php echo $Plan->reason ?>" size="50" maxlength="125">
      <input type="hidden" name="reasonopt" value="<?php echo $Plan->reasonopt ?>" >
    <br /><br />
	<input class="hd_button" value="Next" type="submit">
</form>
<?php
}


//	Form for user to select support
function displayPage5Support( $urlopt  ) {
global $Plan;
global $supportList;
global $option, $view, $id, $itemid;
?>
<?php

		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt) . '" method="post" name="plan1" onSubmit="return processPage5Support (this,\''. _HCC_PP_ERR_SUPPORT. '\')">'; 
		?>
	<h2>Who can help you work on these health habits?</h2>
      <p>It is easier to make changes when someone supports you. This person can remind you of your goals and help you with things that get in your way. He or she can also cheer you on! </p>
      <p>Plus, we&rsquo;ve got a special Healthy Directions web site just for friends and family members. It&rsquo;s full of tips about how they can help you make healthy changes. </p>
      <p>Pick your best buddy from the list below &#8212; but keep in mind that you should
ask for support from as many people as you can! People who get help from a friend do better with making changes.  Use the <a href="index.php?option=com_content&view=article&id=41&Itemid=50">email tool</a> to invite
people to be your buddies. We&rsquo;ll send them an email with a link to a special web site designed for Healthy Directions buddies.  You can check it out at <a href="http://Buddies.TrackMyChanges.org">Buddies.TrackMyChanges.org</a>.
</p>
      <p>I&rsquo;ll ask for help from my:</p>
	  
	<select name="support" id="support" class="inputbox">
        <option value="" selected="selected">Select</option>
        <?php foreach ($supportList as $support) { ?>
        <option value="<?php echo $support?>"><?php echo $support?></option>
        <?php } ?>
      </select>
   <input type="hidden" name="reason" value="<?php echo $Plan->reason ?>" size="50" maxlength="125">
      <input type="hidden" name="reasonopt" value="<?php echo $Plan->reasonopt ?>" >
      <?php foreach ($Plan->beharray as $behavior) { ?>
      <input name="groupID[]" value="<?php echo $behavior ?>" type="hidden" />
      <?php } ?>
	  <br /><br />
      <input class="hd_button" value="Next" type="submit">
</form>
<?php
}

function displayPage6Skills($urlopt) {
global $option, $view, $id, $itemid;
global $Plan;

		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt) . '" method="post" name="plan1">'; 
		?>
	<h2>Making Changes</h2>
      <p>Did you know that no matter which health habits you&rsquo;re changing, there are ways to help make it easier? We&rsquo;ve listed them here so you can start thinking about them. </p>
      <ul>
      <li><strong>Track your health habits.</strong> Use this web site to enter your health information every day. This lets you see how you&rsquo;re doing. You can also see how close you are getting to your goal!</li>
      <li style="display:block"><strong>Plan.</strong> It&rsquo;s easier to make health changes if you think ahead. </li>
      <li><strong>Get support from friends and family.</strong> Tell people close to you about the health changes you are working on. It really does help!</li>
      <li><strong>Take small steps.</strong> It can be hard to try and reach a goal all at once. Break things into smaller steps, and you&rsquo;ll get there! </li>
      <li><strong>Reward yourself.</strong> Making a healthy change deserves a reward! When you reach a goal, do something nice for yourself. </li>
      </ul>
      <p>We&rsquo;ll show you how do all of this and more in your Healthy Directions Plan.</p>
      <?php foreach ($Plan->beharray as $behavior) { ?>
      <input name="groupID[]" value="<?php echo $behavior ?>" type="hidden" />
      <?php } ?>
	  <br /><br />
      <input class="hd_button" value="Next" type="submit">
</form>
<?php 	
}

function displayPage7BehSummary($urlopt) {
global $option, $view, $id, $itemid;
global $Plan;
global $planBehSpecs;
		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt) . '" method="post" name="plan1">'; 
		?>
	<h2>Health Habits You&rsquo;re Working On</h2>
      <p>You said:</p>
      <ul>
        <?php foreach ($Plan->beharray as $behavior) { 
			echo '<li>'. $planBehSpecs[$behavior]['goal'] .'</li>' ;
		 } ?>
        <?php foreach ($Plan->beharray as $behavior) { ?>
        <input name="groupID[]" value="<?php echo $behavior ?>" type="hidden" />
        <?php } ?>
      </ul>
      <p>Click &#8220;next&#8221; to start your Healthy Directions plan.</p>
      <input class="hd_button" value="Next" type="submit">
</form>
<?php 	
}
 

function displayPage8BehStrategy($urlopt) {
global $option, $view, $id, $itemid;
global $Plan, $strategySpecs, $behaviorSpecs;
		echo '<form action="' .JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid". $urlopt) . '" method="post" name="plan1" onSubmit="return processPage8BehStrategy (this,\''. _HCC_PP_ERR_2STR. '\')">'; 
		$behavior = $Plan->beharray[0];
		
	
	?>
      <h2>
        <?php 			echo $behaviorSpecs[$behavior]['sname'] ; ?>
      </h2>
      <h3>Small Steps I Can Take to Reach my Goal</h3>
      <p>Small steps add up to big changes. Pick 2 things below that will help you reach your goal. Or pick one thing, and then write your own idea in the box.</p>
      <!-- current behavior is first in groupID (error if none) -->
      <!-- display list of strategies -->
      <ul>
        <?php 
			foreach ($strategySpecs[$behavior] as $strategyID => $strategy) {
				echo '<div class="checkindent"><input type="checkbox" name="' . 'strategyID[]'. '" value="'.
				$strategyID . '"/>';
				echo '<label for="strategies" >' . $strategy . '</label></div>';
			}
		 ?>
        <div class="checkindent">
          <input type="checkbox" id="strategycheck" name="' . 'strategyID[]'. '" value="0" onclick="processPage8BehStrategy()"/>
          My Idea:
          <input type="text" id="strategytext" name="strategy" value="" size="45" maxlength="255" onfocus="processPage8BehStrategy()">
        </div>
        <?php foreach ($Plan->beharray as $behavior) { ?>
        <input name="groupID[]" value="<?php echo $behavior ?>" type="hidden" />
        <?php } ?>
      </ul>
      <input class="hd_button" value="Next" type="submit">
</form>
<?php 	
}

// Determine whether or not we are wrapping up the individual behaviors or not, which will define
// The submmit button text and form action  
function displayPage8BehBarrier() {
global $option, $view, $id, $itemid;
global $Plan, $behaviorSpecs, $barrierSpecs;

	if (sizeof($Plan->beharray) > 1)
		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid&op=step7a") . '" method="post" name="plan1" onSubmit="return processPage8BehBarrier (this,\''. _HCC_PP_ERR_2BAR. '\')">'; 
	else
		echo '<form action="' . JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid&op=view") . '" method="post" name="plan1" onSubmit="return processPage8BehBarrier (this,\''. _HCC_PP_ERR_2BAR. '\')">'; 
		
		$behavior = $Plan->beharray[0];
		
	?>
	<h2>
        <?php 			echo $behaviorSpecs[$behavior]['sname'] ; ?>
      </h2>
      <h3>Some Things That Might Get in the Way</h3>
      <p>Even if we have plans to change, sometimes things can get in our way. Pick one or 2 things that might get in the way of your goal. </p>
      <ul>
        <!-- display list of barriers -->
        <?php

	foreach ($barrierSpecs[$behavior] as $barrierID => $barrier) {
		echo '<div class="checkindent"><input type="checkbox" name="barrierID[]" id="barriers" value="'. $barrierID .' "/>';
		echo '<label for="barriers">' . $barrier . '</label></div>';
	}


	?>
        <!-- php code to display beh selections -->
      </ul>
      <input type="hidden" name="processbarrier" value="1" size="35" maxlength="125">
      <input type="hidden" name="strategy" value="<?php echo $Plan->strategy ?>" size="35" maxlength="125">
      <input type="hidden" name="strategy1" value="<?php echo $Plan->strategy1 ?>" size="35" maxlength="125">
      <input type="hidden" name="strategy2" value="<?php echo $Plan->strategy2 ?>" size="35" maxlength="125">
      <!-- Remove first  behavior from array and pass remaining => this should be done AFTER saving plan info -->
      <?php foreach ($Plan->beharray as $behavior) { ?>
      <input name="groupID[]" value="<?php echo $behavior ?>" type="hidden" />
      <?php } ?>
      <!-- Depending on whether this is the last behavior or not, go to next behavior or wrap up plan -->
      <?php if (sizeof($Plan->beharray) > 1) { ?>
      <input class="hd_button" value="Go to the next health habit I want to change" type="submit">
      <?php } else { ?>
      <input class="hd_button" value="Go to my Healthy Directions Plan" type="submit">
      <?php }  ?>
</form>
<?php 	
}

function displayPlan( $userPlans, $new) {
global $planBehSpecs, $planReasons, $barrierSpecs, $strategySpecs;
global $option, $view, $id, $itemid;
global $tipSpecs, $supportList;
global $Plan;
	
	?>
<div class="indent">
<a  class="hd_button" onClick="javascript:window.print()">Print this Plan</a>
<?php if ($new) { ?>
<a class="hd_button" style="float:left"
		 href="<?php echo "index.php?option=$option&view=$view&id=$id&Itemid=$itemid&op=step1" ?>">Make a new Plan</a>
<?php } ?>
</div>
<br />
<div class="indent" style="clear:both">

<h2>My Healthy Directions Plan</h2>
  <p> Your Healthy Directions Plan is ready, and it&rsquo;s time to make some healthy changes! See how this plan works for you and come back to make a new plan any time.</p>
  
<div class="important-green"><span class="important-title-green">Why you want to make changes</span>You said:
  
  <?php
	if ($Plan->reasonopt != 0) {
		 echo '<h3>'. $planReasons[$Plan->reasonopt] . '</h3>';
	}
	else { 
		if ($Plan->reason != '') {
			echo '<h3>'. $Plan->reason. '</h3>';
		}
	}
	?>
</div>

<div class="important-green"><span class="important-title-green">Who can help you reach your goals</span>You said:

<?php		 echo '<h3>'. $Plan->support . '</h3>'; ?>

<p>People who get help from a friend do better with making changes. You can invite your friends and family to help you right now!</p>
<p><a target="_blank" href="index.php?option=com_content&amp;view=article&amp;id=41&amp;Itemid=50" title="">Invite them</a> to <a href="http://buddies.trackmychanges.org" title="" target="_blank">Buddies.TrackMyChanges.org</a></p>

 </div>
 
<div class="important-green"><span class="important-title-green">What you'll work on</span>You said:
  <ul class="plan">
    <?php 
		foreach ($userPlans as $plan) {
			echo "<li>". $planBehSpecs[$plan->behaviorID]['goal'] ."</li>"  ;
		 } 
		 
		 ?>
  </ul>
 </div>

<div class="important-green"><span class="important-title-green">Some small steps</span>You said:
  
  <ul class="plan">
    <?php 
		foreach ($userPlans as $plan) { ?>
    <li><?php echo 	($plan->strategy != '') ? $plan->strategy: $strategySpecs[$plan->behaviorID][$plan->strategy1] ?></li>
    <li><?php echo ($plan->strategy != '') ? $strategySpecs[$plan->behaviorID][$plan->strategy1]: $strategySpecs[$plan->behaviorID][$plan->strategy2] ?></li>
    <?php  } 
		 
		 ?>
  </ul>
 </div>

<div class="important-green"><span class="important-title-green">Things that might get in the way</span>

  <!-- Loop through list of behaviors -->
  <?php 	foreach ($userPlans as $plan) { ?>
  <h3><?php echo $barrierSpecs[$plan->behaviorID][$plan->barrier1] ?></h3>
  <p>Try these!</p>
  <!-- Display tips for this barrier -->
  <?php
		if ( isset($tipSpecs[$plan->behaviorID][$plan->barrier1]) ) {
			echo '<ul>';
			foreach ($tipSpecs[$plan->behaviorID][$plan->barrier1] as $tip)
				echo '<li>'. $tip. '</li>';
			echo '</ul>';
		}
		// If a second barrier is specified
		if (($plan->barrier2 > 0 ) &&  isset($tipSpecs[$plan->behaviorID][$plan->barrier2]) ) {
			?>
  <h3><?php	echo $barrierSpecs[$plan->behaviorID][$plan->barrier2]; ?></h3>
  <p>Try these!</p>
  <?php
				echo '<ul>';
				foreach ($tipSpecs[$plan->behaviorID][$plan->barrier2] as $tip)
					echo '<li>'. $tip. '</li>';
				echo '</ul>';
		
		}
	} ?>
 </div>
	
<a  class="hd_button" onClick="javascript:window.print()">Print this Plan</a>
<?php if ($new) { ?>
<a class="hd_button" style="float:left"
		 href="<?php echo JRoute::_("index.php?option=$option&view=$view&id=$id&Itemid=$itemid&op=step1") ?>">Make a new Plan</a>
<?php } ?>
</div>
<?php
}

 
}

?>
