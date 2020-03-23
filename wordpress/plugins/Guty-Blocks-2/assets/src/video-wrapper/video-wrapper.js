/* eslint-disable react/display-name */
/* eslint-disable react/jsx-key */
/* eslint-disable react/react-in-jsx-scope */
import './video-wrapper.view.scss';
import './video-wrapper.editor.scss';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const {
  InnerBlocks,
  BlockControls,
  InspectorControls,
  PlainText,
  AlignmentToolbar,
  ColorPalette,
  MediaUpload,
  MediaUploadCheck,
} = wp.editor;
const { DropdownMenu, Toolbar, PanelBody, ToggleControl, ButtonGroup, Button } = wp.components;
const ALLOWED_MEDIA_TYPES = ['video'];
registerBlockType('sojuz/video-wrapper', {
  title: 'Video wrapper',
  icon: 'format-video',
  category: 'layout',
  description: 'Native SOJUZ project block to display video as background into wrapper',
  supports: {
    align: ['full', 'wide'],
  },

  attributes: {
    align: {
      type: 'string',
      default: '',
    },
    videoSrc: {
      type: 'string',
      default: '',
    },
    backgroundColor: {
      type: 'string',
      default: 'has-default',
    },
    backgroundOpacity: {
      type: 'string',
      default: '1',
    },
    mediaId: {
      type: 'string',
      default: null,
    },
  },

  edit: (props) => {
    const {
      attributes: { align, backgroundColor, backgroundOpacity, videoSrc, mediaId },
      className,
      setAttributes,
    } = props;

    const colors = [
      { name: 'Primary', slug: 'has-primary', color: '#0073a8' },
      { name: 'Secondary', slug: 'has-secondary', color: '#005075' },
      { name: 'Dark gray', slug: 'has-dark-gray', color: '#111111' },
      { name: 'Light gray', slug: 'has-light-gray', color: '#767676' },
      { name: 'White', slug: 'has-white', color: '#ffffff' },
      { name: 'Default', slug: 'has-default', color: '#ffffff' },
    ];

    return (
      <div
        className={className}
        style={{
          backgroundColor: colors.find((el) => {
            return el.slug == backgroundColor;
          }).color,
        }}>
        <InspectorControls>
          <PanelBody title={__('Video properties')} initialOpen={true}>
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) => console.log('selected ' + media.length)}
                allowedTypes={ALLOWED_MEDIA_TYPES}
                value={mediaId}
                render={({ open }) => <Button onClick={open}>Open Media Library</Button>}
              />
            </MediaUploadCheck>
            <label>Video source</label>
            <PlainText className="plain-text" value={videoSrc} onChange={(videoSrc) => setAttributes({ videoSrc })} />
          </PanelBody>

          <PanelBody title={__('Appearance')} initialOpen={false}>
            <label className="components-base-control__label">Background color</label>
            <div className="components-base-control">
              <ButtonGroup>
                {colors.map((item, i) => {
                  return (
                    <Button
                      onClick={() => setAttributes({ backgroundColor: item.slug })}
                      isDefault={backgroundColor !== item.slug}
                      isPrimary={backgroundColor === item.slug}
                      style={{ backgroundColor: item.color }}>
                      &nbsp; &nbsp;
                    </Button>
                  );
                })}
              </ButtonGroup>
            </div>
            <label>Background opacity</label>
            <PlainText
              className="plain-text"
              value={backgroundOpacity}
              onChange={(backgroundOpacity) => setAttributes({ backgroundOpacity })}
            />
          </PanelBody>
        </InspectorControls>
        <InnerBlocks />
      </div>
    );
  },
  save: (props) => {
    return (
      <div>
        <InnerBlocks.Content />
      </div>
    );
  },
});
