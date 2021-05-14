<?php
$header         = array(
	'logo_alt' => '',
	'logo_url' => '',
	'text'     => '',
	'text_url' => '',
);
$header['logo'] = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'login_logo' );
if ( ! empty( $header['logo'] ) ) {
	$header['logo_alt'] = get_post_meta( $header['logo'], '_wp_attachment_image_alt', true );
	/**
	 * Filters Focus mode header logo alternative text.
	 * This filter will be called only if there is a logo set in LearnDash plugin settings.
	 *
	 * @param string $logo_alt  Header logo alternative text.
	 * @param int    $course_id Course ID.
	 * @param int    $user_id   User ID.
	 */
	$header['logo_alt'] = apply_filters( 'learndash_focus_header_logo_alt', $header['logo_alt'], $course_id, $user_id );
	/**
	 * Filters Focus mode header logo URL.
	 * This filter will be called only if there is a logo set in LearnDash plugin settings.
	 *
	 * @param string $logo_url  Header logo URL.
	 * @param int    $course_id Course ID.
	 * @param int    $user_id   User ID.
	 */
	$header['logo_url'] = apply_filters( 'learndash_focus_header_logo_url', get_home_url(), $course_id, $user_id );
}

		$header_element = '';
	if ( ! empty( $header['logo'] ) ) {
		if ( ! empty( $header['logo_url'] ) ) {
			$header_element .= '<a href="' . esc_url( $header['logo_url'] ) . '">';
		}
		$header_element .= '<img src="' . esc_url( wp_get_attachment_url( $header['logo'] ) ) . '" alt="' . esc_html( $header['logo_alt'] ) . '" />';
		if ( ! empty( $header['logo_url'] ) ) {
			$header_element .= '</a>';
		}
	} else {
		if ( ! empty( $header['text'] ) ) {
			if ( ! empty( $header['text_url'] ) ) {
				$header_element .= '<a href="' . esc_url( $header['text_url'] ) . '">';
			}
			$header_element .= esc_html( $header['text'] );
			if ( ! empty( $header['text_url'] ) ) {
				$header_element .= '</a>';
			}
		}
	}

		/**
		 * Filters Focus mode Header element markup.
		 *
		 * @param string $header_element Focus mode header element markup.
		 * @param array  $header         Array of header element details keyed logo_alt, logo_url, text, text_url.
		 * @param int    $course_id      Course ID.
		 * @param int    $user_id        User ID.
		 */
		$header_element = apply_filters( 'learndash_focus_header_element', $header_element, $header, $course_id, $user_id );
		echo '<div class="ldvc-focus-sidebar-logo">' . $header_element . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs HTML for the header element
