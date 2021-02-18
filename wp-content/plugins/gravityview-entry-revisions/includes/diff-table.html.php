<?php
/**
 * @global string $revision_title Title of the table ("Entry modified by {user} {time ago}.")
 * @global string $nonce_field
 * @global bool $user_can_edit_entry Can the user edit the current entry
 * @global string $url_cancel URL to return to Entry Detail
 */
?>
<table class="diff gv-entry-diff">

	<caption><?php echo esc_html( $revision_title ); ?></caption>

	<colgroup><col class='content diffsplit' style='width: 20%;' /><col class='content diffsplit left' /><col class='content diffsplit middle' /><col class='content diffsplit right' /></colgroup>

	<thead>
	<tr class="diff-sub-title">
		<th scope="col" id="diff-col-field" class="screen-reader-text"><?php esc_html_e( 'Field', 'gravityview-entry-revisions' ); ?></th>
		<th scope="col" id="diff-col-revision"><?php esc_html_e( 'Revision Value', 'gravityview-entry-revisions' ); ?>
            <div class="diff-date" title="<?php echo esc_attr( $revision_title ); ?>"><?php echo esc_html( $date ); ?></div>
			<?php if( $restore_links && 1 < sizeof( $diffs ) ) { ?>
				<label class="screen-reader-text" for="toggle_revisions_tfoot"><?php esc_html_e( 'Toggle all changes', 'gravityview-entry-revisions' ); ?></label>
				<input type="checkbox" id="toggle_revisions_tfoot" class="toggle-all-revisions" title="<?php esc_html_e( 'Toggle all changes', 'gravityview-entry-revisions' ); ?>" />
			<?php } ?>
		</th>
		<th scope="col" id="diff-col-gap"></th>
		<th scope="col" id="diff-col-current"><?php esc_html_e( 'Current Value', 'gravityview-entry-revisions' ); ?></th>
	</tr>
	</thead>

	<?php
	if ( $restore_links ) {
		?>
		<tfoot>
		<tr>
			<?php
			if( ! $user_can_edit_entry ) {
				?>
				<td colspan="4"><a href="<?php echo esc_url( $url_cancel ); ?>" class="button button-secondary button-large alignleft" title="<?php esc_attr_e( 'Cancel and do not modify the current entry', 'gravityview-entry-revisions' ); ?>"><?php esc_html_e( 'Return to Entry', 'gravityview-entry-revisions' ); ?></a></td>
				<?php
			} else {
				?>
				<td></td>
				<td>
					<input type="submit" class="button button-primary button-large alignleft hide-if-js" value="<?php echo esc_attr( _n( 'Restore This Value', 'Restore These Values', count( $diffs ), 'gravityview-entry-revisions' ) ) ?>" />
					<?php if( 1 < sizeof( $diffs ) ) { ?>
						<label class="screen-reader-text" for="toggle_revisions_tfoot"><?php esc_html_e( 'Toggle all changes', 'gravityview-entry-revisions' ); ?></label>
						<input type="checkbox" id="toggle_revisions_tfoot" class="toggle-all-revisions alignright" title="<?php esc_html_e( 'Toggle all changes', 'gravityview-entry-revisions' ); ?>" />
					<?php } ?>
				</td>
				<td></td>
				<td><a href="<?php echo esc_url( $url_cancel ); ?>" class="button button-secondary button-large alignright" title="<?php esc_attr_e( 'Cancel and do not modify the current entry', 'gravityview-entry-revisions' ); ?>"><?php esc_html_e( 'Cancel', 'gravityview-entry-revisions' ); ?></a></td>
				<?php
			}


			printf( '<input type="hidden" name="revision" value="%d" />', (int) $revision['id'] );

			echo $nonce_field;

			?>
		</tr>
		</tfoot>
		<?php
	}
	?>
	<tbody>
	<?php
		echo implode( "\n", $diffs );
	?>
	</tbody>
</table>