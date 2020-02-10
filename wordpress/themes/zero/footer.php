<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sojuz
 */

?>

	</div><!-- #content -->
</div><!-- #page -->
<script>
	jQuery(document).ready(() => {
		let data= jQuery('#page').outerHeight(true)
		var event = new CustomEvent('myCustomEvent', { detail: data })
		window.parent.document.dispatchEvent(event)
	})

	jQuery('input[type="radio"]').change(() => {
		setTimeout(() => {
			let data= jQuery('#page').outerHeight(true)
			var event = new CustomEvent('myCustomEvent', { detail: data })
			window.parent.document.dispatchEvent(event)
		}, 200)
	})
</script>
<?php wp_footer(); ?>

</body>
</html>
