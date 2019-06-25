import { ColorPicker, Button, Dropdown, Tooltip } from '@wordpress/components';

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
	backgroundColor = '#FFFFFF',
  setAttributes = () => null,
  indicatorText = 'Background color: ',
}) => {

  return (
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
              {indicatorText}
              <span 
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
}

export default Background;
export {
	config,
};
