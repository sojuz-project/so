import { ColorPicker, Dropdown, Tooltip } from '@wordpress/components';

const config = {
	attrs: {
		backgroundColor: {
			type: 'string',
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { backgroundColor } = {},
	} = {}) => ({ backgroundColor, setAttributes }),
};

const Background = ({
	backgroundColor = '#fff',
  setAttributes = () => null,
  indicatorText = 'BG: ',
}) => (
  <div>
    <Dropdown
      renderToggle={({ isOpen, onToggle }) => (
        <Tooltip text={indicatorText}>
          <button
            type="button"
            aria-expanded={ isOpen }
            className="color-picker-pin"
            onClick={ onToggle }
          >
            <span class="innerText">{indicatorText}</span>
            <span
              class="color-marker"
              style={{ backgroundColor }}
            >{backgroundColor}</span>
          </button>
        </Tooltip>
      ) }
      renderContent={() => (
        <ColorPicker
          color={backgroundColor}
          onChangeComplete={({ hex }) => setAttributes({ backgroundColor: hex })}
          disableAlpha
        />
      )}
    />
  </div>
);

export const BackgroundCustom = (key, defaultColor = '') => ({
  config: {
    attrs: {
      [key]: {
        type: 'string',
      },
    },
    getAttrs: ({
      setAttributes,
      attributes = {},
    } = {}) => ({ [key]: attributes[key] || defaultColor, setAttributes }),
  },
  Comp: (props) => (
    <Background
      backgroundColor={props[key]}
      indicatorText={props.indicatorText}
      setAttributes={({ backgroundColor: c }) => props.setAttributes({ [key]: c })}
    />
  ),
})

export default Background;
export {
	config,
};
