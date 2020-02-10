import React from 'react';

const {  MediaUpload, MediaUploadCheck } = wp.editor;

const config = {
	attrs: {
		className: 'sadsadsad',
		overlayColor: '#ffffff',
		overlayOpacity: 0.5,
		fitTo:'content',
		
	},
};

export const customimage = ({
	updateAttrs,
	gridLayoutObject: {
		layout,
		overlayColor,
		overlayOpacity,
	},
	attrs,
}) => (
	<React.Fragment>
		{
				attrs && (
					<figure 
						style={{ height: `${layout.h/1.35}em`}}
						className="img-wrapper">
						<img src={attrs.url} alt={attrs.alt} />
						<div 
							style={{ background: overlayColor, opacity: overlayOpacity}}
							className="overlay"></div>
					</figure>
			)
		}
		<MediaUploadCheck>
			<MediaUpload
				allowedTypes={['image']}
				onSelect={attrs => updateAttrs(attrs)}
				render={function ({ open }) {
					return (
						<button className="components-button is-button is-default is-small img-button" onClick={open}>
							{attrs ? 'Change image' : 'Choose image'}
						</button>
					);
				}}
			/>
		</MediaUploadCheck>
	</React.Fragment>
);
