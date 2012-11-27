<?php

// Static information about the 15 tracked behaviors are stored in this
//	array (faster than querying database). Feedback messages and action
//	planning data for each behavior are defined in separate arrays
//	(strategytips.php, feedback.php)
//	
//	The  array key values range from 1-5, with 6 for the MRF historical chart
//  These behavior IDs identify the behavior
//	being tracked .
//  These values are defined in a specific order so that they can be
//	used in ascending order to determine the display order of
//	any list of behaviors (tracked or not) consistently.
//
//	Each behavior's 'attributes' is represented by key/value pairs.
//	These must exist for each defined behavior in the array
//	constants are defined where possible. Strings are defined in
//	strings.php (to facilitate translation)
//		name: full descriptive text associated with behavior
//		sname: short descriptive text
//		labels: number of labels on vertical axis
//		minVal: validate tracking data entry. Not really needed, since
//			0 for all behaviors. 
//		maxVal: validate tracking data entry.
//		maxChartVal: NOT used to validate data entry, but to calculate
//			chart's Y-axis segment value.  
//		entrytype: one of three types of data entry used in the form
//			radio button, drop-down list, or text entry
//		homeinfo: text recommendation shown on tracking page summary
//		formquest: text for question used in purple tracking data entry form
//		options: list of values displayed for radio button and drop-down lists,
//			in order of display. NULL for text entry.
//		step2item:
//		contentid: Joomla page id for the corresponding HH content item
//		contentitem: Joomla page itemid for the corresponding HH content item
//		feedbacktype: one of 3 types of feedback  - Yes, No, numeric
//			to determine data processing
//		weekavgtype: one of 3 types of weekly feedback classes - Yes, No, numeric
//			so code can determine which average to calculate
//		weekavgdec: number of decimal places for weekly avg display
//		image: corresponding image filename
//		homeavgtext: text for weekly average shown on tracking page summary
//		weekchart: weekly graph image filename
//		longchart: 26-week graph image filename
//		dailygoal: desired daily goal used by chart code to determine whether tracking data
//			met goal
//		weeklygoal: desired weekly total goal used by MRF code 
//		trendgoal: trend weekly goal used by for calculating trend feedback levels
//		weeklyMRFmin: desired weekly total minimum used by MRF code 
//		goalcompare: operator used in comparison with goal value (>, <, =)
//		helplinktxt: text that appears on the link. NULL if no help link
//		helpitem: Joomla page itemid for help
//		helpid: Joomla page id for the help page
//		dfeedback: daily feedback array, indexed by feedback level
//		wfeedback: weekly feedback array, indexed by feedback level
//		tfeedback: trend feedback array, indexed by feedback level

//
//	Special case -MRF history


 
global $behaviorSpecs;

$behaviorSpecs = array(
	// Numbered by order of appearance
	// NUTRITION 
	1 => array(		// walk
		'name' => 'Walking and Physical Activity',
		'sname' => 'Physical Activity',
		'labels' => 15,		
		'minVal' => 0,
		'maxVal' => 20000,
		'maxChartVal' => 14000,
		'entrytype' => _HCC_TRACKING_IN_NUMERIC,
		'homeinfo' => 'We recommend that you <span class="highlight-bold">walk 10,000 or more steps every day.</span> ',
		'formquest' => 'How many steps did you walk <b>with</b> your pedometer?',
		'options' => null,	
		'step2item' => 'steps',
		'contentid' => 17,
		'contentitem' => 24,		
		'feedbacktype' => _HCC_TRACKING_FEED_NUMERIC,
		'image' => 'PA_icon',
		'weekavgtype' => _HCC_TRACKING_AVG_DAILY,
		'weekavgdec' => 0,
		'weekchart' => 'PA_weekly.gif',
		'longchart' => 'PA_history.gif',
		'dailygoal' => 10000,		
		'weeklygoal' => 70000,		
		'trendgoal' => 10000,		
		'weeklyMRFmin' => 23300,		
		'goalcompare' => '>',
		// daily feedback, one random out of 3
		'dfeedback' => array(
			1 => array( 0, array(
				'Getting more physical activity can take time. Use our <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> and find ways to <span class="highlight-bold">add activity</span> to your day tomorrow.',
				'There are lots of ways to add physical activity to your days. Remember that <span class="highlight-bold">every little bit counts!</span> Check out the <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> to learn more.',
				'If you can&rsquo;t get to the goal right away, it&rsquo;s OK. Just try to add some physical activity each day, whenever you can! The <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> can get you <span class="highlight-bold">closer to the goal.</span>',
				)),
			2 => array( 2500, array(
				'You didn&rsquo;t reach the goal today. Are there ways you could <span class="highlight-bold">get more physical activity</span> tomorrow? Our <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> can help.',
				'Remember, lots of small changes can add up to big change. Try to get some more physical activity tomorrow. <span class="highlight-bold">Try walking with a friend!</span> Or visit the <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> for other ideas. ',
				'Reaching a goal can take time. Can you think of ways to add a bit more physical activity   each day, so you&rsquo;ll get <span class="highlight-bold">closer to the goal?</span> Visit the <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> for ideas!',
				)),
			3 => array( 5000, array(
				'You&rsquo;re doing OK with physical activity, but would benefit from doing a bit more. <span class="highlight-bold">Try to get more tomorrow!</span> Check out the <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> for tips. ',
				'You seem to be getting some physical activity in your day. Can you think of <span class="highlight-bold">ways to add more?</span> The <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> are full of ideas!',
				'You&rsquo;re doing all right with physical activity, but it would be great to get more. Try to <span class="highlight-bold">get a bit more physical activity tomorrow</span> &#8212; walk with a friend! Or visit the <a href="index.php?option=com_content&view=article&id=17&Itemid=7">Healthy Tips</a> for other ideas. ',
				)),
			4 => array( 7500, array(
				'You&rsquo;re getting very close! <span class="highlight-bold">Keep at it and try for more steps</span> or a few more minutes of   activity tomorrow.',
			'Add just a bit more physical activity tomorrow and you&rsquo;ll reach the goal. <span class="highlight-bold">You&rsquo;re almost there!</span>',
				'The goal is <span class="highlight-bold">within your reach.</span> Try to get a few more minutes of physical activity or steps tomorrow and you&rsquo;ll be there! Why not try walking with a friend?',
				)),
			5 => array( 10000, array(
				'Awesome! <span class="highlight-bold">You met or passed the goal</span> for physical activity. You&rsquo;re doing great. ',
				'<span class="highlight-bold">You reached or passed the goal</span> for physical activity. Keep it up. Way to go!',
				'You met the goal for physical activity, or even passed it &#8212; <span class="highlight-bold">this is super news!</span> ',
				)),
		),	
		// weekly feedback	
		'wfeedback' => array(
			1 => array( 0, '<li>It looks like you walked an average of <span class="highlight-bold">%s steps a day</span> over the last <span class="highlight-bold">7 days</span>, or about the same amount of other types of physical activity.</li> <li>Are there ways you could <a href="index.php?option=com_content&view=article&id=17&Itemid=7">get more steps</a>? Remember that <span class="highlight-bold">every little bit</span> counts!</li>'),
			2 => array( 2500, '<li>It looks like you walked an average of <span class="highlight-bold">%s steps a day</span> over the last <span class="highlight-bold">7 days</span>, or about the same amount of other types of physical activity. </li> <li>Can you think of ways to <span class="highlight-bold">add more steps</span> over the next few days?</li>' ),
			3 => array( 5000, '<li>It looks like you walked an average of <span class="highlight-bold">%s steps a day</span> over the last <span class="highlight-bold">7 days</span>, or about the same amount of other types of physical activity.</li> <li> <span class="highlight-bold">You&rsquo;re getting there!</span> Try for more steps each day next week.</li>'),
			4 => array( 7500, '<li>It looks like you walked an average of <span class="highlight-bold">%s steps a day</span> over the last <span class="highlight-bold">7 days</span>, or about the same amount of other types of physical activity.</li> <li> <span class="highlight-bold">You&rsquo;re nearing the goal!</span> Think about ways to get more steps each day next week.</li>'),
			5 => array( 10000, '<li>It looks like you walked an average of <span class="highlight-bold">%s steps a day</span> over the last <span class="highlight-bold">7 days</span>, or about the same amount of other types of physical activity.</li> <li><span class="highlight-bold">This is great!</span> Keep going!</li>'),
		),	
		// Trend feedback	
		'tfeedback' => array(
			1 =>  '<li>You walked <span class="highlight-bold">much less</span> than the week before, when you averaged <span class="highlight-bold">%s steps a day,</span> or about the same amount of other types of physical activity. </li> <li>Everyone has times when they aren&rsquo;t as active as they&rsquo;d like. Try to <span class="highlight-bold">work toward getting active</span> again!</li>',
			2 =>  '<li><span class="highlight-bold">You walked less</span> than the week before, when you averaged <span class="highlight-bold">%s steps a day,</span> or about the same amount of other types of physical activity. </li> <li>Can you think of ways to bring that daily number <span class="highlight-bold">back up again?</span></li>',
			3 =>  '<li>You walked <span class="highlight-bold">about the same about as the week</span> before, when you averaged <span class="highlight-bold">%s steps a day,</span> or about the same amount of other types of physical activity.</li> <li> <span class="highlight-bold">Keep working</span> toward the goal!</li>',
			4 =>  '<li><span class="highlight-bold">You walked more</span> than the week before, when you averaged <span class="highlight-bold">%s steps a day,</span> or about the same amount of other types of physical activity. </li> <li><span class="highlight-bold">Keep it up!</span></li>',
			5 =>  '<li><span class="highlight-bold">You&rsquo;re doing much better</span> than the week before, when you averaged <span class="highlight-bold">%s steps a day,</span> or about the same amount of other types of physical activity. </li> <li><span class="highlight-bold">Great job!</span></li>',
		),		

		'helplinktxt' => 'Why steps AND activity?',
		'helpitem' => 96,
		'helpid' => 78,
		'helplinktxt2' => 'What are moderate and vigorous types of physical activity?',
		'helpitem2' => 44,
		'helpid2' => 39,
		),
	2 => array(		// fruits
		'name' => 'Eating Fruits and Vegetables',
		'sname' => 'Fruits and Vegetables',
		'labels' => 11,		
		'minVal' => 0,
		'maxVal' => 10,
		'maxChartVal' => 10,
		'entrytype' => _HCC_TRACKING_IN_LIST,
		'homeinfo' => 'We recommend that you <span class="highlight-bold">eat 5 to 9 servings of fruits and vegetables a day.</span>',
		'formquest' => 'How many servings of fruits and vegetables did you eat?',
		'options' => array(0,1,2,3,4,5,6,7,8,9,'10+' => 10 ),
		'step2item' => 'fruit(s) and vegetable(s)',
		'contentid' => 9,
		'contentitem' => 25,
		'feedbacktype' => _HCC_TRACKING_FEED_NUMERIC,
		'weekavgtype' => _HCC_TRACKING_AVG_DAILY,
 		'weekavgdec' => 1,
		'image' => 'FV_icon',
		'weekchart' => 'FV_weekly.gif',
		'longchart' => 'FV_history.gif',
		'dailygoal' => 5,
		'weeklygoal' => 35,		
		'trendgoal' => 5,		
		'weeklyMRFmin' => 12,		
		'goalcompare' => '>',	
		'dfeedback' => array(
			1 => array( 0, array(
				'It looks like you&rsquo;re having a hard time eating fruits and vegetables. <span class="highlight-bold">Learn how to get more</span> with our great <a href="index.php?option=com_content&view=article&id=32&Itemid=9">recipes</a>. ',
				'There are many delicious ways to <span class="highlight-bold">add fruits and vegetables</span> to your day. Learn how with our <a href="index.php?option=com_content&view=article&id=9&Itemid=25">Healthy Tips</a>.',
				'Don&rsquo;t give up &#8212; change can take time. <span class="highlight-bold">Every little bit counts</span> as you&rsquo;re working toward the goal. Get some <a href="index.php?option=com_content&view=article&id=32&Itemid=9">recipe</a> ideas for tomorrow!',
				)),
			2 => array( 1, array(
				'You&rsquo;re eating some fruits and vegetables, but more would be better. Try for a few <span class="highlight-bold">more servings tomorrow</span> &#8212; check out our <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe section</a> for tasty ideas.',
				'Keep at it! <span class="highlight-bold">Changes take time and planning.</span> Can you think of a way to eat more fruits and vegetables tomorrow? Visit the <a href="index.php?option=com_content&view=article&id=9&Itemid=25">Healthy Tips</a> to learn how. ',
				'Try to get a little closer to the goal tomorrow. Even <span class="highlight-bold">one more serving</span> will help. Our <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe section</a> will help you get there!',
				)),
			3 => array( 3, array(
				'You&rsquo;re getting really close to the goal! Can you add <span class="highlight-bold">one or two more servings</span> of fruit and vegetables tomorrow? Visit the <a href="index.php?option=com_content&view=article&id=9&Itemid=25">Healthy Tips</a> to learn how.',
				'Think about ways to get more fruits and vegetables into your day tomorrow. Check out the <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe section</a> for ideas. <span class="highlight-bold">You&rsquo;re getting there!</span> ',
				'<span class="highlight-bold">You&rsquo;re so close to the goal!</span> Bring it on home by biting into more fruits and vegetables tomorrow! Visit the <a href="index.php?option=com_content&view=article&id=9&Itemid=25">Healthy Tips</a> for tips.',
				)),
			4 => array( 5, array(
				'<span class="highlight-bold">You reached the goal! Excellent work.</span> Remember that when it comes to fruit and vegetables, more is always better. ',
				'<span class="highlight-bold">Congrats &#8212; you hit the goal!</span> This is awesome news. Try for even more servings tomorrow.',
				'<span class="highlight-bold">It looks like you reached the goal &#8212; this is super!</span> More is always better when you&rsquo;re eating fruits and vegetables.',
				)),
			5 => array( 6, array(
				'This is great! <span class="highlight-bold">You reached the goal.</span> Keep up the good work. Try for even more servings tomorrow. ',
				'<span class="highlight-bold">You&rsquo;ve hit the goal. Super!</span> Keep it up. More is always better when it comes to fruits and vegetables. ',
				'Give yourself a hand &#8212; <span class="highlight-bold">you met the goal</span> for fruits and vegetables! Try for one or two more servings tomorrow.',
				)),
		),		
		'wfeedback' => array(
			1 => array( 0, '<li>It looks like you ate an average of <span class="highlight-bold">%s servings</span> of fruits and vegetables a day over the last <span class="highlight-bold">7 days.</span></li> <li> <span class="highlight-bold">Don&rsquo;t be discouraged</span> &#8212; there are many easy ways to <a href="index.php?option=com_content&view=article&id=9&Itemid=25">add fruits and vegetables</a> to your days.</li>'),
			2 => array( 1, '<li>It looks like you ate an average of <span class="highlight-bold">%s servings</span> of fruits and vegetables a day over the last <span class="highlight-bold">7 days</span>. </li> <li>Don&rsquo;t give up on the goal! <span class="highlight-bold">Every little bit counts</span>, so think about ways to get more fruits and vegetables in your day.</li>'),
			3 => array( 3, '<li>It looks like you ate an average of <span class="highlight-bold">%s servings</span> of fruits and vegetables a day over the last <span class="highlight-bold">7 days.</span></li> <li> <span class="highlight-bold">You&rsquo;re almost at the goal!</span> Try for one or two more fruits and vegetables a day over the next week.</li>'),
			4 => array( 5, '<li>It looks like you ate an average of <span class="highlight-bold">%s servings</span> of fruits and vegetables a day over the last <span class="highlight-bold">7 days.</span></li> <li> <span class="highlight-bold">You met the goal</span> &#8212; that&rsquo;s great news! Keep going with this healthy habit.</li>'),
			5 => array( 6, '<li>It looks like you ate an average of <span class="highlight-bold">%s servings</span> of fruits and vegetables a day over the last <span class="highlight-bold">7 days.</span></li> <li> <span class="highlight-bold">You went above the goal!</span> Congratulations &#8212; and keep up the good work!</li>'),
		),		
		// Trend feedback	
		'tfeedback' => array(
			1 =>  '<li>That&rsquo;s <span class="highlight-bold">not as good as</span> last week, when you ate an average of <span class="highlight-bold">%s servings</span> a day.</li> <li> <span class="highlight-bold">Don&rsquo;t give up</span> &#8212; changes take time!</li> ',
			2 =>  '<li>That&rsquo;s <span class="highlight-bold">not quite as good</span> as last week, when you ate an average of <span class="highlight-bold">%s servings</span> a day.</li> <li> Try to get <span class="highlight-bold">more fruit and vegetables</span> back in your days!</li>',
			3 =>  '<li>That&rsquo;s <span class="highlight-bold">about the same</span> as the week before, when you ate an average of <span class="highlight-bold">%s servings</span> a day.</li> <li> Keep working toward the goal &#8212; small changes can make a <span class="highlight-bold">big difference!</span></li>',
			4 =>  '<li>That&rsquo;s <span class="highlight-bold">more than</span> the week before, when you ate an average of <span class="highlight-bold">%s servings</span> a day.</li> <li> <span class="highlight-bold">Keep it up!</span></li>',
			5 =>  '<li>That&rsquo;s <span class="highlight-bold">better than</span> the week before, when you ate an average of <span class="highlight-bold">%s servings</span> a day.</li> <li> <span class="highlight-bold">You&rsquo;re doing great!</span></li>',
		),		
		'helplinktxt' => null,
		'helpitem' => 0,
		'helpid' => 0,
	),
	3 => array(		// rm
		'name' => 'Eating Less Red Meat',
		'sname' => 'Red Meat',
		'labels' => 13,		
		'minVal' => 0,
		'maxVal' => 6,
		'maxChartVal' => 6,
		'entrytype' => _HCC_TRACKING_IN_LIST,
		'homeinfo' => 'We recommend that you eat <span class="highlight-bold">no more than 3 servings of red meat a week.</span>',
		'formquest' => 'How many servings of red meat did you eat?',
		'options' => array(0,1,2,3,4,5,'6+' => 6),
		'step2item' => 'serving(s) of red meat',
		'contentid' => 25,
		'contentitem' => 26,
		'feedbacktype' => _HCC_TRACKING_FEED_YES,
		'weekavgtype' => _HCC_TRACKING_AVG_TOTAL,
 		'weekavgdec' => 1,
		'image' => 'RM_icon',
		'weekchart' => 'RM_weekly.gif',
		'longchart' => 'RM_history.gif',
		'dailygoal' => 0.43,
		'weeklygoal' => 70000,		
		'trendgoal' => 14,		
		'weeklyMRFmin' => 23300,		
		'weeklygoal' => 3,		
		'weeklyMRFmin' => 9,		
		'goalcompare' => '<',
		'dfeedback' => array(
			1 => array( 0, array(
				'<span class="highlight-bold">Way to go!</span> You didn&rsquo;t eat any red meat today. Try to keep up this healthy habit, so you won&rsquo;t eat more than 3 servings by the end of the week.  ',
				'You didn&rsquo;t eat any red meat today. Keep going with this healthy habit, so by week&rsquo;s end you won&rsquo;t have eaten more than 3 servings. <span class="highlight-bold">Give yourself a hand!</span>',
				'<span class="highlight-bold">Awesome! When it comes to red meat, less is best.</span> You didn&rsquo;t eat any today, so you&rsquo;re on your way to staying under the recommendation of no more than 3 servings a week. ',
				)),
			2 => array( 1, array(
				'When it comes to red meat, remember that less is best. <span class="highlight-bold">Keep an eye</span> on how much you eat, so you won&rsquo;t eat more than 3 servings by week&rsquo;s end.',
				'Remember that <span class="highlight-bold">less red meat is best.</span> Watch how much you eat, so you can meet the goal of no more than 3 servings of red meat a week. ',
				'<span class="highlight-bold">Less red meat is best.</span> Pay attention to how much you eat, so you can meet the goal of no more than 3 servings of red meat a week. ',
				)),
			3 => array( 2, array(
				'<span class="highlight-bold">Be careful</span> &#8212; you&rsquo;ve almost reached 3 servings for the week.',
				'Proceed with caution, because <span class="highlight-bold">you&rsquo;re getting close</span> to 3 servings for the week.',
				'Be careful, because <span class="highlight-bold">you&rsquo;ve nearly reached 3 servings</span> for the week.',
				)),
			4 => array( 3, array(
				'Be careful! You&rsquo;ve eaten the weekly recommendation for red meat in just one day. Think about <span class="highlight-bold">other ways to get protein</span> while avoiding red meat. Check our <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe section</a> for ideas!',
				'It looks like you&rsquo;ve <span class="highlight-bold">eaten the weekly recommendation</span> for red meat in just one day. Try to get protein from foods other than red meat. Our <a href="index.php?option=com_content&view=article&id=25&Itemid=26">Healthy Tips</a> can help!',
				'<span class="highlight-bold">Look out!</span> You&rsquo;ve eaten the weekly recommendation for red meat in just one day. Learn how to <a href="index.php?option=com_content&view=article&id=25&Itemid=26">substitute other foods</a> for red meat. ',
				)),
			5 => array( 4, array(
				'It looks like you&rsquo;ve eaten more than the weekly recommendation. Learn about <span class="highlight-bold">other types of protein</span> that taste good and are good for you in our <a href="index.php?option=com_content&view=article&id=25&Itemid=26">Healthy Tips</a> section!',
				'It seems like you have eaten <span class="highlight-bold">more than the weekly recommendation.</span> Check out other types of healthy protein with our great fish and chicken <a href="index.php?option=com_content&view=article&id=32&Itemid=9">recipes</a>!',
				'You&rsquo;ve eaten more than the weekly red meat recommendation. <span class="highlight-bold">Change takes time and planning.</span> Learn about other types of protein for next week&rsquo;s meals with our <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe section</a>.',
				)),
		),		
		'wfeedback' => array(
			1 => array( 0, '<li>Excellent! You ate only <span class="highlight-bold">%s servings</span> of red meat over the last <span class="highlight-bold">7 days</span>. </li><li>Great work &#8212; <span class="highlight-bold">keep it going!</span></li>'),
			2 => array( 2, '<li>It looks like you had <span class="highlight-bold">%s servings</span> of red meat over the last <span class="highlight-bold">7 days</span>. </li><li>You stayed under the recommendation of no more than <span class="highlight-bold">3 servings</span> a week. <span class="highlight-bold">Good job!</span></li>'),
			3 => array( 3, '<li>You had <span class="highlight-bold">%s servings</span> of red meat this week, so you stayed under the red meat recommendation. </li><li>Way to go! Keep in mind that you&rsquo;ve <span class="highlight-bold">reached the limit</span> for red meat this week.</li>'),
			4 => array( 6, '<li>It looks like you had <span class="highlight-bold">%s servings</span> of red meat over the last <span class="highlight-bold">7 days</span>, more than the recommended amount. </li><li>Can you think of ways to <span class="highlight-bold">replace red meat in your meals?</span></li> '),
			5 => array( 7, '<li>It looks like you had <span class="highlight-bold">%s servings</span> of red meat over the last <span class="highlight-bold">7 days</span>. </li><li>This is much more than the recommended amount. Try to <span class="highlight-bold">eat less next week</span> &#8212; <a href="index.php?option=com_content&view=article&id=25&Itemid=26">learn how here</a>.</li>'),
		),		
		// Trend feedback	
		'tfeedback' => array(
			1 =>  '<li>That&rsquo;s <span class="highlight-bold">much less</span> than last week, when you had <span class="highlight-bold">%s servings.</span></li><li> <span class="highlight-bold">Great work!</span></li>',
			2 =>  '<li>That&rsquo;s <span class="highlight-bold">less than</span> last week, when you had an average of <span class="highlight-bold">%s servings.</span></li><li> Keep up the <span class="highlight-bold">good work!</span></li>',
			3 =>  '<li>That&rsquo;s <span class="highlight-bold">about the same amount</span> as last week, when you had an average of <span class="highlight-bold">%s servings.</span></li><li> Keep your <span class="highlight-bold">eye on the goal!</span></li> ',
			4 =>  '<li>That&rsquo;s a <span class="highlight-bold">bit more</span> than last week, when you had an average of <span class="highlight-bold">%s servings.</span></li><li> Try to <span class="highlight-bold">cut back on red meat</span> and go for other protein sources instead.</li>',
			5 =>  '<li>That&rsquo;s a <span class="highlight-bold">lot more</span> than last week, when you had an average of <span class="highlight-bold">%s servings.</span></li><li> Work toward the goal this week, and you&rsquo;ll be <span class="highlight-bold">back on track.</span></li> ',
		),		
		'helplinktxt' => 'What&rsquo;s a serving?',
		'helpitem' => 91,
		'helpid' => 74,
		),

	4 => array(		// mv
		'name' => 'Taking a Multivitamin',
		'sname' => 'Multivitamins',
		'labels' => 2,		
		'minVal' => 0,
		'maxVal' => 1,
		'maxChartVal' => 1,
		'entrytype' => _HCC_TRACKING_IN_RADIO,
		'homeinfo' => 'We recommend that you <span class="highlight-bold">take a multivitamin every day.</span>',
		'formquest' => 'Did you take a multivitamin?',
		'options' => array(
			_HCC_TRACKING_NO => 0,
			_HCC_TRACKING_YES => 1),
		'step2item' => '',
		'contentid' => 26,
		'contentitem' => 27,
		'feedbacktype' => _HCC_TRACKING_FEED_YES,
		'weekavgtype' => _HCC_TRACKING_AVG_YES,
 		'weekavgdec' => 0,
		'image' => 'mv_icon',
		'weekchart' => 'MV_weekly.gif',
		'longchart' => 'MV_history.gif',
		'dailygoal' => _HCC_TRACKING_DATA_YES,
		'weeklygoal' => 6,		
		'trendgoal' => 7,		
		'weeklyMRFmin' => 2,		
		'goalcompare' => '>',
		'dfeedback' => array(
			// no
			1 => array( 0, array(
				'You didn&rsquo;t reach the goal today. Learn <span class="highlight-bold">easy ways</span> to <a href="index.php?option=com_content&view=article&id=26&Itemid=27">get back on track</a> for tomorrow!',
				'<span class="highlight-bold">You didn&rsquo;t hit the goal.</span> <a href="index.php?option=com_content&view=article&id=26&Itemid=27">Check these tips</a> for reaching it tomorrow! ',
				'It looks like you didn&rsquo;t reach the goal &#8212; but <span class="highlight-bold">tomorrow is another day!</span> <a href="index.php?option=com_content&view=article&id=26&Itemid=27">Check out these tips</a> today.',
				)),
			// yes
			2 => array( 1, array(
				'Good for you! <span class="highlight-bold">Way to go.</span>',
				'<span class="highlight-bold">You reached the goal.</span> Keep it up!',
				'This is great news! <span class="highlight-bold">Good job!</span>',
				)),
		),		
		'wfeedback' => array(
			1 => array( 0, '<li>It seems like you didn&rsquo;t take <span class="highlight-bold">any multivitamins</span> over the last <span class="highlight-bold">7 days.</span></li><li> Try getting started and <span class="highlight-bold">take one today!</span> <a href="index.php?option=com_content&view=article&id=26&Itemid=27">Check out these hints</a>.</li>'),
			2 => array( 1, '<li>It looks like you took a multivitamin on <span class="highlight-bold">%s</span> out of the last <span class="highlight-bold">7 days.</span></li><li> This is <span class="highlight-bold">a good start.</span> Can you try to take it a few more times over the next 7 days?</li>'),
			3 => array( 2, '<li>It looks like you took a multivitamin on <span class="highlight-bold">%s</span> out of the last <span class="highlight-bold">7 days.</span></li><li> Can you think of ways to <span class="highlight-bold">get that number up?</li>'),
			4 => array( 4, '<li>It looks like you took a multivitamin on <span class="highlight-bold">%s</span> out of the last <span class="highlight-bold">7 days.</span> </li><li><span class="highlight-bold">You&rsquo;re close to the goal!</span> Keep going.</li>'),
			5 => array( 6, '<li>Awesome! You took a multivitamin on <span class="highlight-bold">%s</span> out of the last <span class="highlight-bold">7 days.</span> </li><li><span class="highlight-bold">Good for you!</span></li>'),
		),		
		// Trend feedback	
		'tfeedback' => array(
			1 =>  '<li><span class="highlight-bold">Last week was better,</span> when you took it on <span class="highlight-bold">%s days</span> of the week.</li><li> What <span class="highlight-bold">helped you</span> take it then? Can you do those things this week?</li>',
			2 =>  '<li>This <span class="highlight-bold">isn&rsquo;t as good</span> as the week before, when you took it on <span class="highlight-bold">%s days</span> of the week.</li><li> <span class="highlight-bold">Try again this week!</span></li>',
			3 =>  '<li>This is <span class="highlight-bold">about the same</span> as last week, when you took it on <span class="highlight-bold">%s days</span> of the week.</li><li> Keep trying to <span class="highlight-bold">hit that goal!</span></li>',
			4 =>  '<li>That&rsquo;s <span class="highlight-bold">even better</span> than last week, when you took it on <span class="highlight-bold">%s days</span> of the week.</li><li> <span class="highlight-bold">Keep going!</span></li>',
			5 =>  '<li>That&rsquo;s <span class="highlight-bold">much better</span> than last week, when you took it on <span class="highlight-bold">%s days</span> of the week.</li><li> <span class="highlight-bold">Nice job!</span></li> ',
		),		
		'helplinktxt' => null,
		'helpitem' => 0,
		'helpid' => 0,
		),
	5 => array(		// smoking 
		'name' => 'Quitting Smoking',
		'sname' => 'Smoking',
		'labels' => 13,
		'minVal' => 0,
		'maxVal' => 30,
		'maxChartVal' => 30,
		'entrytype' => _HCC_TRACKING_IN_NUMERIC,
		'homeinfo' => 'We recommend that you <span class="highlight-bold">do not smoke.</span>',
		'formquest' => 'How many cigarettes did you smoke?',		
		'options' => null,
		'step2item' => 'cigarettes',
		'contentid' => 27,
		'contentitem' => 28,
		'feedbacktype' => _HCC_TRACKING_FEED_YES,
		'weekavgtype' => _HCC_TRACKING_AVG_DAILY,
 		'weekavgdec' => 1,
		'image' => 'sm_icon',
		'weekchart' => 'SM_weekly.gif',
		'longchart' => 'SM_history.gif',
		'dailygoal' => 0,
		'weeklygoal' => 0,		
		'trendgoal' => 25,		
		'weeklyMRFmin' => 1,		
		'goalcompare' => '<',
		'dfeedback' => array(
			1 => array( 0, array(
				'That is amazing news! <span class="highlight-bold">Keep up the good work.</span>',
				'<span class="highlight-bold">You reached the goal.</span> Give yourself a hand!',
				'This is super!<span class="highlight-bold"> Keep going with this healthy habit.</span>',
				)),
			2 => array( 1, array(
				'<span class="highlight-bold">Everyone slips up</span> from time to time. Check out <a href="index.php?option=com_content&view=article&id=70&Itemid=87">how to become smoke-free</a>.',
				'You didn&rsquo;t reach the goal today. <span class="highlight-bold">It&rsquo;s OK;</span> now focus on ways to <a href="index.php?option=com_content&view=article&id=70&Itemid=87">get back on track</a> for tomorrow.',
				'It seems like you didn&rsquo;t reach the goal &#8212; but you can <span class="highlight-bold">try to make tomorrow smoke-free.</span> <a href="index.php?option=com_content&view=article&id=70&Itemid=87">Check out these tips</a> today to get ready.',
				)),
		),		
		'wfeedback' => array(
			1 => array( 0, '<li>You <span class="highlight-bold">didn&rsquo;t smoke</span> for the last <span class="highlight-bold">7 days.</span></li><li> Your hard work paid off &#8212; <span class="highlight-bold">congratulations!</span></li>'),
			2 => array( 1, '<li>It looks like you smoked an average of <span class="highlight-bold">%s cigarettes</span> a day over the last <span class="highlight-bold">7 days.</span></li><li> <a href="index.php?option=com_content&view=article&id=70&Itemid=87">Get tips on kicking ash here</a>.</li>'),
		),		
		// Trend feedback	
		'tfeedback' => array(
			1 =>  '<li>This is <span class="highlight-bold">much better</span> than last week, when you smoked an average of <span class="highlight-bold">%s a day.</span></li><li> That is super news! <span class="highlight-bold">Congrats!</span></li>',
			2 =>  '<li>This is <span class="highlight-bold">better than</span> last week, when you smoked an average of <span class="highlight-bold">%s a day.</span></li><li> <span class="highlight-bold">You are doing great</span> &#8212; keep it up!</li> ',
			3 =>  '<li>This is <span class="highlight-bold">about the same</span> as last week, when you smoked an average of <span class="highlight-bold">%s a day.</span></li><li> Are there any <span class="highlight-bold">small changes</span> you can make this week to keep moving forward?</li>',
			4 =>  '<li>This <span class="highlight-bold">isn&rsquo;t quite as good</span> as the week before, when you smoked an average of <span class="highlight-bold">%s a day.</span></li><li> Think about ways to get <span class="highlight-bold">back on track.</span></li>',
			5 =>  '<li>This <span class="highlight-bold">isn&rsquo;t as good</span> as the week before, when you smoked an average of <span class="highlight-bold">%s a day.</span></li><li> What helped you last week, and can you do those same things to get <span class="highlight-bold">back on track?</span></li>',
		),		
		'helplinktxt' => null,
		'helpitem' => 0,
		'helpid' => 0,
		),
	6 => array(		// MRF - historical chart only 
		'name' => 'My Score',
		'sname' => 'My Score',
		'labels' => 11,
		'maxChartVal' => 5,
		'options' => null,
		'contentid' => 19,
		'contentitem' => 37,
		'image' => 'mrf_icon',
		'longchart' => 'MRF_history.gif',
		'weeklygoal' => 5,		
		'weeklyMRFmin' => 1,		
		'dailygoal' => 5,
		'goalcompare' => '>',
		'helplinktxt' => null,
		'helpitem' => 0,
		'helpid' => 0,
		),
);


?>
