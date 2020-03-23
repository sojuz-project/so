/* eslint-disable react/react-in-jsx-scope */
const { addFilter } = wp.hooks;
const { __ } = wp.i18n;
const { InspectorControls, PlainText } = wp.editor;
const { PanelBody } = wp.components;
const filterCoreImage = (settings) => {
  if (settings.name !== 'core/image') {
    return settings;
  }

  const newSettings = {
    ...settings,
    attributes: {
      ...settings.attributes,
      imageSourceMap: {
        type: 'string',
        default: '',
      },
      figureMinHeight: {
        type: 'string',
        default: '',
      },
    },
    edit(props) {
      const {
        attributes: { imageSourceMap, figureMinHeight },
        setAttributes,
      } = props;
      // imageFromMeta
      // useEffect(() => {
      //   props.setAttributes({ frontUrl: props.attributes.url });
      //   props.setAttributes({ frontText: props.attributes.text });
      //   props.setAttributes({ hyperLink: props.attributes.linkTarget });
      // }, [props.attributes.linkTarget, props.attributes.url, props.attributes.text, props]);

      return (
        <div>
          <InspectorControls>
            <PanelBody title={__('Sojuz extras')} initialOpen={true}>
              <label>Map meta source url</label>
              <PlainText
                className="plain-text"
                value={imageSourceMap}
                onChange={(imageSourceMap) => setAttributes({ imageSourceMap })}
              />
              <label>Figure min height</label>
              <PlainText
                className="plain-text"
                value={figureMinHeight}
                onChange={(figureMinHeight) => setAttributes({ figureMinHeight })}
              />
            </PanelBody>
          </InspectorControls>
          <settings.edit {...props} />
        </div>
      );
    },
  };
  return newSettings;
};
addFilter(
  'blocks.registerBlockType', // hook name, very important!
  'example/filter-core-image', // your name, very arbitrary!
  filterCoreImage // function to run
);
