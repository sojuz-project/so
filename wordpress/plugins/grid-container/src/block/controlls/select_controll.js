const { SelectControl } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  return ( <SelectControl
    label={props.label}
    required='yes'
    value={props.value}
    options={props.data}
    onChange={value => props.onUpdate(value)}
  />
  )}