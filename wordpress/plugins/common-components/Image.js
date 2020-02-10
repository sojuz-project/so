import React from 'react';

const {  MediaUpload, MediaUploadCheck } = wp.editor;

export const customimage = ({
	updateAttrs,
	content: {
		alt, url, blockName,
	}
}) => (
	<React.Fragment>
		{
				url && (
					<div
						// style={{ maxHeight: `${layout.h/1.25}em`}}
						className="img-wrapper">
						<img src={url} alt={alt} />
						<div 
							// style={{ background: overlayColor, opacity: overlayOpacity}}
							className="overlay"></div>
					</div>
			)
		}
		<MediaUploadCheck>
			<MediaUpload
				allowedTypes={['image']}
				onSelect={attrs => updateAttrs({ ...attrs, blockName })}
				render={function ({ open }) {
					return (
						<button className="components-button is-button is-default is-small img-button" onClick={open}>
							{url ? 'Change image' : 'Choose image'}
						</button>
					);
				}}
			/>
		</MediaUploadCheck>
	</React.Fragment>
);
