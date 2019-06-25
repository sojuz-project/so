const { MediaUpload, MediaUploadCheck } = wp.editor;

const config = {
  attrs: {
    thumbImage: {
      type: 'object'
    }
  },
  getAttrs: ({
    setAttributes,
    attributes: { thumbImage } = {},
  } = {}) => ({ thumbImage, setAttributes }),
};

const ThumbImage = ({
  thumbImage,
  setAttributes
}) => (
    <div>
      ThumbImage
      {thumbImage && (
        <img src={thumbImage.url} />
      )}
      <MediaUploadCheck>
					<MediaUpload
						allowedTypes={['image']}
						onSelect={image => setAttributes({ thumbImage: image}) }
						render={function({ open }) {
							return (
								<button className="default-button" onClick={open}>
                  {thumbImage ? 'Change image' : 'Choose image'}
								</button>
							);
						}}
					/>
				</MediaUploadCheck>  
    </div>
  )
export default ThumbImage;
export {
  config,
};
