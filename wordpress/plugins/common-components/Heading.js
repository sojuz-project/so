import React from 'react';
const { RichText } = wp.editor;
export const coreheading = ({
	updateAttrs,
	technicalComponent:{
		tagName
	} = {},
	content: {
		innerHTML,
		...restContent
	}
}) => (
	<React.Fragment>
		<div className="overlay"></div>
		<RichText
			multiline={false}
			tagName={tagName}
			value={innerHTML}
				onChange={innerHTML => updateAttrs({ ...restContent, innerHTML })}
		/>
	</React.Fragment>
);
