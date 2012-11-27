<?php
global $planBehSpecs;
global $strategySpecs, $barrierSpecs;
global $tipSpecs;




$planBehSpecs = array(
	1 => array(			// pa
			'goal' => 'I want to walk at least 10,000 steps a day.',
	),
	2 => array(			// fruits
			'goal' => 'I want to eat 5 to 9 servings of fruits and vegetables a day.',
	),
	3 => array(			// rm
			'goal' => 'I want to eat no more than 3 servings of red meat a week.',
	),
	4 => array(			// mv
			'goal' => 'I want to take a multivitamin every day.',
	),
	5 => array(			// Smoking
			'goal' => 'I want to quit smoking.',
	),

);


$strategySpecs = array(
	// For each behavior
	1 => array(			// pa
			1 => 'I can take a walk around my neighborhood each day.',
			2 => 'I can get off the bus or train one stop early.',
			3 => 'I can take the stairs instead of the elevator or escalator at work each day.',
			4 => 'I can take my dog for a longer daily walk.',
			5 => 'I can start a walking group with friends or neighbors.',
			6 => 'I can walk with my family every day.',
	),
	2 => array(			// f & v
			1 => 'I can keep fruit in the refrigerator or on my kitchen counter so it&rsquo;s handy.',
			2 => 'I can add a banana or strawberries to my breakfast cereal.',
			3 => 'I can pack an apple or orange in my lunch.',
			4 => 'I can visit the salad bar for lunch.',
			5 => 'I can include a salad with my dinner.',
			6 => 'I can add chopped vegetables to pasta sauces or pizzas.',
			7 => 'I can buy frozen or canned fruits and vegetables when fresh ones aren&rsquo;t in season.',
			8 => 'I can buy bagged, pre-washed lettuce that is ready to eat.',
	),
	3 => array(			// rm
			1 => 'I can cook turkey, chicken, or fish instead.',
			2 => 'I can cook with beans and lentils, which both have a lot of protein (the way red meat does).',
			3 => 'I can cook with tofu, which has as much protein as meat.',
			4 => 'When I do buy meat, I can buy lean cuts (with &#8220;loin&#8221; or &#8220;round&#8221; on the package).',
			5 => 'I can make serving sizes of meat 3 ounces or less (the size of a deck of cards).',
	),
	4 => array(			// mv
			1 => 'I can keep my multivitamins in a place where I&rsquo;ll remember to take one.',
			2 => 'I can buy generic brand multivitamins to save money.',
			3 => 'I can keep extra multivitamins in my bag in case I forget to take one at home.',
			4 => 'I can make it a habit by taking my multivitamin at the same time every day (like with breakfast, for example).',
	),
	5 => array(			// smoking
			1 => 'I can make a list of things I don&rsquo;t like about smoking.',
			2 => 'I can start cutting down on smoking.',
			3 => 'I can figure out the best quit-smoking plan for me.',
			4 => 'I can use this site to research local quit-smoking resources.',
			5 => 'I can use this site to research quit-smoking medications.',
			6 => 'I can ask my doctor for help.',
			7 => 'I can talk to other people who have quit smoking and ask for their help.',
	),
);



$barrierSpecs = array(
	// For each behavior
	1 => array(			// pa
			1 => 'I don&rsquo;t have time.',
			2 => 'I don&rsquo;t feel safe.',
			3 => 'I only have time at night, when it&rsquo;s too dark.',
			4 => 'The weather is bad.',
			5 => 'Joining a gym costs too much.',
			6 => 'I lost my pedometer and don&rsquo;t know how to keep track of how much I&rsquo;ve walked.',
	),
	2 => array(			// fruits
			1 => 'My family doesn&rsquo;t like fruits or vegetables.',
			2 => 'I eat out a lot, so it&rsquo;s hard to get enough fruits and vegetables.',
			3 => 'Fruits and vegetables are expensive.',
			4 => 'I don&rsquo;t know how to choose or cook vegetables.',
			5 => 'I don&rsquo;t have time to cook.',
			6 => 'I don&rsquo;t like how fruits and vegetables taste.',
			7 => 'There are no fresh fruits and vegetables in my local stores.',
	),
	3 => array(			// rm
			1 => 'My family mostly likes only red meat.',
			2 => 'I don&rsquo;t know how to cook other kinds of foods.',
			3 => 'I don&rsquo;t like chicken, turkey, or fish.',
			4 => 'I don&rsquo;t have time to cook.',
	),
	4 => array(			// mv
			1 => 'I don&rsquo;t think I need multivitamins.',
			2 => 'I can&rsquo;t remember to take a pill every day.',
			3 => 'I can&rsquo;t afford to buy multivitamins.',
			4 => 'I don&rsquo;t like swallowing pills.',
			5 => 'I don&rsquo;t like how multivitamins make me feel.',
	),
	5 => array(			// smoking
			1 => 'I&rsquo;ve tried to quit before, and it didn&rsquo;t work.',
			2 => 'I&rsquo;m addicted to smoking.',
			3 => 'All of my friends smoke.',
			4 => 'Smoking helps me relax or calm down.',
			5 => 'I can&rsquo;t afford quit-smoking medication.',
	),
);


$tipSpecs = array(
	1 => array(			// pa
			1 => array(
'Take a walk on the weekend, when you have more time.'
,'Take a walk early in the morning, before your day starts.'
,'Have a friend or family member watch your kids while you walk. Or take your kids along.'
,'Take a walk during a break at work.'
,'Add steps by walking for errands or getting off the T or bus one stop early.'
,'If you drive, park further away and get extra steps.'
				),
			2 => array(
'Go with friends or family to a safer neighborhood.'
,'Walk at your local Y or community center.'
,'Walk in a mall.'
,'Find out if your local school allows people to walk in the gym after school hours.'
,'Walk around the track at a local school.'
,'Take a walk during lunch at work.'
				),
			3 => array(
'Walk with friends or family instead of going alone.'
,'Wear light-colored clothing.'
,'Wear clothing with reflective patches.'
,'Exercise in your house or apartment.'
,'Walk in the mall.'
				),
			4 => array(
'Wear extra clothes in the winter. Layers will keep you warm. You may feel cold at first, but exercising will warm you up.'
,'If it&rsquo;s hot out, walk early or late in the day. Stay in the shade. Carry water with you.'
,'Walk on an indoor track at the Y, community athletic center, or local school.'
,'Walk in the mall.'
				),
			5 => array(
'Walking is free – you don&rsquo;t need to join a gym to walk!'
,'Find out if your health insurance company will pay for part of the membership.'
,'Ask if your work offers a gym membership. They may also pay for part of it.'
,'Join a Y or community center, where the rates are usually much lower.'
,'Visit our <a href="index.php?option=com_content&view=article&id=4&Itemid=8">Resources</a> section for free or low-cost ideas.'
				),
			6 => array(
'Get a new pedometer from Healthy Directions. Call (617) xxx-xxxx or email trackmychanges.org.'
,'You can figure out the number of steps based on how long or how far you walked. One mile is about 2,000 to 2,500 steps. It may take about 25 to 30 minutes to walk.'
				),
	),
	2 => array(			// fruits
			1 => array(
'Talk with your family about why fruits and vegetables are good for them.' 
,'Add chopped vegetables to some of your family&rsquo;s favorite recipes. Try adding carrots, broccoli, and peppers to spaghetti sauce.'
,'Offer your kids 2 or 3 choices of vegetables. Let them pick which one they want with dinner.'
,'Let your kids dip vegetables in low-fat or fat-free salad dressing.'
,'Let your kids dip fruits in low-fat or fat-free yogurt.'
,'Keep offering fruits and vegetables to your kids. It can take a while for kids to like new foods.'
				),
			2 => array(
'Ask for vegetables instead of fries.'
,'Order a salad with your meal.'
,'If you have to eat fast food, pick restaurants that have vegetables on the menu.'
,'Pack your lunch.'
,'Make dinner at home. Invite friends or family over.'
				),
			3 => array(
'Buy fruits and vegetables that are in season. They cost less.'
,'Big bags of apples or oranges are usually cheaper.'
,'Visit a local farmer&rsquo;s market.'
,'Try a local warehouse store.'
,'Buy canned or frozen fruits and vegetables. Buy fruits that are packed in water or 100% fruit juice. Buy vegetables that don&rsquo;t have added salt.' 
				),
			4 => array(
'Ask friends or family to show you how to clean and prepare vegetables.'
,'Ask friends and family for vegetable recipes.'
,'Borrow a cookbook from the library, or visit the <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe</a> section of the site.'
,'Have your family help you clean and prepare vegetables.'
,'Some vegetables (like broccoli, carrots, and cauliflower) can be eaten raw. Be sure to clean all raw vegetables before you eat them.'
				),
			5 => array(
'Make extra servings when you cook. Store the extras in the freezer.'
,'Plan a week&rsquo;s menus ahead of time. Cook what you can over the weekend, when you have more time.'
,'Ask your family to help you in the kitchen. Give everyone a job to do.'
,'Buy fruits and vegetables that are already washed and cut. '
,'Make recipes that take less than 30 minutes.'
				),
			6 => array(
'Add chopped vegetables to some of your family&rsquo;s favorite recipes. For example, add carrots, broccoli, and peppers to spaghetti sauce.'
,'Try new recipes that have fruits or vegetables.'
,'Keep trying new fruits or vegetables until you find ones that you like.'
,'Dip vegetables in low-fat or fat-free salad dressing.'
,'Dip fruits in low-fat or fat-free yogurt.'
,'Make salads more exciting by including lots of colors. Add carrots, red or yellow peppers, red cabbage, and red onion.' 
				),
			7 => array(

'Visit a local farmer&rsquo;s market.'
,'Try a local warehouse store.'
,'Ask neighbors where they buy fruits and vegetables.'
,'Look for different stores near where you work or go to religious services.'
,'Buy canned or frozen fruits and vegetables. Buy fruits that are packed in water or 100% fruit juice. Buy vegetables that don&rsquo;t have added salt.'  
				),
	),
	3 => array(			// rm
			1 => array(
'Talk with your family about why eating less meat is healthy for everyone.'
,'Make some of your family&rsquo;s favorite recipes with ground turkey or chicken instead of red meat. Try lasagna with ground turkey. Make sandwiches with grilled chicken or turkey instead of lunch meats like bologna or salami.'
,'Try bean dishes like rice and beans or baked beans.'
,'Keep offering healthy, lean protein to your kids.' 
				),
			2 => array(
'Ask friends and family for help with recipes.'
,'Check our <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe</a> section for recipes that use chicken, turkey, and fish.'
,'Take a cooking class.'
				),
			3 => array(
'Make some of your favorite recipes with ground turkey or chicken instead of red meat. Try lasagna with ground turkey. Make sandwiches with grilled chicken instead of lunch meats like bologna or salami.'
,'Try bean dishes like rice and beans or baked beans.'
,'Try new recipes that call for canned tuna or salmon.'
,'Ask friends and family for recipe ideas.'
,'Visit the <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe</a> section for great chicken, turkey, and fish recipes.' 
				),
			4 => array(
'Make extra meals when you cook. Store them in your freezer.'
,'Plan a week&rsquo;s menus ahead of time. Cook what you can over the weekend, when you have time.'
,'Ask your family to help you in the kitchen. Give everyone a job to do.'
,'Buy some healthy prepared meals from the grocery store.'
,'Buy prepared ingredients, like pre-washed and cut vegetables to add to recipes.'
,'Make recipes that take 30 minutes or less. Check the <a href="index.php?option=com_content&view=article&id=32&Itemid=9">Recipe</a> section for easy, tasty ideas.'
				),
	),
	4 => array(			// mv
			1 => array(
'Ask your doctor to explain why multivitamins are important.'
,'See if your health center or pharmacy has information about how multivitamins work to keep you healthy.'
,'Remember that it can be hard to get all the nutrients you need from food. Multivitamins are an easy way to help fill in the gaps.'
				),
			2 => array(
'Take your multivitamin at the same time every day, when you do something else. For example, take it when you drink juice in the morning.'
,'Put a reminder note on a place you see every day, like your refrigerator or your bathroom mirror.'
,'Buy a pillbox. If you already use a pillbox, add a multivitamin for each day.'
,'Keep some multivitamins in your bag, locker, or desk drawer. If you forget at home, you can take one later.' 
				),
			3 => array(
'Buy the store brand. They work the same as the brand-name multivitamins and cost much less.'
,'Buy multivitamins in bigger bottles. Each pill will cost less.'
,'Healthy Directions will be giving everyone a free 3-month supply of multivitamins.'
				),
			4 => array(
			'Multivitamins come in many shapes and sizes. Try different types until you find one that is easier to swallow.'
,'Take a liquid multivitamin. It comes in a large bottle. Each day you pour out and drink one dose.'
				),
			5 => array(
'Take your multivitamin with food.'
,'Talk to your doctor or pharmacist. They may have tips on how to deal with side effects.'
,'Remember that multivitamins do your body a lot of good.' 
				),
	),
	5 => array(			// smoking
			1 => array(
'Most smokers have to quit many times before they can stay quit. Don&rsquo;t give up!'
,'Think about what has gotten in the way in the past. Did you have a hard time dealing with cravings? Then quit-smoking medication might be for you. '
,'Think about other things you have done in the past that were challenging. How did you accomplish those? Can anything from those experiences help you quit smoking?'
,'Be sure to develop a plan before quitting. This includes setting a quit date and preparing for it mentally and physically. Healthy Directions can help.'
,'Get support from someone you&rsquo;re close to.'
				),
			2 => array(
'Learn about quit-smoking medications. These can make it much easier to deal with cravings.'
,'Learn how to tame cravings. Try waiting 5 minutes before having a cigarette, or drink a large glass of water when a craving hits. '
,'Know that you can conquer cravings with the right combination of quit-smoking medication, help from people around you, and tips from Healthy Directions.'
				),
			3 => array(
'Go out with friends who don&rsquo;t smoke while you are quitting.'
,'Ask smokers to help you by not offering you cigarettes or smoking when they are around you.'
,'Go with friends to places where you can&rsquo;t smoke, like the movies.'
				),
			4 => array(
'Try dealing with stressful situations by doing deep-breathing exercises.'
,'Exercise is a great way to release tension. Take a quick walk instead of smoking.'
,'Call or text friends and vent to them.'
,'Chew gum or eat something healthy and crunchy, like carrots.'
				),
			5 => array(
'Most insurance plans cover some or all of the cost of quit-smoking medications. Many also offer quit-smoking counseling programs. Ask yours what is covered.'
,'Some employers offer quit-smoking programs and may cover some or all of the cost of medication.' 
				),
	),
);

?>
