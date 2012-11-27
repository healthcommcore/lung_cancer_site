<?php

defined ( '_JEXEC') or die ('Restricted access');
?>

<div class="componentheading<?php echo $this->suffix;?>">
<?php if ($this->showtitle) echo $this->pagetitle ?>

</div>
<table class="blog" cellpadding="0" cellspacing="0">
<td valign="top">

<?php
if ($this->intro != '')  echo '<p>' . nl2br( $this->intro) .'</p>';
?>
</td>
</tr>
<tr>
	<td valign="top">
<?php foreach ($this->list as $recipe) : ?>
					<div>

<table class="contentpaneopen<?php echo $this->suffix;?>">
<tbody><tr>
<!-- Title -->
<td class="contentheading<?php echo $this->suffix;?>" width="80%">
<span class="recipeTitle">
<?php 
if ($this->linktitle) {
?>
<a href="<?php echo $recipe->link; ?>">
<?php } ?>
<?php echo ($recipe->title); ?>
<?php 
if ($this->linktitle) {
?>
</a>
<?php } ?>
</span>
</td>
<td>
<a href="<?php echo $recipe->link; ?> " class="readon<?php echo $this->suffix;?>">Read More...</a>
</td>
</tr>
</tbody></table>


<?php
if ($this->showimgintro == 1) {
?>
<table class="contentpaneopen<?php echo $this->suffix;?>">
<tr><td valign="top">
<p>
<?php
if ($recipe->imagefile != '') {
			$file = substr( strrchr( $recipe->imagefile,'/'),1);
			$filename = substr( $file, 0, stripos( $file,'.'));
			$ext = substr( strrchr( $file,'.'),0 );

	echo '<img src="images/recipes/icons/'. $filename . '_icon' . $ext .'" align="left" class="recipe" >';
}
if ($recipe->intro != '') { 
	echo $recipe->intro; 
} 
?>
</p>
</td></tr>
<tr><td>
</td>
</tr>
</tbody></table>
	<?php } ?>

<span class="article_separator">&nbsp;</span>

</div>
<?php endforeach; ?>
</td>

</tr>
</tbody></table>
