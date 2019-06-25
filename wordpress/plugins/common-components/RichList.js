import React from 'react';

const { RichText, MediaUpload, MediaUploadCheck } = wp.editor;

const updateRichListById = (richList = [], id, toUpdate) => richList.map(el => el.id === id ? ({ ...el, ...toUpdate }) : el);

const config = {
	attrs: {
		richList: {
			type: 'array',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { richList = [] } = {},
	} = {}) => ({ richList, setAttributes }),
};

class RichList extends React.Component {
	newSlide = () => {
		const id = Math.floor(Math.random() * 9999999999);  
		const { richList, setAttributes } = this.props;

		setAttributes({ richList: [...richList, { id }] });
	}

	removeSlide = slideId => () => this.props.setAttributes({ richList: this.props.richList.filter(({ id }) => id !== slideId) });

	updateSlide = (slideId, updateObj) => this.props.setAttributes({ richList: updateRichListById(this.props.richList, slideId, updateObj) });

	render() {
		const { richList = [], withDescription = true } = this.props;
		const { newSlide, removeSlide, updateSlide } = this;

		return (
			<div className="block-group slider">
				<button className="default-button" onClick={newSlide}>
					Add list element +
				</button>

				{richList.length > 0 && richList.map((el = {}) => (
					<React.Fragment key={el.id}>
						<div className="rlist-element">
							<div>
								<RichText
									className="smalltext"
									placeholder="Insert list title"
									value={el.content}
									onChange={content => updateSlide(el.id, { content })}
								/>

								{withDescription && (
									<RichText
										className="smalltext"
										placeholder="Insert list description"
										value={el.description}
										onChange={description => updateSlide(el.id, { description })}
									/>
								)}
								<button className="default-button button-red" onClick={removeSlide(el.id)}>
									Remove current list element
								</button>
							</div>

							<div className={`slide-image ${el.image ? '' : 'image-empty'}`}>
								{el.image && (
									<img src={el.image.url} alt={el.alt} />
								)}

								<MediaUploadCheck>
									<MediaUpload
										allowedTypes={['image']}
										onSelect={image => updateSlide(el.id, { image })}
										render={function({ open }) {
											return (
												<button className="default-button" onClick={open}>
													{el.image ? 'Change image' : 'Choose image'}
												</button>
											);
										}}
									/>
								</MediaUploadCheck>
							</div>
						</div>
					</React.Fragment>
				))}
			</div>
		);
	}
}

export default RichList;
export {
	config,
};
