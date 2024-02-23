/**
 * LearnDash ProPanel Filters Block
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
	InspectorControls,
	RichText
} = wp.blockEditor;

const {
	PanelBody
} = wp.components;

const {
	useRef
} = wp.element;

const title = _x('Full Report Link', 'ld_propanel');

registerBlockType(
	'ld-propanel/ld-propanel-link',
	{
		title: title,
		description: __('Use this block to place a link on the page for the full ProPanel Report.', 'ld_propanel'),
		icon: 'welcome-widgets-menus',
		category: 'ld-propanel-blocks',
		keywords: [ 'link' ],
		supports: {
			customClassName: false,
		},
		attributes: {
			content: {
				type: 'string',
				default: __( 'Show ProPanel Full Page', 'ld_propanel' ),
				source: "html",
				__experimentalRole: "content"
			},
		},
		example: {
			attributes: {
				content: __( 'Show ProPanel Full Page', 'ld_propanel' )
			}
		},
		edit: function( props ) {
			const {
				attributes: {
					content
				},
				setAttributes
			} = props;

			const ref = useRef( null );

			const handleClick = () => {
				ref.current.focus();
			};

			const blockProps = useBlockProps( {
				onClick: handleClick
			} );

			const inspectorControls = (
				<InspectorControls>
					<PanelBody
						title=""
						initialOpen={ true }
					>
						{ __( "To change the link's text, click on it in the editor and change it to your preference.", 'ld_propanel' ) }
					</PanelBody>
				</InspectorControls>
			);

			return (
				<div { ...blockProps }>
					{ inspectorControls }
					<div className={ 'learndash-block-inner' }>
						<div data-ld-widget-type={ 'link' } className={ 'ld-propanel-widget ld-propanel-widget-link' }>
							<RichText
								identifier="content"
								tagName="a"
								value={ content }
								ref={ ref }
								onFocus={ ( event ) => event.currentTarget.setSelectionRange( event.currentTarget.value.length, event.currentTarget.value.length ) }
								onChange={ ( content ) => setAttributes( { content } ) }
								allowedFormats={
									// All the default Formats outside of core/link, since you can't put a link within a link.
									[
										'core/bold',
										'core/code',
										'core/italic',
										'core/image',
										'core/strikethrough',
										'core/underline',
										'core/subscript',
										'core/superscript',
										'core/keyboard'
									]
								}
							/>
						</div>
					</div>
				</div>
			);
		},
		save: function( props ) {
			const {
				attributes: {
					content
				},
			} = props;

			return (
				<RichText.Content value={ content } />
			);
		}
	},
);
