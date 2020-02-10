import React from 'react';

export const customwrapper = ({
	updateAttrs,
	technicalComponent,
	content: {
		...restContent
	}
}) => (
		<React.Fragment>
			<div className="overlay">
				<div className="wrapper-badge">
					<div class="wrapper-middle">	<span className="dashicons dashicons-paperclip"></span>
						{technicalComponent.reasignTo}</div>
				
				</div>
			</div>
			
		</React.Fragment>
);
