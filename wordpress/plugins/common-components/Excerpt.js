const { RichText } = wp.editor;

const config = {
	attrs: {
		excerpt: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { excerpt } = {},
	} = {}) => ({ excerpt, setAttributes }),
};

const Excerpt = ({
	excerpt = '',
	placeholder = '',
	setAttributes = () => null,
}) => (
	<RichText
		className="smalltext"
		placeholder={placeholder}
		value={excerpt}
		onChange={newExcerpt => setAttributes({ excerpt: newExcerpt })}
	/>
);

Excerpt.defaultProps = {
	placeholder: 'Insert exceprt text',
};

// export function test(key) {
// 	return {
// 		config: {
// 			attrs: {
// 				[key]: {
// 					type: 'string',
// 				},
// 			},
// 			getAttrs: ({
// 				setAttributes,
// 				attributes = {},
// 			} = {}) => ({ [key]: attributes.key, setAttributes }),
// 		},
// 		Comp: ({
// 			placeholder = '',
// 			setAttributes = () => null,
// 			...rest,
// 		}) => {
// 			const value = rest[key] || '';

// 			return (
// 					<RichText
// 						className="smalltext"
// 						placeholder={placeholder}
// 						value={value}
// 						onChange={newExcerpt => setAttributes({ [key]: newExcerpt })}
// 					/>
// 				);
// 			}
// 	}
// }

export default Excerpt;
export {
	config,
};
