/**
 * External Dependencies
 */
// import classnames from 'classnames';

/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment } = wp.element;
const { InspectorAdvancedControls } = wp.editor;
const { createHigherOrderComponent } = wp.compose;
const { ToggleControl } = wp.components;

//restrict to specific block names
const allowedBlocks = ['core/paragraph', 'core/heading'];

/**
 * Add custom attribute for mobile visibility.
 *
 * @param {Object} settings Settings for the block.
 *
 * @return {Object} settings Modified settings.
 */
function addAttributes(settings) {

  //check if object exists for old Gutenberg version compatibility
  //add allowedBlocks restriction
  if (typeof settings.attributes !== 'undefined' && allowedBlocks.includes(settings.name)) {

    settings.attributes = Object.assign(settings.attributes, {
      visibleOnMobile: {
        type: 'boolean',
        default: true,
      }
    });

  }

  return settings;
}

/**
 * Add mobile visibility controls on Advanced Block Panel.
 *
 * @param {function} BlockEdit Block edit component.
 *
 * @return {function} BlockEdit Modified block edit component.
 */
const withAdvancedControls = createHigherOrderComponent((BlockEdit) => {
  return (props) => {

    const {
      name,
      attributes,
      setAttributes,
      isSelected,
    } = props;

    const {
      visibleOnMobile,
    } = attributes;


    return (
      <Fragment>
        <BlockEdit {...props} />
        {/* //add allowedBlocks restriction */}
        {isSelected && allowedBlocks.includes(name) &&
          <InspectorAdvancedControls>
            <ToggleControl
              label={__('Mobile Devices Visibity')}
              checked={!!visibleOnMobile}
              onChange={() => setAttributes({ visibleOnMobile: !visibleOnMobile })}
              help={!!visibleOnMobile ? __('Showing on mobile devices.') : __('Hidden on mobile devices.')}
            />
          </InspectorAdvancedControls>
        }

      </Fragment>
    );
  };
}, 'withAdvancedControls');

/**
 * Add custom element class in save element.
 *
 * @param {Object} extraProps     Block element.
 * @param {Object} blockType      Blocks object.
 * @param {Object} attributes     Blocks attributes.
 *
 * @return {Object} extraProps Modified block element.
 */
function applyExtraClass(extraProps, blockType, attributes) {

  const { visibleOnMobile } = attributes;

  //check if attribute exists for old Gutenberg version compatibility
  //add class only when visibleOnMobile = false
  //add allowedBlocks restriction
  if (typeof visibleOnMobile !== 'undefined' && !visibleOnMobile && allowedBlocks.includes(blockType.name)) {
    extraProps.className = classnames(extraProps.className, 'mobile-hidden');
  }

  return extraProps;
}

//add filters

addFilter(
  'blocks.registerBlockType',
  'editorskit/custom-attributes',
  addAttributes
);

addFilter(
  'editor.BlockEdit',
  'editorskit/custom-advanced-control',
  withAdvancedControls
);

addFilter(
  'blocks.getSaveContent.extraProps',
  'editorskit/applyExtraClass',
  applyExtraClass
);