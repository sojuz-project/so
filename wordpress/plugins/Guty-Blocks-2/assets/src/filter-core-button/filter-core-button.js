import { useEffect } from 'react'

const { addFilter } = wp.hooks;

const filterCoreButton = (settings) => {
  if (settings.name !== 'core/button') {
    return settings;
  }
  const newSettings = {
    ...settings,
    attributes: {
      ...settings.attributes,
      frontUrl: {
        type: 'string',
        default: '',
      },
      frontText: {
        type: 'string',
        default: '',
      },
      hyperLink: {
        type: 'bolean',
        default: true,
      },
    },
    edit(props) {
      useEffect(() => {
        props.setAttributes({ frontUrl: props.attributes.url }) 
        props.setAttributes({ frontText: props.attributes.text }) 
        props.setAttributes({ hyperLink: props.attributes.linkTarget}) 
      }, [props.attributes.linkTarget, props.attributes.url, props.attributes.text])

      return (
        <div>
          <settings.edit {...props} />
        </div>
      )
    }
  };
  return newSettings;
};
addFilter(
  'blocks.registerBlockType', // hook name, very important!
  'example/filter-core-button', // your name, very arbitrary!
  filterCoreButton // function to run
);
