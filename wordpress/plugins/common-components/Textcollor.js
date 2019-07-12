import Background from "./Background";

const config = {
  attrs: {
    textColor: {
      type: 'string'
    }
  },
  getAttrs: ({
    setAttributes,
    attributes: { textColor } = {},
  } = {}) => ({ textColor, setAttributes }),
};


const Textcolor = ({
  textColor,
  setAttributes
}) => (
  <Background
    backgroundColor={textColor}
    indicatorText="Text color: "
    setAttributes={({ backgroundColor: c }) => setAttributes({ textColor: c })}
  />
)
export default Textcolor;
export {
  config,
};
