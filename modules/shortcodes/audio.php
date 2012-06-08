<?php

/**
* Class wrapper for audio shortcode
*/
class AudioShortcode {

	static $add_script = false;
	
	/**
	 * Add all the actions & resgister the shortcode
	 */
	function __construct() {
		add_shortcode( 'audio', array( $this, 'audio_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'check_infinite' ) );
		add_action( 'infinite_scroll_render', array( $this, 'audio_shortcode_infinite' ), 11 );
	}

	/**
	 * Shortcode for audio
	 * [audio http://wpcom.files.wordpress.com/2007/01/mattmullenweg-interview.mp3|width=180|titles=1|artists=2]
	 *
	 * The important question here is whether the shortcode applies to widget_text:
	 * add_filter('widget_text', 'do_shortcode');
	 * */
	function audio_shortcode( $atts ) {
		global $ap_playerID;
		global $post;

		if ( ! is_array( $atts ) ) {
			return '<!-- Audio shortcode passed invalid attributes -->';
		}

		if ( ! isset( $atts[0] ) ) {
			return '<!-- Audio shortcode source not set -->';
		}

		// add the special .js
		wp_enqueue_script(
			'audio-shortcode',
			plugins_url( 'js/audio-shortcode.js', __FILE__ ),
			array( 'jquery' ),
			'1.1',
			true);

		// alert the infinite scroll renderer that it should try to load the script
		self::$add_script = true;

		$atts[0] = strip_tags( join( ' ', $atts ) );
		$src = ltrim( $atts[0], '=' );
		$ap_options = apply_filters(
			'audio_player_default_colors',
			array(
				"bg"             => "0xF8F8F8",
				"leftbg"         => "0xEEEEEE",
				"lefticon"       => "0x666666",
				"rightbg"        => "0xCCCCCC",
				"rightbghover"   => "0x999999",
				"righticon"      => "0x666666",
				"righticonhover" => "0xFFFFFF",
				"text"           => "0x666666",
				"slider"         => "0x666666",
				"track"          => "0xFFFFFF",
				"border"         => "0x666666",
				"loader"         => "0x9FFFB8"
			) );

		if ( ! isset( $ap_playerID ) ) {
			$ap_playerID = 1;
		} else {
			$ap_playerID++;
		}

		if ( ! isset( $load_audio_script ) ) {
			$load_audio_script = true;
		}

		// prep the audio files
		$src = trim( $src, ' "' );
		$options = array();
		$data = preg_split( "/\|/", $src );
		$sound_file = esc_url_raw( $data[0] );
		$sound_files = preg_split( '/,/', $sound_file );
		$num_files = count( $sound_files );
		$sound_types = array(
			'mp3'  => 'mpeg',
			'wav'  => 'wav',
			'ogg'  => 'ogg',
			'oga'  => 'ogg',
			'm4a'  => 'mp4',
			'aac'  => 'mp4',
			'webm' => 'webm'
		);

		for ( $i = 1; $i < count( $data ); $i++ ) {
			$pair = explode( "=", $data[$i] );
			if ( strtolower( $pair[0] ) != 'autostart' ) {
				$options[$pair[0]] = $pair[1];
			}
		}

		// Merge runtime options to default colour options
		// (runtime options overwrite default options)
		$options = array_merge( $ap_options, $options );
		$options['soundFile'] = esc_url_raw( $data[0] );
		$flash_vars = array();
		foreach ( $options as $key => $value ) {
			$flash_vars[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
		}
		$flash_vars = implode( '&amp;', $flash_vars );
		$flash_vars = esc_attr( $flash_vars );

		// extract some of the options to insert into the markup
		if ( isset( $options['bgcolor'] ) ) {
			$bgcolor = esc_attr( $options['bgcolor'] );
			$bgcolor = preg_replace( '/^0x/', '#', $bgcolor );
		} else {
			$bgcolor = '#FFFFFF';
		}

		if ( isset( $options['width'] ) ) {
			$width = intval( $options['width'] );
		} else {
			$width = 290;
		}

		$loop = '';
		$script_loop = 'false';
		if ( isset( $options['loop'] ) && 'yes' == $options['loop'] ) {
			$script_loop = 'true';
			if ( 1 == $num_files ) {
				$loop = 'loop';
			}
		}

		$volume = 0.6;
		if ( isset( $options['initialvolume'] ) &&
				0.0 < floatval( $options['initialvolume'] ) &&
				100.0 >= floatval( $options['initialvolume'] ) ) {

			$volume = floatval( $options['initialvolume'] )/100.0;
		}

		$file_artists = array_pad( array(), $num_files, '' );
		if ( isset( $options['artists'] ) ) {
			$artists = preg_split( '/,/', $options['artists'] );
			foreach ( $artists as $i => $artist ) {
				$file_artists[$i] = esc_html( $artist ) . ' - ';
			}
		}

		// generate default titles
		$file_titles = array();
		for ( $i = 0; $i < $num_files; $i++ ) { 
			$file_titles[] = 'Track #' . ($i+1); 
		}

		// replace with real titles if they exist
		if ( isset( $options['titles'] ) ) {
			$titles = preg_split( '/,/', $options['titles'] );
			foreach ( $titles as $i => $title ) {
				$file_titles[$i] = esc_html( $title );
			}
		}

		// fallback for the fallback, just a download link
		$not_supported = '';
		foreach ( $sound_files as $sfile ) {
			$not_supported .= sprintf(
				__( 'Download: <a href="%s">%s</a><br />', 'jetpack' ),
				esc_url_raw( $sfile ),
				esc_html( basename( $sfile ) ) );
		}

		// HTML5 audio tag
		$html5_audio = '';
		$all_mp3 = true;
		$add_audio = true;
		$num_good = 0;
		$to_remove = array();
		foreach ( $sound_files as $i => $sfile ) {
			$sfile = esc_url_raw( $sfile );
			$file_extension = pathinfo( $sfile, PATHINFO_EXTENSION );
			if ( ! preg_match( '/^(mp3|wav|ogg|oga|m4a|aac|webm)$/', $file_extension ) ) {
				$html5_audio .= '<!-- Audio shortcode unsupported audio format -->';
				if ( 1 == $num_files ) {
					$html5_audio .= $not_supported;
				}

				$to_remove[] = $i; // make a note of the bad files
				$all_mp3 = false;
				continue;
			} elseif ( ! preg_match( '/^mp3$/', $file_extension ) ) {
				$all_mp3 = false;
			}

			if ( 0 == $i ) { // only need one player
				$html5_audio .= <<<AUDIO
				<span id="wp-as-{$post->ID}_{$ap_playerID}-container">
					<audio id='wp-as-{$post->ID}_{$ap_playerID}' controls preload='none' $loop style='background-color:$bgcolor;width:{$width}px;'>
						<span id="wp-as-{$post->ID}_{$ap_playerID}-nope">$not_supported</span>
					</audio>
				</span>
				<br />
AUDIO;
			}
			$num_good++;
		}

		// player controls, if needed
		if ( 1 < $num_files ) {
			$html5_audio .= <<<CONTROLS
				<span id='wp-as-{$post->ID}_{$ap_playerID}-controls' style='display:none;'>
					<a id='wp-as-{$post->ID}_{$ap_playerID}-prev'
						href='javascript:audioshortcode.prev_track( "{$post->ID}_{$ap_playerID}" );'
						style='font-size:1.5em;'>&laquo;</a>
					|
					<a id='wp-as-{$post->ID}_{$ap_playerID}-next'
						href='javascript:audioshortcode.next_track( "{$post->ID}_{$ap_playerID}", true, $script_loop );'
						style='font-size:1.5em;'>&raquo;</a>
				</span>
CONTROLS;
		}
		$html5_audio .= "<span id='wp-as-{$post->ID}_{$ap_playerID}-playing'></span>";

		$swfurl = apply_filters(
			'jetpack_static_url',
			'http://en.wordpress.com/wp-content/plugins/audio-player/player.swf' );

		// process regular flash player, inserting HTML5 tags into object as fallback
		if ( $all_mp3 ) {
			$audio_tags = <<<FLASH
				<object id='wp-as-{$post->ID}_{$ap_playerID}-flash' type='application/x-shockwave-flash' data='$swfurl' width='$width' height='24'>
					<param name='movie' value='$swfurl' />
					<param name='FlashVars' value='{$flash_vars}' />
					<param name='quality' value='high' />
					<param name='menu' value='false' />
					<param name='bgcolor' value='$bgcolor' />
					<param name='wmode' value='opaque' />
					$html5_audio
				</object>
FLASH;
		} else { // just HTML5 for non-mp3 versions
			$audio_tags = $html5_audio;
		}

		// strip out all the bad files before it reaches .js
		foreach ( $to_remove as $i ) {
			array_splice( $sound_files, $i, 1 );
			array_splice( $file_artists, $i, 1 );
			array_splice( $file_titles, $i, 1 );
		}

		// mashup the artist/titles for the script
		$script_titles = array();
		for ( $i = 0; $i < $num_files; $i++ ) { 
			$script_titles[] = $file_artists[$i] . $file_titles[$i];

		}

		// javacript to control audio
		$script_files   = "'" . implode( "', '", $sound_files ) . "'";
		$script_titles  = "'" . implode( "', '", $script_titles ) . "'";
		$script = <<<SCRIPT
			<script type='text/javascript'>
			//<![CDATA[
			jQuery(document).on( 'ready as-script-load', function($) {
				if ( typeof window.audioshortcode != 'undefined' ) {
					audioshortcode.prep(
						'{$post->ID}_{$ap_playerID}',
						[$script_files],
						[$script_titles],
						$volume,
						$script_loop );
				}
			} );
			//]]>
			</script>
SCRIPT;

		// add the special javascript, if needed
		if ( 0 < $num_good ) {
			$audio_tags .= $script;
		}
		return "<span style='text-align:left;display:block;'><p>$audio_tags</p></span>";
	}

	/**
	 * If the theme uses infinite scroll, include jquery at the start
	 */
	function check_infinite() {
		if ( current_theme_supports( 'infinite-scroll' ) ) {
			wp_enqueue_script( 'jquery' );
		}
	}

	/**
	 * Dynamically load the .js, if needed
	 *
	 * This hooks in late (priority 11) to infinite_scroll_render to determine
	 * a posteriori if a shortcode has been called.
	 */
	function audio_shortcode_infinite() {
		// only try to load if a shortcode has been called
		if( self::$add_script ) {
			$script_url = esc_url_raw( plugins_url( 'js/audio-shortcode.js', __FILE__ ) );

			// if the script hasn't been loaded, load it
			// if the script loads successfully, fire an 'as-script-load' event
			echo <<<SCRIPT
				<script type='text/javascript'>
				//<![CDATA[
				if ( typeof window.audioshortcode === 'undefined' ) {
					var wp_as_js = document.createElement( 'script' );
					wp_as_js.type = 'text/javascript';
					wp_as_js.src = '$script_url';
					wp_as_js.async = true;
					wp_as_js.onload = function() { 
						jQuery( document.body ).trigger( 'as-script-load' ); 
					};
					document.getElementsByTagName( 'head' )[0].appendChild( wp_as_js );
				} else {
					jQuery( document.body ).trigger( 'as-script-load' );
				}
				//]]>
				</script>
SCRIPT;
		}
	}
}

// kick it all off
new AudioShortcode();
