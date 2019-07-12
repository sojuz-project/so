const { RichText } = wp.editor;

const config = {
	attrs: {
		subTitle: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { subTitle } = {},
	} = {}) => ({ subTitle, setAttributes }),
};

const SubTitle = ({
	subTitle = '',
	placeholder = '',
	setAttributes = () => null,
}) => (
	<RichText
		className="richtext"
		placeholder={placeholder}
			value={subTitle}
			onChange={newTitle => setAttributes({ subTitle: newTitle })}
	/>
);

SubTitle.defaultProps = {
	placeholder: 'Insert section subtitle',
};

export default SubTitle;
export {
	config,
};
