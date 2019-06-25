import './style.scss';
import './editor.scss';
import Carousel, { config as carouselConfig } from '../../../common-components/Carousel';
import Title, { config as titleConfig } from '../../../common-components/Title';
import Background, { config as backgroundConfig } from '../../../common-components/Background';
import Textcolor, { config as textcolorConfig } from '../../../common-components/Textcollor';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

registerBlockType('cgb/block-advantages-section', {
	title: __( 'advantages-section' ),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'advantages-section' ),
	],
	attributes: {
		...carouselConfig.attrs,
		...titleConfig.attrs,
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
	},

	edit: function(props) {
		return (
			<div className="advantages-block" style={{ backgroundColor: props.attributes.backgroundColor, color: props.attributes.textColor }}>
				<div className="pickers-group">
					<div className="block-id">Section: {props.name}</div>
					<Textcolor {...textcolorConfig.getAttrs(props)} />
					<Background {...backgroundConfig.getAttrs(props)} />
				</div>
				<Title {...titleConfig.getAttrs(props)} />
				<Carousel {...carouselConfig.getAttrs(props)} />
			</div>
		);
	},

	save: function() {
		return <div></div>;
	},
});
