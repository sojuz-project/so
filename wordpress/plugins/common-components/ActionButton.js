const { RichText } = wp.editor;

const config = {
	attrs: {
		actionButtonLabel: {
			type: 'string',
		},
		actionButtonTarget: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { actionButtonLabel, actionButtonTarget } = {},
	} = {}) => ({ actionButtonLabel, actionButtonTarget, setAttributes }),
};

const ActionButton = ({
	actionButtonLabel = '',
	actionButtonTarget = '',
	placeholder = 'Insert action button text',
	setAttributes = () => null,
}) => (
	<div className="block-group">
		<h5>Call to action</h5>
		<RichText
			className="smalltext"
			placeholder={placeholder}
			value={actionButtonLabel}
			onChange={newContent => setAttributes({ actionButtonLabel: newContent })}
		/>
		<RichText
			className="smalltext"
			placeholder={placeholder}
			value={actionButtonTarget}
			onChange={newContent => setAttributes({ actionButtonTarget: newContent })}
		/>
	</div>
);

export default ActionButton;
export {
	config,
};
