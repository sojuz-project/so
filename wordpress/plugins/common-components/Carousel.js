import React from 'react';
import NukaCarousel from 'nuka-carousel';

const { RichText, MediaUpload, MediaUploadCheck } = wp.editor;

const updateGalleryById = (gallery = [], id, toUpdate) => gallery.map(el => el.id === id ? ({ ...el, ...toUpdate }) : el);

const config = {
	attrs: {
		gallery: {
			type: 'array',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { gallery = [] } = {},
	} = {}) => ({ gallery, setAttributes }),
};

class Carousel extends React.Component {
	state = {
		slideIndex: 0,
	};

	goToSlide = slideIndex => this.setState({ slideIndex });

	newSlide = () => {
		const { gallery, setAttributes } = this.props;

		setAttributes({ gallery: [...gallery, { id: gallery.length }] });
		this.goToSlide(gallery.length);
	}

	removeSlide = slideId => () => this.props.setAttributes({ gallery: this.props.gallery.filter(({ id }) => id !== slideId) });

	updateSlide = (slideId, updateObj) => this.props.setAttributes({ gallery: updateGalleryById(this.props.gallery, slideId, updateObj) });

	render() {
		const { gallery = [], withDescription = true } = this.props;
		const { goToSlide, newSlide, removeSlide, updateSlide } = this;

		return (
			<div className="block-group slider">
				<button className="default-button" onClick={newSlide}>
					Add slide +
				</button>

				{gallery.length > 0 && (
					<NukaCarousel
						slideIndex={this.state.slideIndex}
						afterSlide={goToSlide}
						renderCenterLeftControls={() => null}
						renderCenterRightControls={() => null}
					>
						{gallery.map((el = {}) => (
							<React.Fragment key={el.id}>
								

								<div className="slide-element">
									<div>
										<RichText
											className="smalltext"
											placeholder="Insert slide title"
											value={el.content}
											onChange={content => updateSlide(el.id, { content })}
										/>

										{withDescription && (
											<RichText
												className="smalltext"
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
