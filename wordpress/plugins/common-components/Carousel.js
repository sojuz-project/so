import React from 'react';
import NukaCarousel from 'nuka-carousel';

const { RichText, MediaUpload, MediaUploadCheck } = wp.editor;

const updateGalleryById = (richList = [], id, toUpdate) => richList.map(el => el.id === id ? ({ ...el, ...toUpdate }) : el);

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

class Carousel extends React.Component {
	state = {
		slideIndex: 0,
	};

	goToSlide = slideIndex => this.setState({ slideIndex });

	newSlide = () => {
		const { richList, setAttributes } = this.props;

		setAttributes({ richList: [...richList, { id: richList.length }] });
		this.goToSlide(richList.length);
	}

	removeSlide = slideId => () => this.props.setAttributes({ richList: this.props.richList.filter(({ id }) => id !== slideId) });

	updateSlide = (slideId, updateObj) => this.props.setAttributes({ richList: updateGalleryById(this.props.richList, slideId, updateObj) });

	render() {
		const { richList = [], withDescription = true } = this.props;
		const { goToSlide, newSlide, removeSlide, updateSlide } = this;

		return (
			<div>
				<button className="default-button" onClick={newSlide}>
					Add slide +
				</button>

				{richList.length > 0 && (
					<NukaCarousel
						slideIndex={this.state.slideIndex}
						afterSlide={goToSlide}
						renderCenterLeftControls={() => null}
						renderCenterRightControls={() => null}
					>
						{richList.map((el = {}) => (
							<React.Fragment key={el.id}>
								

								<div className="slide-element">
									<div>
										<RichText
											isolate={true}
											className="text"
											placeholder="Insert slide title"
											value={el.content}
											onChange={content => updateSlide(el.id, { content })}
										/>

										{withDescription && (
											<RichText
												isolate={true}
												className="text"
												placeholder="Insert slide description"
												value={el.description}
												onChange={description => updateSlide(el.id, { description })}
											/>
										)}
										<button className="default-button button-red" onClick={removeSlide(el.id)}>
											Remove current slide
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
					</NukaCarousel>
				)}
			</div>
		);
	}
}

export default Carousel;
export {
	config,
};
