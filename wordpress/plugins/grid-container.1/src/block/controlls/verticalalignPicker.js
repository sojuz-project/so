const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  const el = props.gridLayout[props.selectedElement.componentI];
  return el 
  && props.gridMode && props.selectedElement.component.blockName === "coreparagraph"
  || props.selectedElement.component.blockName === "componentwrapper" 
    ? <div className="components-base-control">
      <label className="components-base-control__label">Vertical align</label>
      <ButtonGroup>
        {[
          { label: __('Top'), value: 'flex-start' },
          { label: __('Middle'), value: 'center' },
          { label: __('Bottom'), value: 'flex-end' },
        ].map((item) => {
          return <Button onClick={() => props.onChangeVerticalAlign(item.value)}
            isDefault={el.verticalAlign !== item.value}
            isPrimary={el.verticalAlign === item.value} >{item.label}</Button>
        })}
      </ButtonGroup>
    </div>  
  : null
}
