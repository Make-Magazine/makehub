import escapeStringRegexp from 'escape-string-regexp';
import _ from 'lodash';
import { pluginData } from 'js/app';
import { addPeriodToString, extractJSON } from 'js/helpers/string-manipulations';

/**
 * Array of error codes used for network requests
 */
const availableErrorCodes = [
	'failed_network_request',
	'empty_response',
	'invalid_response',
	'unknown_error',
	'wp_error',
	'api_error',
];

/**
 * Import API actions
 */
export const API = {
	update: ( { requestData, responseHandler, successHandler, errorHandler } ) => (
		makeRequest( {
			url: `${ pluginData.api_url }/batches/${ requestData.id }`,
			method: 'put',
			requestData,
			responseHandler,
			successHandler,
			errorHandler,
			apiRequest: true,
		} )
	),

	process: ( { requestData, responseHandler, successHandler, errorHandler } ) => (
		makeRequest( {
			url: `${ pluginData.api_url }/batches/${ requestData.id }/process`,
			method: 'get',
			responseHandler,
			successHandler,
			errorHandler,
			apiRequest: true,
		} )
	),

	get: ( { requestData, responseHandler, successHandler, errorHandler } ) => (
		makeRequest( {
			url: `${ pluginData.api_url }/batches/${ requestData.id }`,
			method: 'get',
			responseHandler,
			successHandler,
			errorHandler,
			apiRequest: true,
		} )
	),

	create: ( { requestData, responseHandler, successHandler, errorHandler } ) => (
		makeRequest( {
			url: `${ pluginData.api_url }/batches/`,
			method: 'post',
			requestData,
			responseHandler,
			successHandler,
			errorHandler,
			apiRequest: true,
		} )
	),

	getErrorReport: ( { requestData, responseHandler, successHandler, errorHandler, csv } ) => (
		makeRequest( {
			url: `${ pluginData.api_url }/batches/${ requestData.id }/errors${ csv ? '.csv' : '' }`,
			method: 'get',
			responseType: csv ? 'csv' : 'json',
			responseHandler,
			successHandler,
			errorHandler,
			apiRequest: true,
		} )
	),
};

/*
 * Non-API server requests handled by the UI backend class
 *
 * @see GV\Import_Entries\UI::__construct()
 */
export const AJAX = {
	post: ( { requestData, responseHandler, successHandler, errorHandler } ) => {
		return makeRequest( {
			url: window.ajaxurl,
			method: 'post',
			requestData: _.assign( {}, requestData, { nonce: pluginData.ajax_nonce } ),
			responseHandler,
			successHandler,
			errorHandler,
			ajaxRequest: true,
		} );
	},

	upload: ( { requestData, progressHandler, successHandler, errorHandler } ) => {
		const doRequest = new Promise( ( resolve, reject ) => {
			const xhr = new XMLHttpRequest();

			xhr.onerror = () => {
				const code = 'unknown_error';

				reject( {
					code,
					message: addPeriodToString( xhr.statusText || pluginData.localization.network_errors[ code ] ),
					request: xhr,
				} );
			};
			xhr.upload.onprogress = progressHandler;
			xhr.onload = ( res ) => {
				const body = extractJSON( res.target.response );
				const wpError = getWPError( body );

				const errorResponse = {
					request: xhr,
				};

				if ( wpError ) {
					errorResponse.code = wpError;
					reject( _.assign( {}, errorResponse, wpError ) );
				}

				if ( res.target.status >= 200 && res.target.status <= 300 && ! _.isEmpty( body ) ) {
					resolve( body );
				}

				if ( res.target.status >= 200 && res.target.status <= 300 && _.isEmpty( body ) ) {
					errorResponse.code = 'empty_response';
					errorResponse.message = pluginData.localization.network_errors[ errorResponse.errorCode ];
				} else if ( wpError ) {
					errorResponse.code = wpError;
				} else {
					errorResponse.code = 'failed_network_request';
					errorResponse.message = addPeriodToString( xhr.statusText || pluginData.localization.network_errors[ errorResponse.errorCode ] );
				}

				reject( errorResponse );
			};

			xhr.open( 'post', window.ajaxurl );
			xhr.send(
				createFormDataObject(
					_.assign( {}, requestData, { nonce: pluginData.ajax_nonce } ), // add nonce to request data
				),
			);
		} );

		doRequest
			.then( ( body ) => ( successHandler ) ? successHandler( body ) : body )
			.catch( ( err ) => ( errorHandler ) ? errorHandler( err ) : err );
	},

};

/**
 * Perform AJAX server request
 *
 * @param {string} url Request URL
 * @param {string} method Request method (e.g., 'post', 'put', 'get')
 * @param {Object} requestData Request data object
 * @param {string} expectedResponseType Response type
 * @callback responseHandler Callback executed to validate response; 'false' return throws an error
 * @callback successHandler Callback executed after response is validated
 * @callback errorHandler Callback executed when error is thrown
 */
const makeRequest = ( { url, method, requestData, responseType, responseHandler, successHandler, errorHandler, apiRequest, ajaxRequest } ) => {
	const isJsonExpected = ! responseType || responseType === 'json';
	const requestOptions = {
		method,
		headers: ( apiRequest ) ?
			{ 'X-WP-Nonce': pluginData.api_nonce } : // set nonce in headers for API calls
			{},
	};
	let response = {};
	let networkError = false;

	if ( requestData ) {
		if ( ajaxRequest ) {
			requestData.nonce = pluginData.ajax_nonce; // add nonce to request data for AJAX calls
		}

		if ( requestOptions.method === 'post' ) {
			requestOptions.body = createFormDataObject( requestData );
		}

		if ( requestOptions.method === 'put' ) {
			requestOptions.headers = _.assign( {}, requestOptions.headers, { 'Content-Type': 'application/json; charset=utf-8' } );
			requestOptions.body = JSON.stringify( requestData );
		}
	}

	fetch( url, requestOptions )
		.then( ( res ) => {
			// verify that response status is in the 200-299 range
			response = res.clone(); // make a copy of the original response object before modifications take place

			if ( ! res.ok ) {
				networkError = true;
			}

			return res;
		} )
		.then( ( res ) => res.text() ) // decode body
		.then( ( res ) => {
			// process body
			const body = isJsonExpected ? extractJSON( res ) : res;
			const errorResponse = {};

			// update response object with body
			response = _.extend( {}, response, { body } );

			// handle 400-500 errors
			if ( networkError ) {
				if ( _.get( body, 'code' ) && body.code.match( /gravityview\/import\/errors/ ) ) {
					errorResponse.code = 'api_error';
				} else {
					errorResponse.code = 'failed_network_request';
				}

				errorResponse.message = _.get( body, 'message' ) ? body.message : pluginData.localization.network_errors[ errorResponse.code ];

				throw errorResponse;
			}

			// handle empty response
			if ( _.isEmpty( body ) ) {
				const code = 'empty_response';

				throw {
					code,
					message: pluginData.localization.network_errors[ code ],
				};
			}

			// validate response
			if ( responseHandler && ! responseHandler( body, body ) ) {
				const code = 'invalid_response';

				throw {
					code,
					message: pluginData.localization.network_errors[ code ],
				};
			}

			return ( successHandler ) ? successHandler( body ) : body;
		} )
		.catch( ( err ) => {
			const wpError = getWPError( response.body );
			let errorResponse = {
				message: err.message,
				code: err.code,
				response,
			};

			if ( wpError ) {
				errorResponse = _.assign( {}, errorResponse, wpError );
			} else if ( errorResponse.message && errorResponse.message.match( new RegExp( escapeStringRegexp( 'failed to fetch' ), 'gi' ) ) ) {
				errorResponse.code = 'failed_network_request';
				errorResponse.message = pluginData.localization.network_errors[ errorResponse.code ];
			}

			if ( ! _.includes( availableErrorCodes, errorResponse.code ) ) {
				errorResponse.code = 'unknown_error';
				errorResponse.message = pluginData.localization.network_errors[ errorResponse.code ];
			}

			errorResponse.message = addPeriodToString( errorResponse.message );

			return ( errorHandler ) ? errorHandler( errorResponse ) : errorResponse;
		} );
};

/**
 * Extract WP error from server response
 *
 * @param {Object} responseBody Server response body
 *
 * @return {false|Object} Error object with code & message, or false
 */
const getWPError = ( responseBody ) => {
	const code = 'wp_error';

	return ( responseBody && responseBody.hasOwnProperty( 'success' ) && responseBody.hasOwnProperty( 'data' ) && responseBody.success === false ) ?
		{
			code,
			message: _.get( responseBody, 'data[0].message' ) || pluginData.localization.network_errors[ code ],
		} :
		false;
};

/**
 * Create FormData object from 'key:value' pair object
 *
 * @param {Object} data
 *
 * @return {Object} FormData
 */
const createFormDataObject = ( data ) => {
	const formData = new FormData();

	Object.keys( data ).forEach( ( key ) => formData.append( key, data[ key ] ) );

	return formData;
};
