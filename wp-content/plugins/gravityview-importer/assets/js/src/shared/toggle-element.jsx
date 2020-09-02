import classNames from 'classnames/bind';

import appStyles from 'css/app';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );

/**
 * Display option toggle element
 *
 * @param {Object} props
 *
 * @return {ReactElement} JSX markup
 */
function ToggleElement( props ) {
	const { option, title, description, checked, disabled, onChange, confirmation, articleId } = props;

	return (
		<div className={ styles( 'columns', 'toggle-element' ) }>
			<div className={ styles( 'column' ) }>
				<div className={ styles( 'field' ) }>
					<input
						id={ option }
						type="checkbox"
						className={ styles( 'switch', 'is-success' ) }
						disabled={ disabled }
						checked={ checked }
						onChange={ () => {
							const doChange = () => onChange( option, { checked: ! checked } );

							if ( confirmation && ! checked ) {
								const { displayModalDialog, dismissModalDialog, content, confirmButton, cancelButton } = confirmation;

								displayModalDialog( {
									content,
									buttons: [
										{
											label: confirmButton,
											style: styles( 'is-link' ),
											action: () => {
												doChange();
												dismissModalDialog();
											},
										},
										{
											label: cancelButton,
											action: dismissModalDialog,
											dismissModal: true,
										},
									],
								} );
							} else {
								doChange();
							}
						} }
					/>
					<label htmlFor={ option }>
						{ title } { description && <>(<i>{ description }</i>)</> }
						{ articleId && <Beacon articleId={ articleId } beaconType="inline" /> }
					</label>
				</div>
			</div>
		</div>
	);
}

export default ToggleElement;
