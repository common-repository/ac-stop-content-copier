<div class="wrap">

<img src="<?php echo plugin_dir_url(__FILE__) ?>../static/medium-icon.png" style="float: left; " width="64" height="64"/> 
<h1 style="padding-top:6px;line-height:24px;">AC Stop <br />Content Copier</h1>
<div style="clear:both;"></div>
<hr />

<div class="acbdc-col">
	<h3>FastBrowse Preferences</h3>
	<?php if(isset($message) AND $message == 'OK') echo '<div class="updated below-h2">Saved</div>'; ?>
	Enter number of seconds between visits:
	<form method="POST">
	<input type="number" name="acbd-seconds" value="<?= get_option( 'acbd-seconds', 3 ) ?>" />
	<input type="submit" name="sb" value="Save" class="button"/>
	</form>
	
	<br />
	A bot will always browse very fast and it will be able to "browse" a lot of your pages within a very low seconds range. 
	We recommend a 2-3 seconds setting for this layer.
</div><!-- IP center col -->

<div class="acbdc-col acbd-about">
	<h3>About</h3>
	This plugin is based on a three layer combination of checks to ensure scrappers are kept away:
	<ul>
		<li><strong>First Layer:</strong> Eliminate the most easy to detect scrappers by User Agent filtering (ie. cURL, libwww-perl, scrapy, etc.)</li>
		<li><strong>Second Layer:</strong> Counting the requests per second - a real user will not browse as fast as a robot</li>
		<li><strong>Third Layer:</strong> Complex behaviour computation - a bot is set to crawl your site at the same time using a <strong>cronjob</strong>. 
			We detect that by using a 3 Day comparison.</li>
	</ul>
</div>

<div style="clear:both;"></div>
<div class="acbdc-copy">
	<a href="<?php echo esc_url( __( 'http://adaptcoder.com/', 'ac content Copier' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'ac content Copier' ), 'Adapt Coder' ); ?></a>
</div>
<hr />

<h3>Content Copier Log</h3>

<?php 
$log = get_post_meta( get_option( 'acbd_captcha_page' ), 'acbd-log' );
$log = array_slice( $log, 0, 100 );
asort( $log );

if( $log ) :
	echo '<ul>';
	foreach( $log as $message ) :
		echo '<li>' . $message . '</li>';
	endforeach;
	echo '</ul>';
endif;
?>

</div><!-- wrap -->