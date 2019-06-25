import './style.scss';
import './editor.scss';
import Title, { config as titleConfig } from '../../../common-components/Title';
import ActionButton, { config as actionButtonConfig } from '../../../common-components/ActionButton';
import Background, { config as backgroundConfig } from '../../../common-components/Background';
import Textcolor, { config as textcolorConfig } from '../../../common-components/Textcollor';
import ThumbImage, { config as thumbimageConfig } from '../../../common-components/ThumbImage';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

registerBlockType('cgb/block-about-section', {
	title: __( 'about-section' ),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'about-section' ),
	],
	attributes: {
		blockTitle: '',
		...titleConfig.attrs,
		...actionButtonConfig.attrs,
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
		...thumbimageConfig.attrs,
	},

	edit: function(props) {
		return (
			<div className="about-block" style={{ backgroundColor: props.attributes.backgroundColor, color: props.attributes.textColor }}>
				<div className="pickers-group">
					<div className="block-id">Section: {props.name}</div>
					<Textcolor {...textcolorConfig.getAttrs(props)} />
					<Background {...backgroundConfig.getAttrs(props)} />
				</div>
				<Title {...titleConfig.getAttrs(props)} />
				<ActionButton {...actionButtonConfig.getAttrs(props)} />
				<ThumbImage {...thumbimageConfig.getAttrs(props)} />
			</div>
		);
	},

	save: function() {
		return <div></div>;
	},
});
