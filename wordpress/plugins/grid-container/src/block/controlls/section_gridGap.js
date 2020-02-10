const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n; 
export default ({
  gapValue,
  onGridGap,

}) => {
  return (
    <div className="components-base-control">
      <label className="components-base-control__label">Grid gap (%)</label>
      <ButtonGroup>
        {[...Array(6)].map((_, i) => {
          return <Button onClick={() => onGridGap(i)}
            isDefault={gapValue !== i}
            isPrimary={gapValue === i} >{i}</Button>
          })}
      </ButtonGroup>
    </div>
  );
}
