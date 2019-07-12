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
			<div>
			
				<div className="list-group">
					{richList.length > 0 && richList.map((el = {}) => (
						<div className="list-element" key={el.id}>
								<div className="block-group title">
									<div className="extend-pickers-group">
										<div className="group-title">Title</div>
									</div>
									<RichText
										className="text"
										placeholder="Insert list title"
										value={el.content}
										onChange={content => updateSlide(el.id, { content })}
									/>
								</div>
								{withDescription && (
									<div className="block-group excerpt">
										<div className="extend-pickers-group">
											<div className="group-title">Excerpt</div>
										</div>
										<RichText
											className="text"
											placeholder="Insert list description"
											value={el.description}
											onChange={description => updateSlide(el.id, { description })}
										/>
									</div>
								)}
								<div 
									className={`block-group thumb ${el.image ? '' : 'image-empty'}`}
								>
									<div className="extend-pickers-group">
										<div className="group-title">Thumbnail</div>
									</div>
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
								<div className="remove">
									<button className="default-button button-remove" onClick={removeSlide(el.id)}>
										[X] Remove element
									</button>
								</div>
								
						</div>
					))}
				</div>
				<button className="default-button button-add" onClick={newSlide}>
					Add element +
				</button>
			</div>
		);
	}
}

export default RichList;
export {
	config,
};
