/* global wp */

import { Link } from 'react-router-dom';
import { pluginData } from 'js/app';
import classNames from 'classnames/bind';

import appStyles from 'css/app';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );
const { Component, Fragment } = wp.element;

/**
 * Display import source selection: CSV upload, Google Sheets and FTP
 */
export default class SelectSource extends Component {
	routesLinkData = [
		{
			id: 'step1Upload',
			label: pluginData.localization.select_source.upload_csv, // Upload CSV
			articleId: '5d37c49d2c7d3a2ec4bf53bb',
		},
		{
			id: 'step1Google',
			label: pluginData.localization.select_source.import_from_gsheets, // Import from Google Sheets
			articleId: null,
		},
		{
			id: 'step1Ftp',
			label: pluginData.localization.select_source.connect_to_ftp, // Connect to FTP
			articleId: null,
		},
	];

	/**
	 * Render source selection area
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		return (
			<section className={ styles( 'select-source', 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ /* How would you like to import data? */ }
								{ pluginData.localization.select_source.import_source }
							</p>
						</div>
					</div>
					<div className={ styles( 'columns' ) }>
						{ this.routesLinkData.map( ( data, index ) => ! this.props.context.routes[ data.id ].inactive && (
							<Fragment key={ `source-${ data.id }` }>
								<div className={ styles( 'column', 'is-3' ) }>
									<Link className={ styles( 'no-underline' ) } to={ this.props.context.routes[ data.id ].path }>
										<div className={ styles( 'box', 'is-flex' ) }>
											<figure className={ styles( 'image', 'is-64x64' ) } />
											<p className={ styles( 'is-size-5' ) }>
												{ data.label }
												{ data.articleId && <Beacon articleId={ data.articleId } /> }
											</p>
										</div>
									</Link>
								</div>
								{ /* Add gap between columns (except for the last one) by adding an empty column */ }
								{ this.routesLinkData.length !== index && <div className={ styles( 'column', 'is-1' ) } /> }
							</Fragment>
						) ) }
					</div>
				</div>
			</section>
		);
	}
}
