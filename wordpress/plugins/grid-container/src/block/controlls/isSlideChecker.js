
const { ToggleControl } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  const el = props.isSlider ? props.isSlider : false;
  // el ? null : props.onIsSlider(false)
  return <ToggleControl label= "Display as slider"
    checked={ el }
    onChange={() => props.onIsSlider(!el)}
  /> 
}

