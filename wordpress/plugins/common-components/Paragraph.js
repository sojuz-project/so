import React from 'react';
const { RichText } = wp.editor;
export const coreparagraph = ({
	updateAttrs,
	content: {
		innerHTML,
		...restContent
	}
}) => (
	<React.Fragment>
		<div className="overlay"></div>
		<RichText
				multiline={true}
				tagName="p"
				value={innerHTML}
				onChange={innerHTML => updateAttrs({ ...restContent, innerHTML })}
		/>
	</React.Fragment>
);
