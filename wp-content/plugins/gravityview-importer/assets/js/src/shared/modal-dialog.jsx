/* global wp */

import _ from 'lodash';
import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Display modal dialog box with confirmation
 */

export default class ModalDialog extends Component {
	dismissKeyCode = 27;

	dismissButton = ( () => {
		let dismissButton = _.find( this.props.buttons, { dismissModal: true } ) || {};

		// if multiple dismiss buttons are found, take the first one
		if ( _.isArray( dismissButton ) ) {
			dismissButton = dismissButton.shift();
		}

		return dismissButton;
	} )();

	/**
	 * Dismiss modal on key press
	 *
	 * @param {Object} event Keydown event
	 */
	handleKeyDown( event ) {
		if ( event.keyCode === this.dismissKeyCode && this.dismissButton.action ) {
			this.dismissButton.action();
		}
	}

	/**
	 * Add event listener on component mount
	 */
	componentDidMount() {
		document.addEventListener( 'keydown', this.handleKeyDown.bind( this ) );
	}

	/**
	 * Remove event listener on component unmount
	 */
	componentWillUnmount() {
		document.removeEventListener( 'keydown', this.handleKeyDown.bind( this ) );
	}

	/**
	 * Render modal dialog
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		return (
			<section data-automation-id={ this.props.automationId } className={ styles( 'modal-dialog', 'section' ) }>
				<div className={ styles( 'modal', 'is-active' ) }>
					<div className={ styles( 'modal-background' ) } />
					<div className={ styles( 'modal-card' ) }>
						<section className={ styles( 'modal-card-body' ) }>
							{ this.props.content }
						</section>
						{ this.props.buttons &&
						<footer className={ styles( 'modal-card-foot' ) }>
							{ this.props.buttons.map( ( button, index ) =>
								<button
									ref={ button.ref }
									key={ `model-dialog-button-${ index }` }
									disabled={ button.disabled }
									className={ styles( 'button', button.style ) }
									data-automation-id={ button.automationId }
									onClick={ button.action }
									tabIndex={ 0 }
								>
									{ button.label }
								</button>,
							) }
						</footer>
						}
					</div>
				</div>
			</section>
		);
	}
}
