import './style.scss';
import './editor.scss';
import Title, { config as titleConfig } from '../../../common-components/Title';
import Background, { config as backgroundConfig } from '../../../common-components/Background';
import Textcolor, { config as textcolorConfig } from '../../../common-components/Textcollor';
import RichList, { config as richListConfig } from '../../../common-components/RichList';


const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

registerBlockType('cgb/block-properties-section', {
	title: __( 'properties-section' ),
	icon: 'shield', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'properties-section' ),
	],
	attributes: {
		...titleConfig.attrs,
		...backgroundConfig.attrs,
		...textcolorConfig.attrs,
		...richListConfig.attrs
	},

	edit: function(props) {
		return (
			<div className="properties-block" style={{ backgroundColor: props.attributes.backgroundColor, color: props.attributes.textColor }}>
				<div className="pickers-group">
					<div className="block-id">Section: {props.name}</div>
					<Textcolor {...textcolorConfig.getAttrs(props)} />
					<Background {...backgroundConfig.getAttrs(props)} />
				</div>
				<Title {...titleConfig.getAttrs(props)} />
				<RichList {...richListConfig.getAttrs(props)} />
			</div>
		);
	},

	save: function() {
		return <div></div>;
	},
});
