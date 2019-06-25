import './style.scss';
import './editor.scss';
import Carousel, { config as carouselConfig } from '../../../common-components/Carousel';
import Title, { config as titleConfig } from '../../../common-components/Title';
import ActionButton, { config as actionButtonConfig } from '../../../common-components/ActionButton';
import Background, { config as backgroundConfig } from '../../../common-components/Background';
import Textcolor, { config as textcolorConfig } from '../../../common-components/Textcollor';


const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

registerBlockType('cgb/block-hero-section', {
	title: __( 'hero-section' ),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'hero-section' ),
	],
	attributes: {
		blockTitle: '',
		...carouselConfig.attrs,
		...titleConfig.attrs,
		...actionButtonConfig.attrs,
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
	},

	edit: function(props) {
		return (
			<div className="hero-block" style={{ backgroundColor: props.attributes.backgroundColor, color: props.attributes.textColor }}>
				<div className="pickers-group">
					<div className="block-id">Section: {props.name}</div>
					<Textcolor {...textcolorConfig.getAttrs(props)} />
					<Background {...backgroundConfig.getAttrs(props)} />
				</div>
				<Title {...titleConfig.getAttrs(props)} />
				<Carousel {...carouselConfig.getAttrs(props)} />
				<ActionButton {...actionButtonConfig.getAttrs(props)} />
			</div>
		);
	},

	save: function() {
		return <div></div>;
	},
});
