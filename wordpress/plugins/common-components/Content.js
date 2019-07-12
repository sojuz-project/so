const { RichText } = wp.editor;

const config = {
	attrs: {
		content: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { content } = {},
	} = {}) => ({ content, setAttributes }),
};

const Content = ({
	content = '',
	placeholder = '',
	setAttributes = () => null,
}) => (
	<RichText
		className="richtext smalltext"
		placeholder={placeholder}
		value={content}
			onChange={newContent => setAttributes({ content: newContent })}
	/>
);

Content.defaultProps = {
	placeholder: 'Insert content',
};

export default Content;
export {
	config,
};
