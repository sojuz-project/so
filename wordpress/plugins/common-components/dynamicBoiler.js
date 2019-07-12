const config = {
  getAttrs: ({
    setAttributes,
    attributes: { customValue } = {},
  } = {}) => ({ customValue, setAttributes }),
};

const DynamicBoiler = ({
  customValue = '2',
  setAttributes = () => null,
}) => {
  return (
    <div>
      <select
        value={customValue}
        onChange={({ target }) => setAttributes({ customValue: target.value })}
      >
        <option value="1">One</option>
        <option value="2">Two</option>
      </select>
    </div>
  );
}

export default DynamicBoiler;
export {
  config,
};