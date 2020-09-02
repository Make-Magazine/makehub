import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

/**
 * Display notice for network errors
 *
 * @param {Object} props
 *
 * @return {ReactElement} JSX markup
 */
function ErrorNotice( props ) {
	const { error, description, buttons, automationId } = props;

	return (
		<section data-automation-id={ automationId } className={ styles( 'section', 'error-notice' ) }>
			<div className={ styles( 'container' ) }>
				<div className={ styles( 'hero', 'is-medium' ) }>
					<div className={ styles( 'hero-body' ) }>
						<div className={ styles( 'container', 'has-text-centered' ) }>
							<div className={ styles( 'title' ) }>
								{ error }
							</div>
							<div className={ styles( 'is-size-5', 'block' ) }>
								{ description }
							</div>
							{ buttons && <div className={ styles( 'buttons', 'has-text-centered', 'block' ) }>
								{ buttons.map( ( button ) =>
									<span
										key={ `button-${ button.label }` }
										className={ styles( 'button', 'is-link' ) }
										data-automation-id={ button.automationId }
										role="button"
										tabIndex={ 0 }
										onClick={ button.action }
										onKeyDown={ ( e ) => e.keyCode === 13 ? button.action() : null }
									>
										{ button.label }
									</span>,
								) }
							</div>
							}
						</div>
					</div>
				</div>
			</div>
		</section>
	);
}

export default ErrorNotice;
