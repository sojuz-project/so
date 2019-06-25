const { RichText } = wp.editor;

const config = {
	attrs: {
		title: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { title } = {},
	} = {}) => ({ title, setAttributes }),
};

const Title = ({
	title = '',
	placeholder = '',
	setAttributes = () => null,
}) => (
	<RichText
		className="richtext"
		placeholder={placeholder}
		value={title}
		onChange={newTitle => setAttributes({ title: newTitle })}
	/>
);

Title.defaultProps = {
	placeholder: 'Insert section title',
};

export default Title;
export {
	config,
};
