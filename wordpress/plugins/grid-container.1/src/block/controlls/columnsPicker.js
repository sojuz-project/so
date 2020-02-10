const { Dropdown, Button } = wp.components;
const { __ } = wp.i18n;

export default (props) => {
  // const el = props.gridTemplateColumns ? props.gridTemplateColumns : 1;
  // forced sed default as 1
  // props.gridTemplateColumns ? null : props.gridTemplateColumnsChange(1)
  return <div className="components-base-control">
    <Dropdown
      className="components-toolbar"
      contentClassName="my-popover-content-classname"
      position="bottom left"
      renderToggle={({ isOpen, onToggle }) => (
        <Button isDefault onClick={onToggle} aria-expanded={isOpen}>
          Columns {props.gridTemplateColumns}
        </Button>
      )}
      renderContent={() => (
        <div className="dropdown-list">
          {[
            { label: 'One column', value: 'repeat(1, 1fr)' },
            { label: 'Two columns', value: 'repeat(2, 1fr)' },
            { label: 'Three columns', value: 'repeat(3, 1fr)' },
            { label: 'Four columns', value: 'repeat(4, 1fr)' },
            { label: 'Five columns', value: 'repeat(5, 1fr)' },
            { label: 'Six columns', value: 'repeat(6, 1fr)' },
          ].map((item, i) =>  {
            return <Button onClick={() => props.gridTemplateColumnsChange(item.value)}
              isDefault={props.gridTemplateColumns !== item.value}
              isPrimary={props.gridTemplateColumns === item.value} >{item.label}</Button>
          })}
        </div>
      )}
    />
  </div> 
}

