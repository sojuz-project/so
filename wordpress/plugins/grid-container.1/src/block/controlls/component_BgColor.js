const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  const el = props.gridLayout[props.selectedElement.componentI]
  return (el &&
    props.selectedElement.component.blockName === "coreparagraph"
    || props.selectedElement.component.blockName === "coreheading"
    || props.selectedElement.component.blockName === "componentwrapper"
    || props.selectedElement.component.blockName === "customimage") && (
      <div className="components-base-control">
        <label className="components-base-control__label">Background color</label>
        <ButtonGroup>
          {[
            { name: 'primary', color: '#0073a8', },
            { name: 'secondary', color: '#005075' },
            { name: 'dark-gray', color: '#111111' },
            { name: 'light-gray', color: '#767676' },
            { name: 'white', color: '#ffffff' },
          ].map((item) => {
            return <Button onClick={() => props.onChangeBgColor(item.name)}
              style={{
                backgroundColor: item.color,
              }}
              isDefault={el.backgroundColor !== item.name}
              isPrimary={el.backgroundColor === item.name} >

            </Button>
          })}
        </ButtonGroup>
      </div>
    );
}
