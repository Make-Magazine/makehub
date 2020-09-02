/* global wp */

import Dropzone from 'react-dropzone';
import { AJAX } from 'js/helpers/server-requests';
import { Circle } from 'rc-progress';
import { pluginData } from 'js/app';
import classNames from 'classnames/bind';

import appStyles from 'css/app';
import Beacon from 'js/shared/helpscout-beacon';

const styles = classNames.bind( appStyles );
const { Component } = wp.element;

/**
 * Handle CSV upload
 */
export default class Upload extends Component {
	stepRouteId = 'step1Upload';

	state = {
		status: null,
		progress: null,
		file: {},
	};

	/**
	 * Upload CSV using AJAX
	 *
	 * @param {Object} files
	 */
	uploadCSV = ( files ) => {
		// Dismiss previous error notice before uploading
		this.props.context.dismissNotification();

		this.setState( {
			status: 'uploading',
			progress: null,
		} );

		const errorHandler = ( err ) => {
			this.setState(
				{
					status: null,
					progress: null,
				}, () => this.props.context.displayNotification( {
					type: 'error',
					message: `${ pluginData.localization.upload_csv.upload_error } ${ err.message || '' }`, // CSV file failed to upload
				} ),
			);
		};

		AJAX.upload( {
			requestData: {
				upload: files[ 0 ],
				action: pluginData.action_csv_upload,
			},
			progressHandler: ( res ) => {
				this.setState( { progress: res.loaded / res.total * 100 } );
			},
			successHandler: ( body ) => {
				this.goToNextStep( {
					name: files[ 0 ].name,
					serverLocation: body.data.file,
				} );
			},
			errorHandler,
		} );
	};

	/**
	 * Proceed to form selection
	 *
	 * @param {Object} selectedFile
	 */
	goToNextStep( selectedFile ) {
		const { importData, progress } = this.props.context;
		const { file = {} } = importData;
		const { stepData: { step1Upload, step3MapFields } } = progress;

		// If source was previously selected and fields mapped and now a new source was chosen, unset step data for field mapping/etc.
		if ( step1Upload && step3MapFields && selectedFile.serverLocation !== file.serverLocation ) {
			progress.stepData = {
				...progress.stepData,
				step3MapFields: {},
				step4Configure: {},
				step5ImportData: {},
			};
		}

		importData.file = selectedFile;

		this.setState( { file: selectedFile }, () => {
			this.props.context.saveCurrentProgress( this.state, this.stepRouteId );

			this.props.context.setState( { importData, progress }, () => this.props.history.push( this.props.context.routes.step2SelectForm.path ) );
		} );
	}

	/**
	 * Render upload area
	 *
	 * @return {ReactElement} JSX markup
	 */
	render() {
		const { status, progress } = this.state;

		return (
			<section className={ styles( 'upload', 'section' ) }>
				<div className={ styles( 'container' ) }>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<p className={ styles( 'title', 'is-4' ) }>
								{ /* CSV upload */ }
								{ pluginData.localization.select_source.upload_csv }
								<Beacon articleId="5d37c49d2c7d3a2ec4bf53bb" />
							</p>
						</div>
					</div>
					<div className={ styles( 'columns' ) }>
						<div className={ styles( 'column' ) }>
							<Dropzone
								acceptClassName={ styles( 'active' ) }
								accept=".csv"
								multiple={ false }
								disabled={ status !== null }
								onDrop={ this.uploadCSV }
							>
								{ ( { getRootProps, getInputProps, isDragActive } ) => (
									<div { ...getRootProps() } className={ styles( 'box', 'has-text-centered', { active: isDragActive } ) }>
										{ status === 'uploading' ?
											<>
												<div className={ styles( 'upload-progress' ) }>
													<Circle percent={ progress } strokeWidth="5" strokeColor="#46B450" />
												</div>
												<p className={ styles( 'is-size-5' ) }>
													{ /* Do not close this page. Your CSV file data is being added to your site. Please wait while this completes. */ }
													<strong>{ pluginData.localization.upload_csv.do_not_close_page }</strong> { pluginData.localization.upload_csv.csv_data_being_added }
												</p>
											</> :
											<>
												<input
													data-automation-id="csv_upload"
													{ ...getInputProps() }
												/>
												<p className={ styles( 'title', 'is-4' ) }>
													{ /* Drop file or click anywhere to upload. */ }
													{ pluginData.localization.upload_csv.drop_file_or_click_to_upload }
												</p>
												<p className={ styles( 'is-size-5' ) }>
													{ /* Maximum upload file size: X MB. */ }
													{ pluginData.localization.upload_csv.max_upload_size }
												</p>
											</>
										}
									</div>
								) }
							</Dropzone>
						</div>
					</div>
				</div>
			</section>
		);
	}
}
