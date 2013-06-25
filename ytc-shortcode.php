<?php
/*
Plugin Name: YouTube Control Shortcode
Plugin URI: http://wordpress.org/extend/plugins/youtube-shortcode/
Description: Adds shortcode that enables you to create accordions
Author: Devindra Payment, CTLT
Version: 1.0
Author URI: http://ctlt.ubc.ca
*/

/**
 * YouTube_Control_Shortcode class.
 */
class YouTube_Control_Shortcode {
	static $counter = 0;
	static $title_counter;
	static $player_id = null;
	static $wrapper_id = null;
	
	static $defaults = array(
		'id'       => '',
		'title'    => "Browse Video Segments",
		'autoplay' => '0',
		'autohide' => '2',
		'theme'    => "dark",
		'ratio'    => "720:440", //Standard youtube ratio for 720p
		'width'    => '1320px',
		'nav'      => 'auto',
	);
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function init() {
		add_shortcode( 'yc_video',   array( __CLASS__, 'youtube_shortcode' ) );
		add_shortcode( 'yc_control', array( __CLASS__, 'control_shortcode' ) );
		add_shortcode( 'yc_title',   array( __CLASS__, 'title_shortcode' ) );
		
		add_action( 'init',               array( __CLASS__, 'register_script' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_style' ) );
	}
	
	/**
	 * register_script function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function register_script() {
		wp_register_style( 'ytc-embed',  plugins_url( 'css/embed.css', __FILE__ ) );
		wp_register_style( 'ytc-controls',  plugins_url( 'css/controls.css', __FILE__ ) );
	}
	
	/**
	 * enqueue_style function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function enqueue_style() {
		wp_enqueue_style( 'ytc-embed' );
		wp_enqueue_style( 'ytc-controls' );
	}
	
	/**
	 * accordion_shortcode function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function youtube_shortcode( $atts, $content ) {
		wp_enqueue_script( 'ytc-shortcode' , plugins_url( 'ytc-shortcode.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		if ( ! isset( $atts['id'] ) ):
			return;
		endif;
		
		if ( in_array( 'autoplay', $atts ) ):
			$atts['autoplay'] = 'true';
		endif;
		
		if ( in_array( 'autohide', $atts ) ):
			$atts['autohide'] = 'true';
		endif;
		
		$atts = shortcode_atts( self::$defaults, $atts );
		
		if ( $atts['autoplay'] == '1' || $atts['autoplay'] == 'true' ):
			$atts['autoplay'] = '1';
		else:
			$atts['autoplay'] = self::$defaults['autoplay'];
		endif;
		
		if ( $atts['autohide'] == '1' || $atts['autohide'] == 'true' ):
			$atts['autohide'] = '1';
		else:
			$atts['autohide'] = self::$defaults['autohide'];
		endif;
		
		if ( $atts['theme'] != "light" ):
			$atts['theme'] = self::$defaults['theme'];
		endif;
		
		if ( ! in_array( $atts['nav'], array( 'auto', 'compact', 'expanded' ) ) ):
			$atts['nav'] = self::$defaults['nav'];
		elseif ( $atts['nav'] == 'expanded' ):
			$atts['nav'] = "";
		endif;
		
		self::$counter++;
		self::$title_counter = 0;
		self::$player_id = 'ytplayer-'.self::$counter;
		self::$wrapper_id = 'ytc-wrapper-'.self::$counter;
		
		$ratio = split( ':', $atts['ratio'] );
		$percentage = $ratio[1] / $ratio[0] * 100;
		
		$content = do_shortcode( $content );
		
		$classes = $atts['nav'];
		$classes .= ( $content == "" ? " no-controls" : "" );
		
		ob_start();
		?>
		<div id="<?php echo self::$wrapper_id; ?>" class="ytc-embed <?php echo $classes; ?>" style="max-width: <?php echo $atts['width']; ?>" data-atts='<?php echo json_encode( $atts ); ?>' >
			<?php if ( $content != "" ): ?>
				<ul class="ytc-controls">
					<?php
						if ( self::$title_counter == 0 ):
							echo self::title_shortcode( array( $atts['title'] ) );
						endif;
						echo $content;
					?>
				</ul>
			<?php endif; ?>
			<div class="ytc-wrapper">
				<div class="iframe-wrapper" style="padding-bottom: <?php echo $percentage; ?>%;">
					<div id="<?php echo self::$player_id; ?>" class="yc_player" data-vid="<?php echo $atts['id']; ?>" data-play="<?php echo $atts['autoplay']; ?>" data-hide="<?php echo $atts['autohide']; ?>" data-theme="<?php echo $atts['theme']; ?>">
						<div class="error">
							<img src="<?php echo plugins_url( 'img/javascript.jpg', __FILE__ ); ?>" width=64 height=64 />
							<div>You need JavaScript enabled to view this video.</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		self::$player_id = null;
		self::$wrapper_id = null;
		
		return ob_get_clean();
	}
	
	/**
	 * control_shortcode function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function control_shortcode( $atts, $content ) {
		if ( self::$player_id != null ):
			$timestamp = $atts[0];
			$title = $atts[1];
			
			$seconds = 0;
			$segments = array_reverse( split( ':', $timestamp ) );
			$increments = array( 1, MINUTE_IN_SECONDS, HOUR_IN_SECONDS );
			
			for ( $i = 0; $i < count( $segments ); $i++ ):
				$seconds += $segments[$i] * $increments[$i];
			endfor;
			
			$action = "YTControl_Shortcode.skipTo('".self::$player_id."', ".$seconds.");";
			
			ob_start();
			?>
			<li class="control" onclick="<?php echo $action; ?>">
				<div class="inner">
					<span class="onhover"><?php echo $title; ?></span>
					<span class="timestamp"><?php echo $timestamp; ?></span>
				</div>
			</li>
			<?php
			return ob_get_clean();
		else:
			return $content;
		endif;
	}
	
	/**
	 * title_shortcode function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function title_shortcode( $atts, $content = "" ) {
		if ( self::$player_id != null ):
			self::$title_counter++;
			ob_start();
			?>
			<li class="title">
				<div class="inner">
					<span class="onhover"><?php echo $atts[0]; ?></span>
				</div>
			</li>
			<?php
			return ob_get_clean();
		else:
			return $content;
		endif;
	}
}

YouTube_Control_Shortcode::init();