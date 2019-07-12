import Background from "./Background";

const config = {
  attrs: {
    excerptColor: {
      type: 'string'
    }
  },
  getAttrs: ({
    setAttributes,
    attributes: { excerptColor } = {},
  } = {}) => ({ excerptColor, setAttributes }),
};


const Excerptcolor = ({
  excerptColor,
  setAttributes
}) => (
  <Background
    backgroundColor={excerptColor}
    indicatorText="excerpt color: "
    setAttributes={({ backgroundColor: c }) => setAttributes({ excerptColor: c })}
  />
)
export default Excerptcolor;
export {
  config,
};
