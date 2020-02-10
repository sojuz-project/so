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
/*
	[...Array(50)].map((a, i) => `.cstr${i + 1} { grid-columns-start: ${i + 1}; }`).join('\n')
*/

//  Import CSS.
import './style.scss';
import './editor.scss';
import React from 'react'
const { set, get: _get } = lodash
let i = 1;
import { DragGrid } from '../../../common-components/DragGrid';
/* Block Content Components */
import { coreparagraph } from '../../../common-components/Paragraph';
import { customimage } from '../../../common-components/Image';
import { coreheading } from '../../../common-components/Heading';
import { customwrapper } from '../../../common-components/CustomWrapper';
/* Panels */
import ComponentsPanel from './panels/componentsPanel';
import BlockPanel from './panels/blockPanel';
/* Block controlls */
import BlockPickComponent from './controlls/Block_PickComponent';
import {TECHNICAL,CLASS,STYLE,CONTENT,BLOCK,COMPONENT,SECTION,DATA} from './index'
/* Schemas */
import { defaultAttributes } from './schemas/defaultAttributes';
import { componentsSchema } from './schemas/componentsSchema';
/* hooks */
import { useUndo } from './useUndo';

const { InspectorControls, BlockControls,  AlignmentToolbar } = wp.editor;
const { IconButton, MenuItem, Toolbar } = wp.components;


const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { useState } = wp.element;


const componentMap = {
	coreparagraph,
	coreheading,
	customimage,
	customwrapper,
};

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
	title: __('Grid Container'),
	description: __('Main SOJUZ project Block, dedicated to build multipropose sections'),
	icon: 'screenoptions',
	category: 'common',
	keywords: [
		__('grid-container — SOJUZ Block'),
		__('SOJUZ Example'),
		__('create-guten-block'),
	],
	supports: {
		align: ['full'],
	},
	attributes: defaultAttributes,

	edit: function (currentProps) {
		const [gridMode, setGridMode] = useState(true) // is inverselogic
		const props = useUndo(currentProps);

		const {
			attributes: {
				selectedElement,
				[TECHNICAL]: technical,
				[TECHNICAL]: {
					[BLOCK]: technicalBlock,
					[SECTION]: technicalSection,
					[COMPONENT]: technicalComponent,
				} = {},
				[CLASS]:classObj,
				[CLASS]:{
					[BLOCK]: classBlock = {},
					[COMPONENT]: classComponent,
				} = {},
				[STYLE]: {
					[BLOCK]: styleBlock,
					[COMPONENT]: styleComponent,
					[SECTION]: styleSection,
				} = {},
				[CONTENT]: content,
				[DATA] : data,
				[DATA]: {
					[BLOCK]: blockData,
					[COMPONENT]: componentData
				}
			},
			setAttributes
		} = props;

		const get = (path, defaultValue) => _get(props.attributes, path, defaultValue)
		const rm = (el, i) => el.slice(0, i).concat(el.slice(i + 1, el.length))

		const setAttrs = (path, cb = v => v) => (val) => {
			const newAttrs = set(props.attributes, path, val);
			/* all {i} fix gutenberg dafault values problem */
			const attrs = {
				i: ++i,
				...newAttrs,
				class: {
					...newAttrs.class,
					block: {
						...newAttrs.class.block,
						i: ++i,
					},
				},
				style: {
					...newAttrs.style,
					block: {
						...newAttrs.style.block,
						i: ++i,
					},
				},
				technical: {
					...newAttrs.technical,
					block: {
						...newAttrs.technical.block,
						i: ++i,
					},
				},
				data: {
					...newAttrs.data,
					block: {
						...newAttrs.data.block,
						i: ++i,
					},
				}
			};
			setAttributes(cb(attrs));
		}

		const newComponent = (blockName) => {
			const schema = componentsSchema[blockName];

			setAttrs(`${CONTENT}`, updated => {
				updated.technical.component = [...technicalComponent, {
					...schema.defaultTechnical,
					y: technicalSection.h,
				}];
				updated.technical.section.h = schema.defaultTechnical.h + technicalSection.h;
				updated.class.component = [...classComponent, schema.defaultClass];
				updated.style.component = [...styleComponent, schema.defaultStyle];
				updated.technical.i = technical.i + 1;

				return updated;
			})(
				get(CONTENT).map(el => [...el,
					schema.defaultContent,
				])
			);
			setGridMode(prev => false);
		};

		const removeComponent = (componentI) => {
			/** CONTENT */
			setAttrs(`${CONTENT}`)(	get(CONTENT).map(el => {
				return [...rm(el, componentI)]
			}))
			/** CLASSES */
			setAttrs(`${CLASS}.${COMPONENT}`)(
				rm(get(`${CLASS}.${COMPONENT}`), componentI)
			)
			/** TECH */
			setAttrs(`${TECHNICAL}.${COMPONENT}`)(
				rm(get(`${TECHNICAL}.${COMPONENT}`), componentI)
			)
		};

		const addSection = () => {
			setAttrs(`${CONTENT}`)(
				[
					...content,
					JSON.parse(JSON.stringify(content[0])),
				]
			)
		};

		const removeSection = () => {
			const newContent = content.slice(0, -1);
			if (newContent.length >= 1) {
				setAttrs(CONTENT)(newContent)
			}
		};

		const onClickComponent = (component, componentI) => {
			setAttributes({
				selectedElement:  { component, componentI  }
			})
		}

		const calcCompH = (compH) => {
			// console.log('compH', compH, get(`${TECHNICAL}.${BLOCK}.gridTemplateColumns`));
			return (compH * 2) / get(`${TECHNICAL}.${BLOCK}.gridTemplateColumns`);
		}
		return (
			<React.Fragment>
				<div
					className={
						Object.entries(classBlock || {}).map(([key, value]) => {
							return `${value}-${key}`
						}).join(' ') + ' alignfull wp-block-sojuz-block-grid-container ' + (gridMode ? 'grid-mode-off' : 'grid-mode-on')}
					style={{ ...styleBlock, ...(!gridMode && { gridTemplateColumns: '1fr' }) }}>
				<BlockControls>
						<BlockPickComponent newComponent={newComponent} />
						<Toolbar>
							<IconButton
								label="Grid edit mode (double click on block)"
								className={['components-toolbar__control',
									{ 'is-active': !gridMode },
								]}
								icon="grid-view"
								onClick={() => { setGridMode(prev => !prev) }}
							/>
						</Toolbar>
						<Toolbar>
							<IconButton
								label="Remove component"
								className='components-toolbar__control'
								icon="trash"
								onClick={() => { removeComponent(selectedElement.componentI) }}
							/>
						</Toolbar>
						<Toolbar>
							<IconButton
								label="Data sources"
								className={['components-toolbar__control',
									{ 'is-active': wp.data.select("dataSourcesStore").isOpen() },
								]}
								icon="download"
								onClick={() => { wp.data.dispatch("dataSourcesStore").toggle() }}
							/>
						</Toolbar>

							<AlignmentToolbar
							value={selectedElement.componentI ? classComponent[selectedElement.componentI].align : 'left'}
							onChange={(val) => setAttrs(`${CLASS}.${COMPONENT}[${selectedElement.componentI}].align`)(val)}
							/>

				</BlockControls>
				{technicalComponent[0] ? (
					<React.Fragment>
					<InspectorControls>
						{ <BlockPanel
							onUpdate={setAttrs}
							addSection={addSection}
							removeSection={removeSection}
							classBlock={classBlock}
							styleBlock={styleBlock}
							technicalBlock={technicalBlock}
						/>}
						{ <ComponentsPanel
							onUpdate={setAttrs}
							technicalComponent={technicalComponent[selectedElement.componentI]}
							classComponent={classComponent[selectedElement.componentI]}
							styleComponent={styleComponent[selectedElement.componentI]}
							componentI={selectedElement.componentI}
						/>}
				</InspectorControls>
					<React.Fragment>
					{(gridMode ? content : [content[0]]).map((section, sectionI) => (
						<section

						onDoubleClick={() => {
							setGridMode(prev => !prev)
						}} key={`${props.clientId} section-${sectionI}`}>
							<DragGrid
								styleComponent={styleComponent}
								styleSection={styleSection}
								gridMode={gridMode}
								techC={technicalComponent}
								techS={technicalSection}
								onChange={(val) => {
									setAttrs(`${CLASS}.${COMPONENT}`, updated => {
										const gridRows = val.reduce((acc, curr) => acc > curr.y2 ? acc : curr.y2, 0)
										updated.technical.section.h = gridRows
										updated.style.section.gridTemplateRows = `repeat(${gridRows}, min-content)`
										updated.technical.component = val;
										// updated.style.component[selectedElement.componentI].height = `${calcCompH(val[selectedElement.componentI].h)}vw`
										return updated
									})(val.map((data, i) => ({
										...classComponent[i],
										sc: `c${data.x + 1}`,
										ec: `c${data.x2 + 1}`,
										sr: `r${data.y + 1}`,
										er: `r${data.y2 + 1}`,
									})));
								}}>
							{
								section.map((component, componentI) => {
									const Component = componentMap[component.blockName];
									return (
										<div
											id={`${props.clientId} component-${sectionI}-${componentI}`}
											key={`${props.clientId} component-${sectionI}-${componentI}`}
											className={Object.entries(classComponent[componentI] || {}).map(([key, value]) =>{
												return `${value}-${key}`
											}).join(' ') + (selectedElement.componentI == componentI ? ' component-selected' : '')+ ' component'}
											style={gridMode ? { ...styleComponent[componentI], height: `${calcCompH(technicalComponent[componentI].h)*.76}vw` } : { }}
											onMouseDown={() => onClickComponent(component, componentI)}
										>
											<Component content={component} updateAttrs={setAttrs(`${CONTENT}[${sectionI}][${componentI}]`)} technicalComponent={technicalComponent[componentI]} />
										</div>
								)})}
							</DragGrid>
						</section>
					))}
					</React.Fragment>
				</React.Fragment>
			) : null}
			</div>
			</React.Fragment>
	)},
	save: function (props) {
		console.log("SAVE OBJECT",props)
		return (
			<div>
				This is sojuz project grid container block
			</div>
		);
	},
});
