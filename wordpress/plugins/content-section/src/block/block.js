import './style.scss';
import Background, { config as backgroundConfig, BackgroundCustom } from '../../../common-components/Background';
import ThumbImage, { config as thumbimageConfig } from '../../../common-components/ThumbImage';
import GridCss, { config as gridCssConfig, GridCssCustom } from '../../../common-components/GridCss';

const { Comp: Textcolor, config: textcolorConfig } = BackgroundCustom('textColor');
const { Comp: Excerptcolor, config: excerptcolorConfig } = BackgroundCustom('excerptColor', '#000');

const { Comp: Titlealign, config: titlealignConfig } = GridCssCustom('titleAlign');
const { Comp: Excerptalign, config: excerptalignConfig } = GridCssCustom('excerptAlign');
const { Comp: Contentalign, config: contentalignConfig } = GridCssCustom('contentAlign');
const { Comp: Imagesize, config: imagesizeConfig } = GridCssCustom('imageSize');

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { RichText } = wp.editor;

registerBlockType('sojuz/block-content-section', {
	title: __('content-section'),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__('content-section'),
	],
	attributes: {
		blockTitle: '',
		title: {
			type: 'string',
		},
		content: {
			type: 'string',
		},
		excerpt: {
			type: 'string',
		},
		...thumbimageConfig.attrs,
		...excerptcolorConfig.attrs,
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
		...gridCssConfig.attrs,
		...titlealignConfig.attrs,
		...contentalignConfig.attrs,
		...excerptalignConfig.attrs,
		...imagesizeConfig.attrs,
	},

	edit: function(props) {
		return (
			<div
				className={`sojuz-block content-block ${gridCssConfig.getAttrs(props).gridCss}`}
				style={{ backgroundColor: props.attributes.backgroundColor, color: props.attributes.textColor }}
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
					<GridCss
						{...gridCssConfig.getAttrs(props)}
						data={[
							{ value: 'default', label: 'Default' },
							{ value: 'default-revers', label: 'Default rev.' },
							{ value: 'horizontal', label: 'Horizontal' },
							{ value: 'horizontal-revers', label: 'Horizontal rev.' },
							{ value: 'compact', label: 'Compact' },
							{ value: 'compact-revers', label: 'Compact rev.' },
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
					<!-- Excerpt group -->
				*/}
				<div className="block-group excerpt">
					<div className="extend-pickers-group">
						<div className="group-title">Excerpt</div>
						<Excerptcolor
							{...excerptcolorConfig.getAttrs(props)}
							indicatorText="Color: "
							// excerptColor={props.attributes.excerptColor}
						/>
						<Excerptalign
							{...excerptalignConfig.getAttrs(props)}
							excerptAlign={props.attributes.excerptAlign}
							data={[
								{ value: 'left', label: 'Left' },
								{ value: 'center', label: 'Center' },
							]}
						/>
					</div>
					<div
						style={{ color: props.attributes.excerptColor }}
					>
						<RichText
							className={`text ${props.attributes.excerptAlign}`}
							placeholder="Insert excerpt text"
							value={props.attributes.excerpt}
							onChange={newExcerpt => props.setAttributes({ excerpt: newExcerpt })}
						/>
					</div>
				</div>
				{/*
					<!-- Thumbnail group -->
				*/}
				<div className="block-group thumb">
					<div className="extend-pickers-group">
						<div className="group-title">ThumbImage</div>
						<Imagesize
							{...imagesizeConfig.getAttrs(props)}
							imageSize={props.attributes.imageSize}
							data={[
								{ value: 'standard', label: 'Standard' },
								{ value: 'big', label: 'Big' },
								{ value: 'small', label: 'Small' },
								{ value: 'micro', label: 'Micro' },
							]}
						/>
					</div>
					<div className={props.attributes.imageSize}>
						<ThumbImage {...thumbimageConfig.getAttrs(props)} />
					</div>
				</div>
				{/*
					<!-- Content group -->
				*/}
				<div className="block-group content">
					<div className="extend-pickers-group">
						<Contentalign
							{...contentalignConfig.getAttrs(props)}
							contentAlign={props.attributes.contentAlign}
							data={[
								{ value: 'left', label: 'Left' },
								{ value: 'center', label: 'Center' },
							]}
						/>
					</div>
					<RichText
						className="text"
						placeholder="Insert content text"
						value={props.attributes.content}
						onChange={changed => props.setAttributes({ content: changed })}
					/>
				</div>
			</div>
		);
	},

	save: function () {
		return <div></div>;
	},
});
