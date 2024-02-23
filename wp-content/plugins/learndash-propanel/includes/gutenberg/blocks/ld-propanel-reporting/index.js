/**
 * LearnDash ProPanel Reporting Block
 *
 * @package ProPanel
 * @since 2.2.0
 */

/**
 * ProPanel block functions
 */

/**
 * Internal block libraries
 */
const { __, _x, sprintf } = wp.i18n;
const {
	registerBlockType,
} = wp.blocks;

const {
	useBlockProps,
	InspectorControls
} = wp.blockEditor;

const {
	PanelBody,
	ToggleControl,
	Disabled,
	SelectControl,
	__experimentalNumberControl,
} = wp.components;

const NumberControl = __experimentalNumberControl;

import { PostDropdown } from '../lib/post-dropdown';
import { UserDropdown } from '../lib/user-dropdown';

import ServerSideRender from '@wordpress/server-side-render';

const title = _x('ProPanel Reporting Block', 'ld_propanel');

registerBlockType(
	'ld-propanel/ld-propanel-reporting',
	{
		title: title,
		description: __('Displays the ProPanel Reporting information for User and Progress.', 'ld_propanel'),
		icon: 'media-spreadsheet',
		category: 'ld-propanel-blocks',
		keywords: [ 'reporting' ],
		supports: {
			customClassName: false,
		},
		attributes: {
			preview_show: {
				type: 'boolean',
				default: true
			},
			filter_groups: {
				type: 'int',
				default: 0
			},
			filter_courses: {
				type: 'int',
				default: 0
			},
			filter_users: {
				type: 'int',
				default: 0
			},
			filter_status: {
				type: 'string',
				default: ''
			},
			per_page: {
				type: 'int',
				default: 0
			}
		},
		example: {
			attributes: {
				preview_show: true,
				filter_groups: 0,
				filter_courses: 0,
				filter_users: 0,
				filter_status: '',
				per_page: 0,
			},
		},
		edit: function( props ) {
			const {
				attributes: {
					preview_show,
					filter_groups,
					filter_courses,
					filter_users,
					filter_status,
					per_page
				},
				setAttributes
			} = props;
			const blockProps = useBlockProps();

			const panel_settings = (

				<PanelBody
					title={ __( 'Settings', 'ld_propanel' ) }
					initialOpen={true}
				>

					<PostDropdown
						key="filter_groups"
						label={
							sprintf(
								// translators: placeholder: Filter Courses.
								__(
									'Filter %s',
									'ld_propanel'
								),
								learndash.customLabel.get( 'groups' )
							)
						}
						placeholder={
							sprintf(
								// translators: placeholder: Type to search for a Course...
								__(
									'Type to search for a %s...',
									'ld_propanel'
								),
								learndash.customLabel.get( 'group' )
							)
						}
						additional={
							{
								postType: 'groups',
							}
						}
						value={ filter_groups || '' }
						onChange={ ( filter_groups ) => setAttributes( { filter_groups } ) }
					/>

					<PostDropdown
						key="filter_courses"
						label={
							sprintf(
								// translators: placeholder: Filter Courses.
								__(
									'Filter %s',
									'ld_propanel'
								),
								learndash.customLabel.get( 'courses' )
							)
						}
						placeholder={
							sprintf(
								// translators: placeholder: Type to search for a Course...
								__(
									'Type to search for a %s...',
									'ld_propanel'
								),
								learndash.customLabel.get( 'course' )
							)
						}
						additional={
							{
								postType: 'sfwd-courses',
							}
						}
						value={ filter_courses || '' }
						onChange={ ( filter_courses ) => setAttributes( { filter_courses } ) }
					/>

					<UserDropdown
						key="filter_users"
						label={ __( 'Filter Users', 'ld_propanel' ) }
						placeholder={ __( 'Type to search for a User...', 'ld_propanel' ) }
						value={ filter_users || '' }
						onChange={ ( filter_users ) => setAttributes( { filter_users } ) }
					/>

					<SelectControl
						key='filter_status'
						label={
							sprintf(
								// translators: placeholder: Filter Course Status.
								__(
									'Filter %s Status',
									'ld_propanel'
								),
								learndash.customLabel.get( 'course' )
							)
						}
						value={filter_status}
						onChange={ ( filter_status ) => setAttributes( { filter_status } ) }
						options={ [
							{
								label: __( 'All Statuses', 'ld_propanel' ),
								value: '',
							},
							{
								label: __( 'Not Started', 'ld_propanel' ),
								value: 'not-started',
							},
							{
								label: __( 'In Progress', 'ld_propanel' ),
								value: 'in-progress',
							},
							{
								label: __( 'Completed', 'ld_propanel' ),
								value: 'completed',
							},
						] }
					/>

					<NumberControl
						key="per_page"
						label={ __( 'Per Page', 'ld_propanel' ) }
						value={ per_page || '' }
						onChange={ ( per_page ) => setAttributes( { per_page } ) }
					/>

				</PanelBody>

			);

			const panel_preview = (
				<PanelBody
					title={__('Preview', 'ld_propanel')}
					initialOpen={false}
				>
					<ToggleControl
						label={__('Show Preview', 'ld_propanel')}
						checked={!!preview_show}
						onChange={preview_show => setAttributes({ preview_show })}
					/>
				</PanelBody>
			);

			const inspectorControls = (
				<InspectorControls>
					{ panel_settings }
					{ panel_preview }
				</InspectorControls>
			);

			function do_serverside_render( attributes ) {
				if ( attributes.preview_show == true ) {

					let template = 'course-reporting',
						filters = {
							type: 'course',
							id: '',
							courseStatus: attributes.filter_status,
							groups: attributes.filter_groups || '',
							courses: attributes.filter_courses || '',
							users: attributes.filter_users || '',
							reporting_pager: {
								per_page: attributes.per_page || '',
								current_page: 1,
							}
						};

					if ( filters.groups ) {
						template = 'group-reporting';
						filters.type = 'group';
						filters.id = filters.groups;
					} else if ( filters.courses ) {
						filters.id = filters.courses;
					} else if ( filters.users ) {
						template = 'user-reporting';
						filters.type = 'user';
						filters.id = filters.users;
					}

					return (
						<div className={ 'learndash-block-inner' }>
							<div data-ld-widget-type={ 'reporting' } className={ 'ld-propanel-widget ld-propanel-widget-reporting' }>
								<ServerSideRender
									block="ld-propanel/ld-propanel-reporting"
									attributes={ attributes }
									// GET is the default, but just to help ensure future-proofing
									httpMethod='GET'
									urlQueryArgs={
										// Pass attributes through to the GET request at the top-level to better re-use the existing Ajax logic for Shortcodes
										Object.assign(
											{
												template: template,
												filters: filters,
												container_type: 'widget'
											},
											attributes
					 					)
									}
								/>
							</div>
						</div>
					);

				} else {
					// translators: %s is the title for the Block.
					return __( 'Toggle the Preview setting in the sidebar to see the %s in the editor.', 'ld_propanel' ).replace( '%s', title );
				}

			}

			return (
				<div { ...blockProps }>
					{ inspectorControls }
					<Disabled>
						{ do_serverside_render( props.attributes ) }
					</Disabled>
				</div>
			);
		},
		save: props => {
			// Delete meta from props to prevent it being saved.
			delete (props.attributes.meta);
		}
	},
);
