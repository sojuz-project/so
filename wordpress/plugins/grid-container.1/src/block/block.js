/**
 * BLOCK: grid-container
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/**
 * docs
 * https://developer.wordpress.org/block-editor/components/toggle-control/
	HERO BLOCK
	https://jschof.com/gutenberg-blocks/wordpress-gutenberg-blocks-example-creating-a-hero-image-block-with-inspector-controls-color-palette-and-media-upload-part-2/
*/

//  Import CSS.
import './style.scss';
import './editor.scss';
import React from 'react'

import { DragGrid } from '../../../common-components/DragGrid';

import { coreparagraph } from '../../../common-components/Paragraph';
import { coreimage } from '../../../common-components/Image';
import { customimage } from '../../../common-components/CustomImage';
import { coreheading } from '../../../common-components/Heading';
import { componentwrapper } from '../../../common-components/ComponentWrapper';
import { customaction } from '../../../common-components/Action';

import { componentsSchema } from './componentsSchema'
/* Block controlls */
import BlockGridOn from './controlls/Block_GridOn';
import BlockPickComponent from './controlls/Block_PickComponent';
/* Inspector componsnts controlls */
import ColumnsPicker from './controlls/columnsPicker';
import SizeClassPicker from './controlls/component_fontSize';
import TagnamePicker from './controlls/component_tagName';
import TextColorPicker from './controlls/component_textColor';
import TextWeightPicker from './controlls/component_textWeight';
import BackgroundColor from './controlls/component_BgColor';
import ZIndexPicker from './controlls/component_zIndex';
/* Inspector sections controlls */
import VerticalAlignPicker from './controlls/verticalalignPicker';
import SectionHorizontalPaddings from './controlls/section_horizontalPaddings';
import SectionGridGap from './controlls/section_gridGap';
import IsSlideChecker from './controlls/isSlideChecker';

const { InspectorControls, ColorPalette, BlockControls, AlignmentToolbar } = wp.editor;
const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks;
const { useState } = wp.element;
const { PanelBody, Button, Dropdown, ToggleControl, TextControl, SelectControl, RangeControl, ButtonGroup } = wp.components;

const componentMap = {
	coreparagraph,
	coreimage,
	customimage,
	coreheading,
	componentwrapper,
	customaction,
};

let cellI = 0;
const addGridCell = (prev, defaultValues) => {
	return [...prev, {
		i: `n${++cellI}`,
		x: 0,
		y: 4,
		zIndex: 1,
		w: 30,
		h: 4,
		gh: undefined,
		padding: undefined,
		align: undefined,
		verticalAlign: undefined,
		fontSize: undefined,
		tagName: undefined,
		fontWeight: undefined,
		textColor: undefined,
		backgroundColor: undefined,
		backgroundOpacity: undefined,
		reasignTo: undefined,
		mapQL: undefined,
		...defaultValues
}]}

const updateAttrs = (sectionI, componentI, attrs) => ((section, sectionIndex) => sectionIndex === sectionI
		? section.map((comp, compI) => compI === componentI
			? ({ ...comp, attrs })
			: comp)
	: section);

/**
 * Register: aa Gutenberg Block.
 *
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
const block = registerBlockType('sojuz/block-grid-container', {
	title: __('Grid Container'), // Block title.
	description: __('Main SOJUZ project Block, dedicated to build multipropose sections'),
	icon: 'screenoptions', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__('grid-container — SOJUZ Block'),
		__('SOJUZ Example'),
		__('create-guten-block'),
	],
	supports: {
		align: ['full'],
	},
	attributes: {
		align:{
			type: 'string',
			default: "full",
		},
		gridSections:{
			type: 'object',
			default: {
				gridTemplateColumns: 'repeat(1, 1fr)', // gridTemplateColumns: `repeat(1, 1fr)`,
				isSlider: false, // ... 
				gridType:'fr', 
				gridGap: '0vw', // gridGap
				padding: '0 0vw 0 0vw', //  paddingLeft,  paddingRight
				backgroundColor: '',
			},
		},
		template: {
			type: 'array',
			default: [[]]
		},
		gridLayout: {
			type: 'array',
			default: [],
		},
		selectedElement:{
			type: 'object',
			default: {
				component: {},
				componentI: null
			}
		},
		clientId: {
			type: 'string',
			default: ""
		},
		query: {
			type: 'string',
			default: ""
		},
	},

	// eslint-disable-next-line react/display-name
	edit: function (props) {
		const [gridMode, setGridMode] = useState(true) // is inverselogic
	
		// eslint-disable-next-line react/prop-types
		const { attributes: { 
			contentStyle, 
			template, 
			gridLayout, 
			selectedElement, 
			clientId,
			query,
			gridSections,
			gridSections: {
				isSlider,
				padding,
				gridType,
				gridGap,
				gridTemplateColumns,
			},
		}, className, setAttributes } = props;
	
		// depreciated in dataSources
		setAttributes({
			clientId: props.clientId
		});

		const setAttributesByGrid = (key) => value => setGridMode(prev => true) || setAttributes({
			gridLayout: gridLayout.map((element, elementI) => selectedElement.componentI === elementI
				? { ...element, [key]: value }
				: element)
		}) || console.log(gridLayout, value);
		
		const setAttributesGridSections = (key) => value => setGridMode(prev => true) || setAttributes({
			gridSections: { ...gridSections, [key]: value }
		});

		// const onChangeFontClassSize = ({slug:fontSize}) => {
		// 	setAttributes({
		// 		gridLayout: gridLayout.map((element, elementI) => selectedElement.componentI === elementI
		// 			? { ...element, ['fontSize']: fontSize }
		// 			: element)
		// 	});
		// };

		const addSection = () => {
			setAttributes({ template: [...template, template[0]] });
			setGridMode(prev => true);
		};

		const removeComponent = (componentI) => {
			const copy = template.map((section) => {
				return section.filter((e, index) => (
					index != componentI
				))
			})
			const test = gridLayout.filter((e, index) => (index != componentI))
			setAttributes({ template: copy, gridLayout: test });
		};
		// const onRemoveItem = (i) => {
		// 	const copy = template.map((section) =>
		// 		section.filter((e, index) => (
		// 			index != i
		// 		)));
		// 	const test = gridLayout.filter((e, index) => (index != i))
		// 	setAttributes({ template: copy, gridLayout: test});
		// };
		// const onClickItem = (i) => {
		// 	setAttributes({selectedElement: { componentI: i }})
		// };
		const onClickComponent = (component, componentI) => {
			setAttributes({
				selectedElement:  { component, componentI  }
			})
		}

		const newComponent = (compKey) => {
			const { defaultValues, ...schemaCopmonent } = componentsSchema[compKey];
			const copy = template.map((section, i) =>  [...section, schemaCopmonent]);
			setAttributes({
				template: copy,
				gridLayout: addGridCell(gridLayout, defaultValues),
				selectedElement: { component: schemaCopmonent, componentI: template[0].length }
			});
			setGridMode(prev => false);
		};

		return (
			<React.Fragment >
				<div style={gridSections} className={'alignfull wp-block-sojuz-block-grid-container ' + (gridMode ? '' : 'grid-mode-on')} >
				<BlockControls>
					
						<BlockPickComponent newComponent={newComponent} />
						<BlockGridOn setGridMode={setGridMode} gridMode={gridMode}/>
						
						<div 
						className={`components-toolbar`}
							onClick={() => {
								setGridMode(prev => true);
								wp.data.select('core/edit-post').getActiveGeneralSidebarName() === 'data-sources-plugin/dataSources'
								? wp.data.dispatch("core/edit-post").openGeneralSidebar("edit-post/block")
								: wp.data.dispatch("core/edit-post").openGeneralSidebar("data-sources-plugin/dataSources")
							}}>
							<span className="dashicons dashicons-download"></span>
						</div>
				</BlockControls>

				<InspectorControls>
					<PanelBody
						title={__('Block canvas propertis')}
						initialOpen={false}
					>
						{/* Block background  */}
						<label>Background color</label>
						<ColorPalette vslue={gridSections.backgroundColor} onChange={setAttributesGridSections('backgroundColor')} />
						{/* Inner padding  */}
						<SectionHorizontalPaddings marginValue={gridSections.padding} onHorizontalMargins={setAttributesGridSections('padding')}/>
						{/* Grid Gap  */}
						<SectionGridGap gapValue={gridSections.gridGap} onGridGap={setAttributesGridSections('gridGap')} />
					</PanelBody>

					<PanelBody
						title={__('Selected component properties')}
						initialOpen={true}
					>
						{/* Vertical align  */}
						<VerticalAlignPicker gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeVerticalAlign={setAttributesByGrid('verticalAlign')} />
						<hr/>
						{/* Font size */}
						<SizeClassPicker gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeFontClassSize={setAttributesByGrid('fontSize')} />
						{/* Tag name  */}
						<TagnamePicker gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeTagname={setAttributesByGrid('tagName')}/>
						{/* Font weight  */}
						<TextWeightPicker gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeFontWeight={setAttributesByGrid('weight')} />
						{/* Font color  */}
						<TextColorPicker gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeTextColor={setAttributesByGrid('textColor')} />
						{/* Overlay color  */}
						<BackgroundColor gridMode={gridMode} selectedElement={selectedElement} gridLayout={gridLayout} onChangeBgColor={setAttributesByGrid('backgroundColor')} />
						{/* Overlay opacity  */}
						{(gridLayout[selectedElement.componentI] && <RangeControl
							label="Background opacity"
							value={gridLayout[selectedElement.componentI].backgroundOpacity}
							onChange={setAttributesByGrid('backgroundOpacity')}
							min={0}
							max={1}
							step={0.1}
						/>)}

						{/* Inner padding  */}
						{(gridLayout[selectedElement.componentI] && <RangeControl
							label="Inner padding"
							value={gridLayout[selectedElement.componentI].padding}
							onChange={setAttributesByGrid('padding')}
							min={0}
							max={2}
							step={0.1}
						/>)}

						<hr/>

						{/* Z index  */}
						<ZIndexPicker selectedElement={selectedElement} gridLayoutObject={gridLayout[selectedElement.componentI]} onZIndexChange={setAttributesByGrid('zIndex')} />
						{/* Reasign vue component  */}
						<TextControl
							label="Ressign component"
							value={gridLayout[selectedElement.componentI] ? gridLayout[selectedElement.componentI].reasignTo : null}
							onChange={setAttributesByGrid('reasignTo')}
						/>
						{/* Hidden to hover  */}
						<div class="components-base-control">
							<ToggleControl
								label="Hidden to hover"
								// checked={isSlider}
								// onChange={() => setAttributes({ isSlider: !isSlider })}
							/>
						</div>
					</PanelBody>
					
					
					<PanelBody
						title={__('List')}
						initialOpen={false}
					>
						<div className="list-data-elements">
							{/* sections */}
							{template.map((section, sectionIndex) => (
								<div className="selector-item" key={sectionIndex}>Section list element: {sectionIndex}</div>
							))}
						</div>
						<Button isPrimary className="full-width" onClick={addSection}>+ Duplicate section</Button>
		
						<ColumnsPicker gridTemplateColumns={gridSections.gridTemplateColumns} gridTemplateColumnsChange={setAttributesGridSections('gridTemplateColumns')} />

						<ToggleControl
							label="Display as slider"
							checked={isSlider}
							onChange={() => setAttributes({ isSlider: !isSlider })}
						/>
					</PanelBody>
					<PanelBody
						title={__('Data sources')}
						initialOpen={false}
					>
						<Button
							className="full-width"
							isPrimary
							onClick={() => {
								setGridMode(prev => true)
								wp.data.dispatch("core/edit-post").openGeneralSidebar("data-sources-plugin/dataSources")
							}}>
							<span className="dashicons dashicons-download"></span> Open data sources panel
						</Button>
					</PanelBody>
				</InspectorControls>
				{
					<BlockControls>
						<AlignmentToolbar
							value={template[0][selectedElement.componentI] ? template[0][selectedElement.componentI].attrs.align : null}
							onChange={setAttributesByGrid('align')}
						/>
					</BlockControls>
				}

				<React.Fragment>
					{(gridMode ? template : [template[0]])
						//sections
						.map((section, sectionI) => (
							<section onDoubleClick={() => {
								setGridMode(prev => !prev)
								wp.data.dispatch("core/edit-post").openGeneralSidebar("edit-post/block")
							}} key={`section-${sectionI}`}>
								<DragGrid
									gridMode={gridMode}
									gridLayout={gridLayout}
									onChange={newLayout => setAttributes({ gridLayout: newLayout })}>
								{
									//components.editor-rich-text{
									section.map((component, componentI) => {
										const Component = componentMap[component.blockName];
										const pathTo = component.attrs;
										const update = (attrs) => {
											setAttributes({
												template: template.map(updateAttrs(sectionI, componentI, attrs)),
											});
										};
										return (
											<div
												id={`component-${sectionI}-${componentI}`}
												key={`component-${sectionI}-${componentI}`}
												style={{ 
													gridArea: `${gridLayout[componentI].y + 1} / ${gridLayout[componentI].x + 1} / span ${gridLayout[componentI].h} / span ${gridLayout[componentI].w}`, 
													zIndex: gridLayout[componentI].zIndex,
													padding: `${gridLayout[componentI].padding}vw`,
													textAlign: gridLayout[componentI].align,
													fontWeight: gridLayout[componentI].fontWeight,
												}}
												className={
													`component ${component.mapQL.length ? 'edit-lock' : ''} 
													${selectedElement.componentI == componentI && newFunction()}`}
												onClick={() => onClickComponent(component, componentI)}
											>		
												<Component attrs={pathTo} updateAttrs={update} gridLayoutObject={gridLayout[componentI]} />
												{!component.mapQL.length && selectedElement.componentI === componentI && wp.data.select('core/edit-post').getActiveGeneralSidebarName() === 'data-sources-plugin/dataSources'
													? <div className="set-data-source badge">
														<span className="dashicons dashicons-download"></span>
														<span>Choose source from sidebar</span>
													</div>
													: false}

												{component.mapQL.length
													? <div className="mapql badge">
														<span className="dashicons dashicons-admin-links"></span>
														<span>{component.mapQL[1]}</span>
													</div>
													: false}

												{gridMode && selectedElement.componentI === componentI
													? <div onClick={() => removeComponent(selectedElement.componentI)} className="remove-component badge">
														<span className="dashicons dashicons-trash"></span>
													</div>
													: false}
											</div>
										)
									})
								}
								</DragGrid>
							</section>
						))
					}
				</React.Fragment>
	
				
			</div>
			<div className="new-section-bar">
				<IsSlideChecker isSlider={isSlider} onIsSlider={setAttributesGridSections('isSlider')} />
				<ColumnsPicker gridTemplateColumns={gridSections.gridTemplateColumns} gridTemplateColumnsChange={setAttributesGridSections('gridTemplateColumns')} />
				<Button isPrimary onClick={addSection}>+ Duplicate section</Button>
			</div>
			</React.Fragment >
		);
	},

	save: function () {
		return (
			<div>
				This is sojuz project block
			</div>
		);
	},
});

function newFunction() {
	return 'component-selected';
}

