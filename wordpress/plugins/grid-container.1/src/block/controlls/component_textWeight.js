const { SelectControl } = wp.components;
const { __ } = wp.i18n; 
export default (props) => {
  return (props.gridMode 
    && props.selectedElement.component.blockName === "coreparagraph"
    || props.selectedElement.component.blockName === "coreheading"
    || props.selectedElement.component.blockName === "componentwrapper") && <SelectControl
    label="Font weight"
    required='yes'
    value={(props.gridLayout[props.selectedElement.componentI] || {}).weight || null}
    options={[
      { label: 'Light', value: '300' },
      { label: 'Regular', value: '400' },
      { label: 'Semi bold', value: '600' },
      { label: 'Bold', value: '700' },
      { label: 'Extra bold', value: '800' },
    ]}
    onChange={props.onChangeFontWeight}
  />
}
