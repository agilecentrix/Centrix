<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the top of the file. It is used mostly as an opening
 * wrapper, which is closed with the footer.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package Hybrid
 * @subpackage Template
 */

hybrid_doctype(); ?>
<html>
<head>
<meta charset="utf-8"> 
<title><?php hybrid_document_title(); ?></title>

<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen"/>

<?php hybrid_head(); // Hybrid head hook ?>
<?php wp_head(); // WP head hook ?>

</head>

<body class="<?php hybrid_body_class(); ?> fixed chrome">

<?php hybrid_before_html(); // Before HTML hook ?>

<div id="body-container">

	<?php hybrid_before_header(); // Before header hook ?>

	 <div class="header-wrapper">
      <div class="header group">
        <a href="http://centrix.kz/" class="sprite digg-logo" id="header-logo">
          centrix
        </a>
			<?php hybrid_header(); // Header hook ?>
   
      </div>
    </div>
	
	<?php// hybrid_after_header(); // After header hook ?>

 <div class="page-wrapper group ">
      <div class="column-container group">
        <div class="columns group">
          <?php hybrid_sub_header();?>
		  
		<?php hybrid_before_container(); // Before container hook ?>