const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
   return (
      <div className="components-base-control">
        <label className="components-base-control__label">{props.label}</label>
        <ButtonGroup>
        {props.data.map((item, i) => {
          return <Button onClick={() => props.onUpdate(item.value)}
          isDefault={props.value !== item.value}
          isPrimary={props.value === item.value} 
          style={
            {backgroundColor: item.color}
          }
          ></Button>
          })}
        </ButtonGroup>
      </div>
    );
}
