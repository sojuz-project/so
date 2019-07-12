import './style.scss';
import Background, { config as backgroundConfig, BackgroundCustom } from '../../../common-components/Background';
import RichList, { config as listConfig } from '../../../common-components/RichList';
import Carousel from '../../../common-components/Carousel';
import GridCss, { config as gridCssConfig, GridCssCustom } from '../../../common-components/GridCss';

const { Comp: Textcolor, config: textcolorConfig } = BackgroundCustom('textColor');
const { Comp: Allalign, config: allalignConfig } = GridCssCustom('allAlign');
const { Comp: Imagesize, config: imagesizeConfig } = GridCssCustom('imageSize');
const { Comp: Titlealign, config: titlealignConfig } = GridCssCustom('titleAlign');

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { RichText } = wp.editor;

registerBlockType('sojuz/block-list-section', {
	title: __( 'list-section' ),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'list-section' ),
	],
	attributes: {
		blockTitle: '',
		title: {
			type: 'string',
		},
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
		...gridCssConfig.attrs,
		...listConfig.attrs,
		...allalignConfig.attrs,
		...imagesizeConfig.attrs,
		...titlealignConfig.attrs,
	},

	edit: function(props) {
		return (
			<div
				className={
					`sojuz-block list-block 
					${props.attributes.gridCss} 
					${props.attributes.imageSize} 
					${props.attributes.allAlign}`
				}
				style={{
					backgroundColor: props.attributes.backgroundColor,
					color: props.attributes.textColor,
				}}
			>
				{/*
					<!-- Main block pickers -->
				*/}
				<div className="main-pickers-group">
					<div className="block-id">Section: {props.name}</div>
					<Textcolor
						{...textcolorConfig.getAttrs(props)}
						indicatorText="Text: "
						textColor={props.attributes.textColor}
					/>
					<Background {...backgroundConfig.getAttrs(props)} />
					<Allalign
						{...allalignConfig.getAttrs(props)}
						allAlign={props.attributes.allAlign}
						data={[
							{ value: 'left', label: 'Align left' },
							{ value: 'center', label: 'Align center' },
						]}
					/>
					<Imagesize
						{...imagesizeConfig.getAttrs(props)}
						imageSize={props.attributes.imageSize}
						data={[
							{ value: 'standard', label: 'Img standard' },
							{ value: 'big', label: 'Img big' },
							{ value: 'small', label: 'Img small' },
							{ value: 'micro', label: 'Img micro' },
						]}
					/>
					<GridCss
						{...gridCssConfig.getAttrs(props)}
						data={[
							{ value: 'default', label: 'Standard' },
							{ value: 'default-compact', label: 'Standard compact' },
							{ value: 'horizontal-2', label: 'Horizontal x2' },
							{ value: 'horizontal-3', label: 'Horizontal x3' },
							{ value: 'horizontal-4', label: 'Horizontal x4' },
							{ value: 'hero-slider', label: 'Hero slider' },
						]}
					/>
				</div>
				{/*
					<!-- Title group -->
				*/}
				<div className="block-group title">
					<div className="extend-pickers-group">
						<div className="group-title">Title</div>
						<Titlealign
							{...titlealignConfig.getAttrs(props)}
							titleAlign={props.attributes.titleAlign}
							data={[
								{ value: 'left', label: 'Left' },
								{ value: 'center', label: 'Center' },
							]}
						/>
					</div>
					<RichText
						className={`text ${props.attributes.titleAlign}`}
						placeholder="Insert section title"
						value={props.attributes.title}
						onChange={changed => props.setAttributes({ title: changed })}
					/>
				</div>
				{/*
					<!-- classic list -->
				*/}
				{gridCssConfig.getAttrs(props).gridCss === 'hero-slider' ? (
					<Carousel className="carousel" {...listConfig.getAttrs(props)} />
				) : (
					<RichList {...listConfig.getAttrs(props)} />
				)}

			</div>
		);
	},

	save: function() {
		return <div></div>;
	},
});
