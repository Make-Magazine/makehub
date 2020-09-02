import classNames from 'classnames/bind';

import appStyles from 'css/app';

const styles = classNames.bind( appStyles );

/**
 * Display HelpScout Beacon tooltip
 *
 * @param {Object} props
 *
 * @return {ReactElement} JSX markup
 */
function Beacon( props ) {
	const { articleId, title, automationId, href = '#', beaconType = 'inline' } = props;

	let type = beaconType;

	// 'inline' type does not work in IE, default to 'sidebar'
	if ( type && !! window.MSInputMethodContext && !! document.documentMode ) {
		type = 'sidebar';
	}

	const beaconProp = { [ type ? `data-beacon-article-${ type }` : 'data-beacon-article' ]: articleId };

	return Beacon && (
		<a
			href={ href }
			className={ styles( 'helpscout-beacon' ) }
			data-automation-id={ automationId }
			title={ title }
			{ ...beaconProp }
			role="button"
			tabIndex={ 0 }
			onClick={ ( e ) => e.preventDefault() }
		>
			{ title }
		</a>
	);
}

export default Beacon;
