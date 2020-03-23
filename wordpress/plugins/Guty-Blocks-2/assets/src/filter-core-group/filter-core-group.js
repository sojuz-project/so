const { addFilter } = wp.hooks;
const { __ } = wp.i18n;
const { InspectorControls, PlainText } = wp.editor;
const { PanelBody } = wp.components;
const filterCoreGroup = (settings) => {
  if (settings.name !== 'core/group') {
    return settings;
  }

  const newSettings = {
    ...settings,
    attributes: {
      ...settings.attributes,
      url: {
        type: 'string',
        default: '',
      },
    },
    edit(props) {
      const {
        attributes: { url },
        setAttributes,
      } = props;

      return (
        <div>
          <InspectorControls>
            <PanelBody title={__('Sojuz extras')} initialOpen={true}>
              <label>Group url</label>
              <PlainText className="plain-text" value={url} onChange={(url) => setAttributes({ url })} />
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
  'example/filter-core-group', // your name, very arbitrary!
  filterCoreGroup // function to run
);
