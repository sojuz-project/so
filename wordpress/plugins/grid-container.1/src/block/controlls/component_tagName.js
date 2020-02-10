const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n; 
export default (props) => {
  const el = props.gridLayout[props.selectedElement.componentI];
  return el && props.gridMode && props.selectedElement.component.blockName === "coreheading"
    ? <div className="components-base-control">
    <label className="components-base-control__label">Heading</label>
    <ButtonGroup>
        {['h1', 'h2', 'h3', 'h4', 'h5'].map((item, i) => {
          return <Button onClick={() => props.onChangeTagname(item)}
            isDefault={el.tagName !== item}
            isPrimary={el.tagName === item} >{item}</Button>
         })}
    </ButtonGroup>
  </div> : null
}
