import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

/**
 * Display notification (error, success, info, etc.)
 *
 * @param {Object} props
 *
 * @return {ReactElement} JSX markup
 */
function Notification( props ) {
	const { type, message, onDismiss } = props;

	// Map notification types to CSS styles
	const availableTypes = {
		error: 'is-danger',
		warning: 'is-warning',
		success: 'is-success',
		info: 'is-info',
	};

	return (
		<section data-automation-id={ `${ type }_notification` } className={ styles( 'section' ) }>
			<div className={ styles( 'container' ) }>
				<div className={ styles( 'columns' ) }>
					<div className={ styles( 'column' ) }>
						<div className={ styles( 'message', availableTypes[ type ] ) }>
							<div className={ styles( 'message-body' ) }>
								<button className={ styles( 'delete' ) } onClick={ () => onDismiss() } />
								{ message }
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	);
}

export default Notification;
