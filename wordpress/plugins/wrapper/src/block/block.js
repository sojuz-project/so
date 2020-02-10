/**
 * BLOCK: wrapper
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
import { InnerBlocks, BlockControls, InspectorControls, PlainText, AlignmentToolbar, ColorPalette } from '@wordpress/block-editor';
import { DropdownMenu, Toolbar, PanelBody, ToggleControl } from '@wordpress/components';
import { defaultTemplates } from './schemas/defaultTemplates';
/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'cgb/block-wrapper', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'wrapper - CGB Block' ), // Block title.
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'wrapper — CGB Block' ),
		__( 'CGB Example' ),
		__( 'create-guten-block' ),
	],
	supports: {
		align: [ 'full', 'wide' ],
	},
	attributes: {
		backgroundColor: {
			type: 'string',
			default: 'initial',
		},
		align: {
			type: 'string',
			default: '',
		},
		wrapperType: {
			type: 'string',
			default: 'editor-code',
		},
		tagName: {
			type: 'string',
			default: 'section',
		},
		blockName: {
			type: 'string',
			default: 'unselected',
		},
		query: {
			type: 'string',
			default: '',
		},
		template: {
			type: 'string',
			default: '',
		},
		queryAlias: {
			type: 'string',
			default: '',
		},
		component: {
			type: 'string',
			default: '',
		},
		queryVariables: {
			type: 'bollean',
			default: false,
		},
		slider: {
			type: 'bollean',
			default: false,
		},

	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: ( props ) => {
		const { attributes: {
			align,
			backgroundColor,
			tagName,
			blockName,
			wrapperType,
			query,
			queryAlias,
			template,
			queryVariables,
			component,
			slider,
		}, className, setAttributes } = props;

		// Creates a <p class='wp-block-cgb-block-wrapper'></p>.
		return (
			<div className={ className } style={ { backgroundColor: backgroundColor } }>
				<BlockControls>
					<AlignmentToolbar
						value={ align }
						onChange={ setAttributes( { align } ) }
					/>
					<Toolbar>
						<DropdownMenu
							icon={ wrapperType }
							label="Select block type"
							controls={ [
								{
									title: 'Query',
									icon: 'excerpt-view',
									onClick: () => setAttributes( { wrapperType: 'excerpt-view' } ),
								},
								{
									title: 'Tag name',
									icon: 'editor-code',
									onClick: () => setAttributes( { wrapperType: 'editor-code' } ),
								},
							] }
						/>
						{ wrapperType != 'editor-code' && <DropdownMenu
							icon="editor-table"
							label="Select template"
							controls={ [
								{
									title: 'PostCard',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'PostCard',
										template: JSON.stringify( defaultTemplates.PostCard.template ),
										query: defaultTemplates.PostCard.query,
									} ),
								},
								{
									title: 'ProductCard',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'ProductCard',
										template: JSON.stringify( defaultTemplates.ProductCard.template ),
										query: defaultTemplates.ProductCard.query,
									} ),
								},
								{
									title: 'CategoryCard',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'CategoryCard',
										template: JSON.stringify( defaultTemplates.CategoryCard.template ),
										query: defaultTemplates.CategoryCard.query,
									} ),
								},
								{
									title: 'SinglePost',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'SinglePost',
										template: JSON.stringify( defaultTemplates.SinglePost.template ),
										query: defaultTemplates.SinglePost.query,
									} ),
								},
								{
									title: 'SingleProduct',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'SingleProduct',
										template: JSON.stringify( defaultTemplates.SingleProduct.template ),
										query: defaultTemplates.SingleProduct.query,
									} ),
								},
								{
									title: 'RelatedProducts',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'RelatedProducts',
										template: JSON.stringify( defaultTemplates.RelatedProducts.template ),
										query: defaultTemplates.RelatedProducts.query,
									} ),
								},
								{
									title: 'Search',
									icon: 'editor-table',
									onClick: () => setAttributes( {
										blockName: 'Search',
										template: JSON.stringify( defaultTemplates.Search.template ),
										query: defaultTemplates.Search.query,
									} ),
								},

							] }
						/> }
					</Toolbar>

				</BlockControls>

				<InspectorControls>

					{ wrapperType == 'excerpt-view' &&	<PanelBody
						title={ __( 'Query properties' ) }
						initialOpen={ true }
					>
						<div className="flex">
							<label>Template</label>
							<DropdownMenu
								icon="editor-table"
								label="Select template"
								controls={ [
									{
										title: 'PostCard',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'PostCard',
											template: JSON.stringify( defaultTemplates.PostCard.template ),
											query: defaultTemplates.PostCard.query,
										} ),
									},
									{
										title: 'ProductCard',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'ProductCard',
											template: JSON.stringify( defaultTemplates.ProductCard.template ),
											query: defaultTemplates.ProductCard.query,
										} ),
									},
									{
										title: 'CategoryCard',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'CategoryCard',
											template: JSON.stringify( defaultTemplates.CategoryCard.template ),
											query: defaultTemplates.CategoryCard.query,
										} ),
									},
									{
										title: 'SinglePost',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'SinglePost',
											template: JSON.stringify( defaultTemplates.SinglePost.template ),
											query: defaultTemplates.SinglePost.query,
										} ),
									},
									{
										title: 'SingleProduct',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'SingleProduct',
											template: JSON.stringify( defaultTemplates.SingleProduct.template ),
											query: defaultTemplates.SingleProduct.query,
										} ),
									},
									{
										title: 'RelatedProducts',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'RelatedProducts',
											template: JSON.stringify( defaultTemplates.RelatedProducts.template ),
											query: defaultTemplates.RelatedProducts.query,
										} ),
									},
									{
										title: 'Search',
										icon: 'editor-table',
										onClick: () => setAttributes( {
											blockName: 'Search',
											template: JSON.stringify( defaultTemplates.Search.template ),
											query: defaultTemplates.Search.query,
										} ),
									},

								] }
							/>
						</div>
						<PlainText
							className="plain-text"
							value={ template }
							onChange={ ( template ) => setAttributes( { template } ) }
						/>
						<label>Query body </label>
						<PlainText
							className="plain-text"
							value={ query }
							onChange={ ( query ) => setAttributes( { query } ) }
						/>
						<label>Query alias </label>
						<PlainText
							className="plain-text"
							value={ queryAlias }
							onChange={ ( queryAlias ) => setAttributes( { queryAlias } ) }
						/>
						<label>Component </label>
						<PlainText
							className="plain-text"
							value={ component }
							onChange={ ( component ) => setAttributes( { component } ) }
						/>
						<hr></hr>
						<ToggleControl
							label="Query variables"
							help={ queryVariables ? 'Get variables from routing' : 'Disable query variables' }
							checked={ queryVariables }
							onChange={ ( queryVariables ) => setAttributes( { queryVariables } ) }
						/>
						<ToggleControl
							label="Classic slider"
							help={ slider ? 'Display as classic slider' : 'Classic slider disable' }
							checked={ slider }
							onChange={ ( slider ) => setAttributes( { slider } ) }
						/>
					</PanelBody> }

					{ wrapperType == 'editor-code' && <PanelBody
						title={ __( 'Query properties' ) }
						initialOpen={ true }
					>
						<label>Tag name</label>
						<PlainText
							className="plain-text"
							value={ tagName }
							onChange={ ( tagName ) => setAttributes( { tagName } ) }
						/>
					</PanelBody> }

					<PanelBody
						title={ __( 'Appearance' ) }
						initialOpen={ false }
					>
						{ /* Block background  */ }
						<label>Background color</label>
						<ColorPalette value={ backgroundColor } onChange={ ( backgroundColor ) =>setAttributes( { backgroundColor } ) } />
					</PanelBody>

				</InspectorControls>

				{ wrapperType != 'excerpt-view' ? <InnerBlocks /> : <div >{ backgroundColor }GraphQL Query block with <b>{ blockName }</b> template</div> }

			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: ( props ) => {
		console.log( props );
		return (
			<div>
				<InnerBlocks.Content />
			</div>
		);
	},
} );
