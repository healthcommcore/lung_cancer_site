<?php

defined ( '_JEXEC') or die ('Restricted access');
	$buttonurl =  "images/M_images/printButton.png";

?>

<div class="recipe">

<table class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' );?>">
<tr>
<!-- Title -->
<td class="contentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="100%">
<span class="recipeTitle"><?php echo $this->recipe->title; ?></span>
</td>

		<?php // if ( $this->params->get( 'show_print_icon' )) : ?>
		<td align="right" width="100%" class="buttonheading">
		<?php // echo JHTML::_('icon.print_popup',  $this->article, $this->params, $this->access); ?>
		</td>
		<?php // endif; ?>

	<?php if (!$this->print) : 
	$url =  "index.php?view=recipe&tmpl=component&option=com_recipe&amp;id=". $this->recipe->id ."&amp;print=1&amp;page=0";
	?>

            <td align="right" width="100%" class="buttonheading">
		
				<a href="<?php echo JRoute::_($url) ?>" title="Print" onclick=	"window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"><img src="<?php echo JRoute::_($buttonurl) ?>" alt="Print" name="Print"></a>		
		

		</td>
	
	<?php else : 
	?>
		<td align="right" width="100%" class="buttonheading">
			<a href="#" onclick="window.print();return false;"><img src="<?php echo JRoute::_($buttonurl) ?>" alt="Print"></a>			
		</td>
	<?php endif; ?>
			
			
</tr>


<tr><td  align="right">
  <?php 
  foreach ($this->icons as $icon) { 
  ?>
  
  <div class="<?php echo $icon[0]; ?>" id="<?php echo $icon[0]; ?>"></div>
	<?php
  }
   ?>
   </td>
</tr>   

</table>

   
<table class="contentpaneopen<?php echo $this->suffix;?>">
<tr><td>
   
   
<?php
  // echo '<p>' .  $this->recipe->webcat . '</p>
  echo 
  '<p>Serves ' .
    ($this->recipe->serves) . 
	'</p>'
  	. (($this->recipe->imagefile != '') ? 
			('<img src="images/recipes/images/' . $this->recipe->imagefile . '" align="right">') : '') 
  	.' <p><h3>Ingredients</h3>' .  $this->recipe->ingredients . 
	'</p><p><h3>Instructions</h3>' .  
     ($this->recipe->instructions) .'</p>';
   
?>

</td>
</tr>

</table>

</div>
